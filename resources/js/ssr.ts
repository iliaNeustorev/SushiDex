import {createInertiaApp} from '@inertiajs/vue3'
import createServer from '@inertiajs/vue3/server'
import {renderToString} from '@vue/server-renderer'
import {createSSRApp, h} from 'vue'
import initVuetifyPlugin from "./plugins/vuetify";

createServer(page =>
    createInertiaApp({
        page,
        render: renderToString,
        resolve: name => {
            const pages = import.meta.glob('./Pages/**/*.vue', {eager: true})
            return pages[`./Pages/${name}.vue`] as any
        },
        setup({App, props, plugin}) {
            const vuetify = initVuetifyPlugin()
            return createSSRApp({
                render: () => h(App, props),
            })
                .use(plugin)
                .use(vuetify)
        },
    }),
)
