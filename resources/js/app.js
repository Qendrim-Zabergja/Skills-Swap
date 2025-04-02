import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import App from './App.vue';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App: InertiaApp, props, plugin }) {
        const app = createApp({
            render: () => h(InertiaApp, props),
        });

        app.use(plugin)
           .use(ZiggyVue)
           .component('App', App) // Register App.vue globally if needed
           .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
