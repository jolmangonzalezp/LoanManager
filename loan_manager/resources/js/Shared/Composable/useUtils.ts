export type CustomerStatus = 'active' | 'pending' | 'overdue' | 'closed' | 'paid' | 'defaulted';

export function formatCurrency(value:any):string {
    if (value == null) return '$0'
    const num = Math.round(typeof value === 'number' ? value : Number(value))
    return '$' + num.toLocaleString('es-CO')
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
