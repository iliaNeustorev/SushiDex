import {createApp, h} from 'vue'
import {createInertiaApp} from '@inertiajs/vue3'
import initVuetifyPlugin from './plugins/vuetify';

createInertiaApp({
    resolve: name => {
        const pages = import.meta.glob('./Pages/**/*.vue', {eager: false});
        return pages[`./Pages/${name}.vue`]() as any;
    },
    setup({el, App, props, plugin}) {
        const vuetify = initVuetifyPlugin();

        createApp({render: () => h(App, props)})
            .use(plugin)
            .use(vuetify)
            .mount(el)
    },
});
