import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    // Hapus atau ubah bagian server ini
    server: {
        host: "localhost",
        cors: true,
    },
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.js"],
            refresh: true,
        }),
    ],
});
