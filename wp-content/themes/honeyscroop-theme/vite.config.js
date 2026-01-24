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
                'style': path.resolve(__dirname, 'src/style.css'),
                'editor': path.resolve(__dirname, 'src/editor.css')
            },
            output: {
                entryFileNames: '[name].js',
                assetFileNames: '[name].[ext]',
            },
        },
        emptyOutDir: true,
    },
});
