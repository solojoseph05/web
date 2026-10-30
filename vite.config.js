import { defineConfig } from 'vite'
import { resolve } from 'path'
import vue from '@vitejs/plugin-vue'
import tailwindcss from '@tailwindcss/vite'

export default defineConfig({
    plugins: [
        vue(),
        tailwindcss()
    ],

    server: {
        host: '0.0.0.0',
        port: 3000,
        allowedHosts: true,
        hmr: false,
        watch: null,
    },

    preview: {
        allowedHosts: true,
    },

    resolve: {
        alias: {
            '@': resolve(__dirname, 'src')
        }
    }
})