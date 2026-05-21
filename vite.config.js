import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js'],
            refresh: true,
        }),
    ],
    server: {
        host: '0.0.0.0', // Memungkinkan akses dari network luar
        hmr: {
            host: 'https://glummest-dangelo-politely.ngrok-free.dev',
            protocol: 'wss',
        },
    },
});
