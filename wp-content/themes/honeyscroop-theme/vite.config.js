import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import path from 'path';

export default defineConfig({
    plugins: [react()],
    build: {
        outDir: 'dist',
        rollupOptions: {
            input: {
                'honey-finder': path.resolve(__dirname, 'src/honey-finder/index.jsx'),
                'header-nav': path.resolve(__dirname, 'src/header-nav/index.jsx'),
                'product-grid': path.resolve(__dirname, 'src/product-grid/index.jsx'),
                'partner-ticker': path.resolve(__dirname, 'src/partner-ticker/index.jsx'),
                'bees': path.resolve(__dirname, 'src/bees/index.js'),
                'hive-scene': path.resolve(__dirname, 'src/bees/hive-scene.js'),
                'events-calendar': path.resolve(__dirname, 'src/events-calendar/index.jsx'),
                'style': path.resolve(__dirname, 'src/style.css'),
                'editor': path.resolve(__dirname, 'src/editor.css')
            },
            output: {
                entryFileNames: '[name].js',
                chunkFileNames: '[name].js',
                assetFileNames: '[name].[ext]',
                manualChunks: {
                    vendor: ['gsap']
                }
            },
        },
        emptyOutDir: true,
    },
});
