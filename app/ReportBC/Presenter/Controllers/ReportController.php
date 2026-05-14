<?php

declare(strict_types=1);

namespace App\ReportBC\Presenter\Controllers;

use App\ReportBC\Domain\Service\ReportQueryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class ReportController
{
    public function __construct(
        private readonly ReportQueryService $reportService
    ) {}

    private function getUserContext(Request $request): array
    {
        $user = $request->user();
        return [
            $user?->getAuthIdentifier(),
            $user?->roles ? ($user->roles->pluck('slug')->contains('admin') ? 'admin' : 'agent') : null,
        ];
    }

    public function portfolio(Request $request): JsonResponse
    {
        [$userId, $role] = $this->getUserContext($request);
        $report = $this->reportService->getPortfolioReport($userId, $role);

        return response()->json($report->toArray());
    }

    public function cashFlow(Request $request): JsonResponse
    {
        [$userId, $role] = $this->getUserContext($request);
        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', now()->endOfMonth()->format('Y-m-d'));

        $report = $this->reportService->getCashFlowReport($fechaInicio, $fechaFin, $userId, $role);

        return response()->json($report->toArray());
    }

    public function profitability(Request $request): JsonResponse
    {
        [$userId, $role] = $this->getUserContext($request);
        $report = $this->reportService->getProfitabilityReport($userId, $role);

        return response()->json($report->toArray());
    }

    public function delinquency(Request $request): JsonResponse
    {
        [$userId, $role] = $this->getUserContext($request);
        $report = $this->reportService->getDelinquencyReport($userId, $role);

        return response()->json($report->toArray());
    }

    public function monthlyCollection(Request $request): JsonResponse
    {
        [$userId, $role] = $this->getUserContext($request);
        $mes = $request->get('mes') ? (int) $request->get('mes') : null;
        $anio = $request->get('anio') ? (int) $request->get('anio') : null;

        $report = $this->reportService->getMonthlyCollectionReport($mes, $anio, $userId, $role);

        return response()->json($report->toArray());
    }

    public function kpis(Request $request): JsonResponse
    {
        [$userId, $role] = $this->getUserContext($request);
        $report = $this->reportService->getKpiReport($userId, $role);

        return response()->json($report->toArray());
    }

    public function customerKpis(Request $request): JsonResponse
    {
        [$userId, $role] = $this->getUserContext($request);
        $report = $this->reportService->getCustomerKpiReport($userId, $role);

        return response()->json($report->toArray());
    }

    public function audit(Request $request): JsonResponse
    {
        [$userId, $role] = $this->getUserContext($request);
        $entidad = $request->get('entidad');
        $fechaInicio = $request->get('fecha_inicio');
        $fechaFin = $request->get('fecha_fin');
        $limit = (int) $request->get('limit', 100);

        $report = $this->reportService->getAuditReport($entidad, $fechaInicio, $fechaFin, $limit, $userId, $role);

        return response()->json($report->toArray());
    }

    public function activeLoans(Request $request): JsonResponse
    {
        [$userId, $role] = $this->getUserContext($request);
        $report = $this->reportService->getActiveLoansReport($userId, $role);

        return response()->json($report->toArray());
    }

    public function paymentHistory(Request $request): JsonResponse
    {
        [$userId, $role] = $this->getUserContext($request);
        $loanId = $request->get('loan_id');
        $customerId = $request->get('customer_id');

        $report = $this->reportService->getPaymentHistoryReport($loanId, $customerId, $userId, $role);

        return response()->json($report->toArray());
    }

    public function summary(Request $request): JsonResponse
    {
        [$userId, $role] = $this->getUserContext($request);

        $portfolio = $this->reportService->getPortfolioReport($userId, $role);
        $kpis = $this->reportService->getKpiReport($userId, $role);
        $collection = $this->reportService->getMonthlyCollectionReport(null, null, $userId, $role);
        $delinquency = $this->reportService->getDelinquencyReport($userId, $role);

        return response()->json([
            'portfolio' => $portfolio->toArray(),
            'kpis' => $kpis->toArray(),
            'collection' => $collection->toArray(),
            'delinquency' => [
                'clientes_en_mora' => $delinquency->clientesEnMora,
                'monto_en_mora' => $delinquency->montoEnMora,
                'porcentaje_cartera_vencida' => $delinquency->porcentajeCarteraVencida,
            ],
        ]);
    }
}
