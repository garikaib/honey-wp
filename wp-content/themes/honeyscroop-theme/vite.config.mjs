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
                'shop-archive': path.resolve(__dirname, 'src/shop/archive.jsx'),
                'shop-single': path.resolve(__dirname, 'src/shop/single.jsx'),
                'shop-cart': path.resolve(__dirname, 'src/shop/cart.jsx'),
                'faqs': path.resolve(__dirname, 'src/faqs/index.jsx'),
                'admin-spa': path.resolve(__dirname, 'src/admin/index.jsx'),
                'style': path.resolve(__dirname, 'src/style.css'),
                'editor': path.resolve(__dirname, 'src/editor.css')
            },
            output: {
                entryFileNames: '[name].[hash].js',
                chunkFileNames: '[name].[hash].js',
                assetFileNames: '[name].[hash].[ext]',
                manualChunks: {
                    vendor: [
                        'react',
                        'react-dom',
                        'gsap',
                        'lucide-react',
                        'zustand'
                    ]
                }
            },
        },
        emptyOutDir: true,
    },
});
