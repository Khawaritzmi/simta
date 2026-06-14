import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/landing.css',
                'resources/css/auth-login.css',
                'resources/css/account-password.css',
                'resources/css/auth-register.css',
                'resources/css/auth-student-register.css',
                'resources/css/admin-dashboard.css',
                'resources/css/admin-bimbingan-pa.css',
                'resources/css/admin-database-ta.css',
                'resources/css/admin-seminars.css',
                'resources/css/database-ta-index.css',
                'resources/css/database-ta-show.css',
                'resources/css/dosen-layout.css',
                'resources/css/mahasiswa-layout.css',
                'resources/css/pa-dosen.css',
                'resources/css/pa-mahasiswa.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        host: '0.0.0.0',
        port: 5173,
        strictPort: true,
        hmr: {
            host: 'localhost',
        },
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
