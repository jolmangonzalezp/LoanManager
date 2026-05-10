export type CustomerStatus = 'active' | 'pending' | 'overdue' | 'closed' | 'paid' | 'defaulted';

export function formatCurrency(value: number | string | null | undefined): string {
    const num = Number(value)

    if (isNaN(num)) return '$0'

    return new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP',
        maximumFractionDigits: 0
    }).format(num)
}

export function formatDate(date: any):string {
    if (!date) return '—'
    return new Date(date).toLocaleDateString('es-CO', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    })
}

export function getStatusLabel(status:any): any {
    const labels = {
        active: 'Al día',
        pending: 'Pendiente',
        overdue: 'En mora',
        closed: 'Cerrado',
        paid: 'Pagado',
        defaulted: 'Mora'
    }
    return labels[status as CustomerStatus] || status}

export function getStatusColor(status:any):any {
    const colors = {
        active: 'success',
        pending: 'pending',
        overdue: 'overdue',
        closed: 'closed',
        paid: 'success',
        defaulted: 'overdue'
    }
    return colors[status as CustomerStatus] || 'active'}
