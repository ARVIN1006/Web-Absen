import './bootstrap';
import '../css/app.css';

import { createRoot } from 'react-dom/client';
import { createInertiaApp } from '@inertiajs/react';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { Ziggy } from './ziggy.js';
import { route } from '../../vendor/tightenco/ziggy';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

console.log('[DEBUG] app.jsx loaded');
console.log('[DEBUG] Environment:', import.meta.env.MODE);
console.log('[DEBUG] App Name:', appName);

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => {
        console.log('[DEBUG] Resolving page:', name);
        return resolvePageComponent(`./Pages/${name}.jsx`, import.meta.glob('./Pages/**/*.jsx'));
    },
    setup({ el, App, props }) {
        console.log('[DEBUG] Inertia setup called');
        console.log('[DEBUG] Props:', props);
        console.log('[DEBUG] Initial page:', props.initialPage);
        
        const root = createRoot(el);
        root.render(<App {...props} />);
        
        console.log('[DEBUG] React app rendered');
    },
    progress: {
        color: '#4B5563',
    },
});
