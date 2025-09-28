import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'path';

export default defineConfig({
    resolve: {
        alias: {
            '@css': path.resolve(__dirname, './resources/css'),
            '@views': path.resolve(__dirname, './resources/js/views'),
            '@components': path.resolve(__dirname, './resources/js/components'),
            '@utils': path.resolve(__dirname, './resources/js/utils'),
            '@api': path.resolve(__dirname, './resources/js/api'),
            'randombytes': 'randombytes/browser',
            'readable-stream': 'readable-stream',
            'stream': 'readable-stream',
            'simple-peer': 'simple-peer/simplepeer.min.js',
        },
    },
    plugins: [
        laravel({
            input: ['resources/js/app.js', 'resources/css/app.css'], // Đảm bảo file CSS chính được thêm vào
            refresh: [
                'resources/views/**/*.blade.php', // Theo dõi thay đổi Blade template
                'routes/**/*.php',               // Theo dõi thay đổi file routes
                'app/**/*.php',                  // Theo dõi thay đổi các file PHP
            ],
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    define: {
        'process.env': {
            APP_URL: process.env.APP_URL, // Biến từ Laravel .env
            APP_NAME: process.env.APP_NAME,
        },
        global: 'globalThis',
    },
});
