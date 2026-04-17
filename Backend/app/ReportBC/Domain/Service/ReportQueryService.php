<?php

declare(strict_types=1);

namespace App\ReportBC\Domain\Service;

use App\LoanBC\Domain\Repository\CustomerNameProvider;
use App\LoanBC\Infrastructure\Persistence\Model\LoanModel;
use App\PaymentBC\Infrastructure\Persistence\Model\PaymentModel;
use App\ReportBC\Application\DTO\ActiveLoansReportDTO;
use App\ReportBC\Application\DTO\AuditEntryDTO;
use App\ReportBC\Application\DTO\AuditReportDTO;
use App\ReportBC\Application\DTO\CashFlowReportDTO;
use App\ReportBC\Application\DTO\DelinquencyReportDTO;
use App\ReportBC\Application\DTO\KpiReportDTO;
use App\ReportBC\Application\DTO\LoanDelinquencyDTO;
use App\ReportBC\Application\DTO\LoanProfitabilityDTO;
use App\ReportBC\Application\DTO\LoanSummaryDTO;
use App\ReportBC\Application\DTO\MonthlyCollectionReportDTO;
use App\ReportBC\Application\DTO\PaymentDetailDTO;
use App\ReportBC\Application\DTO\PaymentHistoryReportDTO;
use App\ReportBC\Application\DTO\PortfolioReportDTO;
use App\ReportBC\Application\DTO\ProfitabilityReportDTO;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

final class ReportQueryService
{
    public function __construct(
        private readonly CustomerNameProvider $customerNameProvider
    ) {}

    public function getPortfolioReport(): PortfolioReportDTO
    {
        $loans = LoanModel::all();

        $totalPrestado = $loans->sum('original_capital');
        $capitalPendiente = $loans->where('status', '!=', 'paid')->sum('remaining_debt');
        $interesesCobrados = $loans->sum('paid_interest');
        $interesesGenerados = $loans->sum(fn ($l) => $this->calculateExpectedInterest($l));
        $numeroActivos = $loans->where('status', 'active')->count();
        $totalClientes = $loans->pluck('customer_id')->unique()->count();

        $tasaPromedio = $loans->count() > 0
            ? $loans->avg('interest_rate')
            : 0;

        return new PortfolioReportDTO(
            totalPrestado: (int) $totalPrestado,
            capitalPendiente: (int) $capitalPendiente,
            interesesGenerados: (int) $interesesGenerados,
            interesesCobrados: (int) $interesesCobrados,
            numeroPrestamosActivos: $numeroActivos,
            tasaInteresPromedio: (float) $tasaPromedio,
            totalClientes: $totalClientes
        );
    }

    public function getCashFlowReport(string $fechaInicio, string $fechaFin): CashFlowReportDTO
    {
        $startDate = Carbon::parse($fechaInicio)->startOfDay();
        $endDate = Carbon::parse($fechaFin)->endOfDay();

        $payments = PaymentModel::whereBetween('payment_date', [$startDate, $endDate])
            ->where('status', 'applied')
            ->get();

        $loans = LoanModel::whereBetween('start_date', [$startDate, $endDate])->get();

        $ingresosPorPagos = $payments->sum('amount');
        $totalPagos = $payments->count();

        $egresosPorDesembolsos = $loans->sum('original_capital');
        $totalDesembolsos = $loans->count();

        return new CashFlowReportDTO(
            ingresosPorPagos: (int) $ingresosPorPagos,
            egresosPorDesembolsos: (int) $egresosPorDesembolsos,
            flujoNeto: (int) ($ingresosPorPagos - $egresosPorDesembolsos),
            fechaInicio: $fechaInicio,
            fechaFin: $fechaFin,
            totalPagos: $totalPagos,
            totalDesembolsos: $totalDesembolsos
        );
    }

    public function getProfitabilityReport(): ProfitabilityReportDTO
    {
        $loans = LoanModel::all();
        $loanNumbers = $loans->pluck('loan_number', 'id')->toArray();

        $customerIds = $loans->pluck('customer_id')->unique()->toArray();
        $customerNames = $this->customerNameProvider->getNamesMap($customerIds);

        $totalIntereses = $loans->sum('paid_interest');
        $totalCapital = $loans->sum('original_capital');

        $ratioInteresesCapital = $totalCapital > 0 ? $totalIntereses / $totalCapital : 0;
        $roiGlobal = $totalCapital > 0 ? $totalIntereses / $totalCapital : 0;

        $roiPorPrestamo = [];
        foreach ($loans as $loan) {
            $diasActivo = (int) Carbon::parse($loan->start_date)->diffInDays(now());
            $roi = $diasActivo > 0 ? ($loan->paid_interest / $loan->original_capital) * (365 / $diasActivo) : 0;

            $roiPorPrestamo[] = (new LoanProfitabilityDTO(
                loanId: $loan->id,
                loanNumber: $loanNumbers[$loan->id] ?? '-',
                customerName: $customerNames[$loan->customer_id] ?? '-',
                capital: (int) $loan->original_capital,
                interesesCobrados: (int) $loan->paid_interest,
                diasActivo: $diasActivo,
                roi: (float) $roi
            ))->toArray();
        }

        return new ProfitabilityReportDTO(
            totalIntereses: (int) $totalIntereses,
            totalCapital: (int) $totalCapital,
            ratioInteresesCapital: (float) $ratioInteresesCapital,
            roiGlobal: (float) $roiGlobal,
            roiPorPrestamo: $roiPorPrestamo,
            totalPrestamos: $loans->count()
        );
    }

    public function getDelinquencyReport(): DelinquencyReportDTO
    {
        $now = Carbon::now();
        $loans = LoanModel::where('status', '!=', 'paid')->get();

        $loanNumbers = $loans->pluck('loan_number', 'id')->toArray();
        $customerIds = $loans->pluck('customer_id')->unique()->toArray();
        $customerNames = $this->customerNameProvider->getNamesMap($customerIds);

        $totalCartera = $loans->sum('remaining_debt');
        $prestamosEnMora = [];
        $clientesUnicos = [];

        foreach ($loans as $loan) {
            $nextPayment = Carbon::parse($loan->next_payment_date);
            if ($nextPayment->lt($now)) {
                $diasAtraso = (int) $nextPayment->diffInDays($now);

                $prestamosEnMora[] = (new LoanDelinquencyDTO(
                    loanId: $loan->id,
                    loanNumber: $loanNumbers[$loan->id] ?? '-',
                    customerName: $customerNames[$loan->customer_id] ?? '-',
                    saldoPendiente: (int) $loan->remaining_debt,
                    diasAtraso: $diasAtraso,
                    estado: 'mora'
                ))->toArray();

                $clientesUnicos[$loan->customer_id] = true;
            }
        }

        $montoEnMora = array_sum(array_column($prestamosEnMora, 'saldoPendiente'));
        $diasPromedio = count($prestamosEnMora) > 0
            ? (int) (array_sum(array_column($prestamosEnMora, 'diasAtraso')) / count($prestamosEnMora))
            : 0;
        $porcentajeCarteraVencida = $totalCartera > 0 ? ($montoEnMora / $totalCartera) * 100 : 0;

        return new DelinquencyReportDTO(
            clientesEnMora: count($clientesUnicos),
            montoEnMora: (int) $montoEnMora,
            diasPromedioAtraso: (int) $diasPromedio,
            porcentajeCarteraVencida: (float) $porcentajeCarteraVencida,
            prestamosEnMora: count($prestamosEnMora),
            detalleMora: $prestamosEnMora
        );
    }

    public function getMonthlyCollectionReport(?int $mes = null, ?int $anio = null): MonthlyCollectionReportDTO
    {
        $month = $mes ?? (int) now()->format('n');
        $year = $anio ?? (int) now()->format('Y');

        $loans = LoanModel::where('status', '!=', 'paid')->get();
        $payments = PaymentModel::whereMonth('payment_date', $month)
            ->whereYear('payment_date', $year)
            ->where('status', 'applied')
            ->get();

        $montoEsperado = $loans->count() * ($loans->avg('original_capital') * $loans->avg('interest_rate') / 100);
        $montoCobrado = $payments->sum('amount');

        $porcentajeCumplimiento = $montoEsperado > 0 ? ($montoCobrado / $montoEsperado) * 100 : 0;

        $numeroCuotasVencidas = $loans->filter(function ($loan) use ($month, $year) {
            $nextPayment = Carbon::parse($loan->next_payment_date);

            return $nextPayment->lt(now()) &&
                   $nextPayment->month === $month &&
                   $nextPayment->year === $year;
        })->count();

        return new MonthlyCollectionReportDTO(
            montoEsperado: (int) $montoEsperado,
            montoCobrado: (int) $montoCobrado,
            porcentajeCumplimiento: (float) $porcentajeCumplimiento,
            numeroPagos: $payments->count(),
            numeroCuotasVencidas: $numeroCuotasVencidas,
            mes: Carbon::create($year, $month)->format('F'),
            anio: (string) $year
        );
    }

    public function getKpiReport(): KpiReportDTO
    {
        $loans = LoanModel::all();
        $payments = PaymentModel::where('status', 'applied')->get();

        $totalPrestamos = $loans->count();
        $totalClientes = $loans->pluck('customer_id')->unique()->count();
        $prestamosCerrados = $loans->where('status', 'paid')->count();

        $prestamosEnMora = $loans->filter(function ($loan) {
            return Carbon::parse($loan->next_payment_date)->lt(now()) && $loan->status !== 'paid';
        })->count();

        $tasaMora = $totalPrestamos > 0 ? ($prestamosEnMora / $totalPrestamos) * 100 : 0;

        $interesesCobrados = $loans->sum('paid_interest');
        $interesesGenerados = $loans->sum(fn ($l) => $this->calculateExpectedInterest($l));
        $tasaRecuperacion = $interesesGenerados > 0 ? ($interesesCobrados / $interesesGenerados) * 100 : 0;

        $ticketPromedio = $payments->count() > 0 ? (int) ($payments->sum('amount') / $payments->count()) : 0;

        $duracionTotal = $loans->sum(function ($loan) {
            return Carbon::parse($loan->start_date)->diffInDays($loan->status === 'paid' ? $loan->updated_at : now());
        });
        $duracionPromedio = $totalPrestamos > 0 ? $duracionTotal / $totalPrestamos : 0;

        $clientesConMultiplePrestamo = $loans->groupBy('customer_id')->filter(fn ($g) => $g->count() > 1)->count();
        $porcentajeRecurrentes = $totalClientes > 0 ? ($clientesConMultiplePrestamo / $totalClientes) * 100 : 0;

        return new KpiReportDTO(
            tasaMora: (float) $tasaMora,
            tasaRecuperacion: (float) $tasaRecuperacion,
            ticketPromedio: $ticketPromedio,
            duracionPromedioPrestamo: (float) $duracionPromedio,
            porcentajeClientesRecurrentes: (float) $porcentajeRecurrentes,
            totalPrestamos: $totalPrestamos,
            totalClientes: $totalClientes,
            totalPrestamosCerrados: $prestamosCerrados
        );
    }

    public function getAuditReport(?string $entidad = null, ?string $fechaInicio = null, ?string $fechaFin = null, int $limit = 100): AuditReportDTO
    {
        try {
            if (! DB::getSchemaBuilder()->hasTable('audits')) {
                return new AuditReportDTO(
                    registros: [],
                    totalRegistros: 0
                );
            }
        } catch (\Exception $e) {
            return new AuditReportDTO(
                registros: [],
                totalRegistros: 0
            );
        }

        $query = DB::table('audits');

        if ($entidad) {
            $query->where('auditable_type', 'like', '%'.$entidad);
        }

        if ($fechaInicio) {
            $query->where('created_at', '>=', $fechaInicio);
        }

        if ($fechaFin) {
            $query->where('created_at', '<=', $fechaFin);
        }

        $audits = $query->orderBy('created_at', 'desc')->limit($limit)->get();

        $registros = [];
        foreach ($audits as $audit) {
            $oldValues = json_decode($audit->old_values, true) ?? [];
            $newValues = json_decode($audit->new_values, true) ?? [];

            $cambios = [];
            foreach ($newValues as $key => $value) {
                if (isset($oldValues[$key]) && $oldValues[$key] !== $value) {
                    $cambios[$key] = ['antes' => $oldValues[$key], 'despues' => $value];
                }
            }

            $entityName = class_basename($audit->auditable_type);

            $registros[] = (new AuditEntryDTO(
                id: (string) $audit->id,
                tipo: 'update',
                entidad: $entityName,
                entidadId: $audit->auditable_id,
                usuarioId: $audit->user_id,
                usuarioNombre: null,
                accion: $audit->action,
                datosAnteriores: $oldValues,
                datosNuevos: $newValues,
                fecha: $audit->created_at
            ))->toArray();
        }

        return new AuditReportDTO(
            registros: $registros,
            totalRegistros: count($registros)
        );
    }

    public function getActiveLoansReport(): ActiveLoansReportDTO
    {
        $loans = LoanModel::where('status', '!=', 'paid')->get();

        $loanNumbers = $loans->pluck('loan_number', 'id')->toArray();
        $customerIds = $loans->pluck('customer_id')->unique()->toArray();
        $customerNames = $this->customerNameProvider->getNamesMap($customerIds);

        $prestamos = [];
        $totalSaldo = 0;

        foreach ($loans as $loan) {
            $diasActivo = (int) Carbon::parse($loan->start_date)->diffInDays(now());

            $prestamos[] = (new LoanSummaryDTO(
                id: $loan->id,
                loanNumber: $loanNumbers[$loan->id] ?? '-',
                customerId: $loan->customer_id,
                customerName: $customerNames[$loan->customer_id] ?? '-',
                capitalOriginal: (int) $loan->original_capital,
                saldoPendiente: (int) $loan->remaining_debt,
                tasaInteres: (float) $loan->interest_rate,
                fechaDesembolso: Carbon::parse($loan->start_date)->format('Y-m-d'),
                proximoPago: Carbon::parse($loan->next_payment_date)->format('Y-m-d'),
                estado: $loan->status,
                diasActivo: $diasActivo
            ))->toArray();

            $totalSaldo += $loan->remaining_debt;
        }

        return new ActiveLoansReportDTO(
            prestamos: $prestamos,
            total: count($prestamos),
            totalSaldo: (int) $totalSaldo
        );
    }

    public function getPaymentHistoryReport(?string $loanId = null, ?string $customerId = null): PaymentHistoryReportDTO
    {
        $query = PaymentModel::query();

        if ($loanId) {
            $query->where('loan_id', $loanId);
        }

        $payments = $query->orderBy('payment_date', 'desc')->get();

        $loanIds = $payments->pluck('loan_id')->unique()->toArray();
        $loanNumbers = LoanModel::whereIn('id', $loanIds)->pluck('loan_number', 'id')->toArray();
        $customerIds = LoanModel::whereIn('id', $loanIds)->pluck('customer_id', 'id')->toArray();

        $uniqueCustomerIds = array_unique(array_values($customerIds));
        $customerNames = $this->customerNameProvider->getNamesMap($uniqueCustomerIds);

        if ($customerId) {
            $customerLoanIds = LoanModel::where('customer_id', $customerId)->pluck('id')->toArray();
            $payments = $payments->filter(fn ($p) => in_array($p->loan_id, $customerLoanIds));
        }

        $pagos = [];
        $montoTotal = 0;

        foreach ($payments as $payment) {
            $diasAtraso = null;
            $loanCustomerId = $customerIds[$payment->loan_id] ?? null;

            $pagos[] = (new PaymentDetailDTO(
                id: $payment->id,
                loanId: $payment->loan_id,
                loanNumber: $loanNumbers[$payment->loan_id] ?? '-',
                customerId: $loanCustomerId,
                customerName: $loanCustomerId ? ($customerNames[$loanCustomerId] ?? '-') : '-',
                monto: (int) $payment->amount,
                interesPagado: (int) $payment->interest_paid,
                capitalPagado: (int) $payment->capital_paid,
                fechaPago: Carbon::parse($payment->payment_date)->format('Y-m-d H:i:s'),
                estado: $payment->status,
                diasAtraso: $diasAtraso
            ))->toArray();

            $montoTotal += $payment->amount;
        }

        return new PaymentHistoryReportDTO(
            pagos: $pagos,
            total: count($pagos),
            montoTotal: (int) $montoTotal,
            loanId: $loanId,
            customerId: $customerId
        );
    }

    private function calculateExpectedInterest(LoanModel $loan): int
    {
        $months = Carbon::parse($loan->start_date)->diffInMonths(now());

        return (int) ($loan->original_capital * ($loan->interest_rate / 100) * max(1, $months));
    }
}
