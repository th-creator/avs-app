import { defineConfig } from "vite";
import vue from "@vitejs/plugin-vue";
import path from "path";
import vueI18n from "@intlify/vite-plugin-vue-i18n";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    base: '/build/',  // Set base URL for assets in production.
    plugins: [
        laravel({
            input: ["resources/js/src/main.ts"],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    includeAbsolute: false,
                },
            },
        }),
        vueI18n({
            include: path.resolve("resources/js/src/locales/**"),
        }),
    ],
    resolve: {
        alias: {
            "@": path.resolve("./resources/js/src"),
        },
    },
    build: {
        outDir: 'public/build',  // Ensure the build output is in the right directory.
        manifest: true,          // Ensure manifest.json is generated for Laravel.
    },
    optimizeDeps: {
        include: ["quill"],
    },
});
