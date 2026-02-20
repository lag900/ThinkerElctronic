import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

import { system } from './Stores/SystemStore';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        const vueApp = createApp({ render: () => h(App, props) });
        
        // Global Click-Once Directive for Enterprise Double-Submit Protection
        vueApp.directive('click-once', {
            mounted(el) {
                el.addEventListener('click', (e) => {
                    if (el.getAttribute('disabled')) return;
                    el.setAttribute('disabled', 'true');
                    el.classList.add('opacity-50', 'cursor-not-allowed');
                    setTimeout(() => {
                        el.removeAttribute('disabled');
                        el.classList.remove('opacity-50', 'cursor-not-allowed');
                    }, 1000); // 1s safety window
                });
            }
        });

        return vueApp
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#ff2d55', // Updated to match brand red
        showSpinner: true,
    },
});
