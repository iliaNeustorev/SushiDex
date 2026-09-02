import type {GeneralPagination} from "~types/generated";

export type TypedPagination<T> =
    Omit<GeneralPagination, 'data'> &
    { data: T[] }
