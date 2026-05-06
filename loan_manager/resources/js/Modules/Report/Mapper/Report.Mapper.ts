import {
    CashFlowParams, CashFLowResponse, CashFLowResponseDTO, CustomerKPI, CustomerKPIDTO, ReportDeliquency,
    ReportDeliquencyDTO, ReportPortfolio, ReportPortfolioDTO, ReportProfitable,
    ReportProfitableDTO, ReportSummary, ReportSummaryDTO } from '@/Modules/Report';

import { useMask } from '@/Shared';
import { CashFlowParamsDTO } from '@/Modules/Report/types/reportDTO.Type.ts';

export const ReportMapper = {
    toCustomerKPI(dto: CustomerKPIDTO): CustomerKPI {
        return {
            totalCustomers: dto.total_customers,
            customersWithActiveLoans: dto.customers_with_active_loans,
            customersWithLoans: dto.customers_with_loans,
            customersWithoutLoans: dto.customers_without_loans,
            activeCustomers: dto.active_customers,
        }
    },
    toReportSummary(dto: ReportSummaryDTO): ReportSummary {
        return {
            portfolio: {
                totalPrestado: dto.portfolio.total_prestado,
                capitalPendiente: dto.portfolio.capital_pendiente,
                interesesGenerados: dto.portfolio.intereses_generados,
                interesesCobrados: dto.portfolio.intereses_cobrados,
                numeroPrestamosActivos: dto.portfolio.numero_prestamos_activos,
                tasaInteresPromedio: dto.portfolio.tasa_interes_promedio,
                totalClientes: dto.portfolio.total_clientes,
            },
            kpis: {
                tasaMora: dto.kpis.tasa_mora,
                tasaRecuperacion: dto.kpis.tasa_recuperacion,
                ticketPromedio: dto.kpis.ticket_promedio,
                duracionPromedioPrestamo: dto.kpis.duracion_promedio_prestamo,
                porcentajeClientesRecurrentes: dto.kpis.porcentaje_clientes_recurrentes,
                totalPrestamos: dto.kpis.total_prestamos,
                totalClientes: dto.kpis.total_clientes,
                totalPrestamosCerrados: dto.kpis.total_prestamos_cerrados,
            },
            collection: {
                anio: dto.collection.anio,
                mes: dto.collection.mes,
                montoCobrado: dto.collection.monto_cobrado,
                montoEsperado: dto.collection.monto_esperado,
                numeroCuotasVencidas: dto.collection.numero_cuotas_vencidas,
                numeroPagos: dto.collection.numero_pagos,
                porcentajeCumplimiento: dto.collection.porcentaje_cumplimiento,
            },
            delinquency: {
                clientesEnMora: dto.delinquency.clientes_en_mora,
                montoEnMora: dto.delinquency.monto_en_mora,
                porcentajeCarteraVencida: dto.delinquency.porcentaje_cartera_vencida
            }
        }
    },
    toPortfolio(dto: ReportPortfolioDTO ): ReportPortfolio{
        return {
            capitalPendiente: dto.capital_pendiente,
            interesesCobrados: dto.intereses_cobrados,
            interesesGenerados: dto.intereses_generados,
            numeroPrestamosActivos: dto.numero_prestamos_activos,
            tasaInteresPromedio: dto.tasa_interes_promedio,
            totalClientes: dto.total_clientes,
            totalPrestado: dto.total_prestado
        }
    },
    toProfitable(dto: ReportProfitableDTO): ReportProfitable {
        return {
            ratioInteresesCapital: dto.ratio_intereses_capital,
            roiGlobal: dto.roi_global,
            roiPorPrestamo: dto.roi_por_prestamo.map(loan => ({
                loanId: loan.loan_id,
                    loanNumber: loan.loan_number,
                    customerName: useMask().maskStart(loan.customer_name),
                    capital: loan.capital,
                    interesesCobrados: loan.intereses_cobrados,
                    diasActivo:  loan.dias_activo,
                    roi: loan.roi,
            })),
            totalCapital: dto.total_capital,
            totalIntereses: dto.total_intereses,
            totalPrestamos: dto.total_prestamos,
        }
    },
    toDeliquency(dto: ReportDeliquencyDTO): ReportDeliquency {
        return {
            clientesEnMora: dto.clientes_en_mora,
            detalleMora: dto.detalle_mora.map(loan => ({
                loanId: loan.loan_id,
                loanNumber: loan.loan_number,
                customerName: useMask().maskStart(loan.customer_name),
                saldoPendiente: loan.saldo_pendiente,
                diasAtrazo: loan.dias_atrazo,
            })),
            diasPromedioAtraso: dto.dias_promedio_atraso,
            montoEnMora: dto.monto_en_mora,
            porcentajeCarteraVencida: dto.porcentaje_cartera_vencida,
            prestamosEnMora: dto.prestamos_en_mora,
        }
    },
    toCashFLowParamsDTO(domain: CashFlowParams): CashFlowParamsDTO {
        return {
            fecha_inicio: domain.fechaInicio,
            fecha_fin: domain.fechaFin
        }
    },
    toCashFlowResponse(dto: CashFLowResponseDTO): CashFLowResponse {
        return {
            ingresos: dto.ingresos_por_pagos,
            egresos: dto.egresos_por_desembolsos,
            flujoNeto: dto.flujo_neto,
            totalDesembolsos: dto.total_desembolsos,
            totalPagos: dto.total_pagos,
            fechaFin: dto.fecha_fin,
            fechaInicio: dto.fecha_inicio,
        }
    }
}
