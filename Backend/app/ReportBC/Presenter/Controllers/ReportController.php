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

    public function portfolio(): JsonResponse
    {
        $report = $this->reportService->getPortfolioReport();

        return response()->json($report->toArray());
    }

    public function cashFlow(Request $request): JsonResponse
    {
        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', now()->endOfMonth()->format('Y-m-d'));

        $report = $this->reportService->getCashFlowReport($fechaInicio, $fechaFin);

        return response()->json($report->toArray());
    }

    public function profitability(): JsonResponse
    {
        $report = $this->reportService->getProfitabilityReport();

        return response()->json($report->toArray());
    }

    public function delinquency(): JsonResponse
    {
        $report = $this->reportService->getDelinquencyReport();

        return response()->json($report->toArray());
    }

    public function monthlyCollection(Request $request): JsonResponse
    {
        $mes = $request->get('mes') ? (int) $request->get('mes') : null;
        $anio = $request->get('anio') ? (int) $request->get('anio') : null;

        $report = $this->reportService->getMonthlyCollectionReport($mes, $anio);

        return response()->json($report->toArray());
    }

    public function kpis(): JsonResponse
    {
        $report = $this->reportService->getKpiReport();

        return response()->json($report->toArray());
    }

    public function audit(Request $request): JsonResponse
    {
        $entidad = $request->get('entidad');
        $fechaInicio = $request->get('fecha_inicio');
        $fechaFin = $request->get('fecha_fin');
        $limit = (int) $request->get('limit', 100);

        $report = $this->reportService->getAuditReport($entidad, $fechaInicio, $fechaFin, $limit);

        return response()->json($report->toArray());
    }

    public function activeLoans(): JsonResponse
    {
        $report = $this->reportService->getActiveLoansReport();

        return response()->json($report->toArray());
    }

    public function paymentHistory(Request $request): JsonResponse
    {
        $loanId = $request->get('loan_id');
        $customerId = $request->get('customer_id');

        $report = $this->reportService->getPaymentHistoryReport($loanId, $customerId);

        return response()->json($report->toArray());
    }

    public function summary(): JsonResponse
    {
        $portfolio = $this->reportService->getPortfolioReport();
        $kpis = $this->reportService->getKpiReport();
        $collection = $this->reportService->getMonthlyCollectionReport();
        $delinquency = $this->reportService->getDelinquencyReport();

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
