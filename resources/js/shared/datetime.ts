export function toStandartDate(dt: Date) {
    return [
        dt.getFullYear(),
        ('0' + (dt.getMonth() + 1)).slice(-2),
        ('0' + dt.getDate()).slice(-2)
    ].join('-');
}

