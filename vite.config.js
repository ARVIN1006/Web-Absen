import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import path from "node:path";

import { ViteImageOptimizer } from 'vite-plugin-image-optimizer';

import react from "@vitejs/plugin-react";

export default defineConfig({
    resolve: {
        alias: {
            fs: path.resolve(__dirname, "resources/js/Utils/emptyFs.js"),
        },
    },
    build: {
        chunkSizeWarningLimit: 800,
    },
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
    ],
});
