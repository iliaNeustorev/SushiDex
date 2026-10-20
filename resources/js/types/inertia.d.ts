import '@inertiajs/core';

declare module '@inertiajs/core' {
    interface InertiaConfig {
        flashDataType: {
            type?: NotificationType;
            text?: string;
        };
    }
}

export type NotificationType =
    | 'success'
    | 'warning'
    | 'info'
    | 'error'
    | 'danger'
    | 'primary';
