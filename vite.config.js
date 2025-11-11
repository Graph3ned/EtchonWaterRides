import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    server: {
        host: '0.0.0.0',
        port: '5173',
        strictPort: true,
        hmr: {
            host: '192.168.23.4'
        }
    },
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'node_modules/chart.js/dist/chart.js', // Include Chart.js
                'node_modules/chart.js/auto/auto.js' // Or auto-registering version
            ],
            refresh: true,
        }),
    ],
});
