import {defineConfig} from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue'
import {fileURLToPath} from 'node:url';
import vuetify from 'vite-plugin-vuetify'

export default defineConfig({
    plugins: [
        vue(),
        vuetify({autoImport: true}),
        laravel({
            input: ['resources/js/app.ts'],
            ssr: 'resources/js/ssr.ts',
            refresh: true,
        }),
    ],
    server: {
        host: "0.0.0.0",
        port: 5173,
        hmr: {
            host: "localhost",
        },
    },
    resolve: {
        alias: {
            '~gen': fileURLToPath(new URL('./resources/generated', import.meta.url)),
            '~types': fileURLToPath(new URL('./resources/generated/types', import.meta.url)),
            '~routes': fileURLToPath(new URL('./resources/generated/wayfinder/actions/App/Http/Controllers', import.meta.url)),
            '~vue': fileURLToPath(new URL('./resources/js', import.meta.url)),
        }
    }
});
