import {createApp, h} from 'vue'
import {createInertiaApp} from '@inertiajs/vue3'
import initVuetifyPlugin from './plugins/vuetify';
import {createPinia} from 'pinia'
import '~css/base.css';

createInertiaApp({
    resolve: name => {
        const pages = import.meta.glob('./Pages/**/*.vue', {eager: true});
        return pages[`./Pages/${name}.vue`] as any;
    },
    setup({el, App, props, plugin}) {
        const vuetify = initVuetifyPlugin();
        const pinia = createPinia()
        createApp({render: () => h(App, props)})
            .use(plugin)
            .use(vuetify)
            .use(pinia)
            .mount(el)
    },
});
