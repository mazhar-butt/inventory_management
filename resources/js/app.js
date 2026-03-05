import './bootstrap';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

// Create the loader element
const createLoader = () => {
    const loader = document.createElement('div');
    loader.id = 'app-loader';
    loader.innerHTML = `
        <div class="loader-overlay">
            <div class="loader-spinner">
                <div class="spinner-ring"></div>
                <div class="spinner-ring"></div>
                <div class="spinner-ring"></div>
            </div>
            <p class="loader-text">Loading...</p>
        </div>
    `;
    loader.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        background: rgba(255, 255, 255, 0.9);
    `;
    document.body.appendChild(loader);
    return loader;
};

const loader = createLoader();

// Function to show/hide loader
const showLoader = () => {
    loader.style.display = 'flex';
};

const hideLoader = () => {
    loader.style.display = 'none';
};

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        // Add event listeners inside setup
        document.addEventListener('inertia:start', showLoader);
        document.addEventListener('inertia:finish', hideLoader);
        document.addEventListener('inertia:progress', hideLoader);
        
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
        
        // Hide loader after initial mount
        hideLoader();
        
        return app;
    },
    progress: {
        // Configure Inertia's built-in progress indicator
        color: '#daf63b',
        showSpinner: false,
    },
});