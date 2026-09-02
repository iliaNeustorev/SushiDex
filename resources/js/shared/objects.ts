export function pick<
    T extends object,
    K extends Array<keyof T>
>
(obj: T, keys: K): Pick<T, K[number]> {
    const res = {} as any;
    keys.forEach(key => res[key] = obj[key]);
    return res;
}

export type RequiredKeys<T extends object, Keys extends keyof T> = {
    [K in Keys]-?: T[K]
} & Omit<T, Keys>
