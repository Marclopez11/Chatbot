import '../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { createPinia } from 'pinia';
import ChatBot from './Components/ChatBot.vue';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';
const pinia = createPinia();

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(pinia)
            .component('ChatBot', ChatBot)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});

// Función para detectar cambios en la autenticación cuando se navega entre páginas
document.addEventListener('inertia:navigate', (event) => {
    // Comprobar si tenemos un chatStore inicializado en Pinia
    if (window.__pinia && window.__pinia.chatStore) {
        // El chatStore comprobará si ha cambiado el estado de autenticación
        // y reiniciará el chat si es necesario
        setTimeout(() => {
            window.__pinia.chatStore.checkAuthState();
        }, 300); // Damos un pequeño retraso para que el DOM se actualice
    }
});
