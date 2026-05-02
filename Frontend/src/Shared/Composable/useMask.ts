export const useMask = () => {
    const maskEnd = (value: any): string => {
        if (value.length < 1) return ''
        const visible = value.slice(-4)
        const hidden = '*'.repeat(Math.max(0, value.length - 4))
        return hidden + visible
    }

    const maskStart = (value: any): string => {
        if (value.length < 1) return ''
        const visibleLenght = Math.min(4, Math.ceil(value.length / 2))
        const visible = value.slice(0, visibleLenght)
        const hidden = "*".repeat(value.length - visibleLenght)
        return visible + hidden
    }

    const maskEmail = (email) => {
        const [user, domain] = email.split("@");
        if (user.length <= 2) return `*@${domain}`;
        return `${user[0]}${user[1]}${"*".repeat(user.length - 2)}@${domain}`;
    };

    return {
        maskStart,
        maskEnd,
        maskEmail
    }
}