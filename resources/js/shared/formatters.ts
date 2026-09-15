const dateFormatter = new Intl.DateTimeFormat('ru-RU', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    timeZone: 'Europe/Moscow',
});

const priceFormatter = new Intl.NumberFormat('ru-RU', {
    style: 'currency',
    currency: 'RUB',
    minimumFractionDigits: 0,
    maximumFractionDigits: 2,
});

export function formatDate(date: string): string {
    return dateFormatter.format(new Date(date));
}

export function formatPrice(price: string): string {
    const numericPrice = Number(price);

    return Number.isFinite(numericPrice)
        ? priceFormatter.format(numericPrice)
        : `${price} ₽`;
}

export function formatItemsCount(count: number): string {
    const lastTwoDigits = count % 100;
    const lastDigit = count % 10;

    if (lastTwoDigits >= 11 && lastTwoDigits <= 14) {
        return `${count} позиций`;
    }

    if (lastDigit === 1) {
        return `${count} позиция`;
    }

    if (lastDigit >= 2 && lastDigit <= 4) {
        return `${count} позиции`;
    }

    return `${count} позиций`;
}
