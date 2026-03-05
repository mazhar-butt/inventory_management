import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

// Import NProgress for navigation loading bar
import NProgress from 'nprogress';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

// Configure NProgress with custom styling
NProgress.configure({ 
    showSpinner: true,
    trickleSpeed: 200,
    minimum: 0.3,
    easing: 'ease',
    speed: 500
});

// Create the Vue app
createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
    // Progress bar configuration
    progress: {
        color: '#6366f1',
        showSpinner: true,
    },
});

// Start NProgress on Inertia navigation start
document.addEventListener('inertia:start', () => {
    NProgress.start();
});

// Stop NProgress on Inertia navigation finish
document.addEventListener('inertia:finish', () => {
    NProgress.done();
});
