import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import { VitePWA } from "vite-plugin-pwa";

import { ViteImageOptimizer } from 'vite-plugin-image-optimizer';

import react from "@vitejs/plugin-react";

export default defineConfig({
    server: {
        host: "0.0.0.0",
        port: 5173,
        cors: true,
        hmr: {
            host: "localhost",
        },
    },
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.jsx"],
            refresh: true,
        }),
        react(),
        ViteImageOptimizer({
            /* pass your config */
        }),
        VitePWA({
            registerType: "autoUpdate",
            workbox: {
                globPatterns: ["**/*.{js,css,html,ico,png,svg,woff2,bin,json}"], // Meng-cache model face-api (.bin, .json)
                maximumFileSizeToCacheInBytes: 15000000, // Limit cache 15MB karena model AI berukuran besar
            },
            manifest: {
                name: "Sistem Absensi Wajah",
                short_name: "Absensi",
                description: "Aplikasi Absensi Berbasis AI dan Geolocation",
                theme_color: "#ffffff",
                icons: [
                    {
                        src: "/pwa-192x192.png",
                        sizes: "192x192",
                        type: "image/png",
                    },
                    {
                        src: "/pwa-512x512.png",
                        sizes: "512x512",
                        type: "image/png",
                    },
                ],
            },
        }),
    ],
});
