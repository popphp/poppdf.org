import { defineConfig } from 'vite';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    publicDir: false,
    plugins: [
        tailwindcss()
    ],
    build: {
        outDir: 'public/assets',
        // Vite empties outDir by default, which deletes the tracked .empty
        // placeholders that keep public/assets/{css,js} in git. The build only
        // ever writes those two files, so there is nothing stale to clear.
        emptyOutDir: false,
        rollupOptions: {
            input: 'app/assets/js/app.js',
            output: {
                entryFileNames: 'js/app.js',
                assetFileNames: (assetInfo) => {
                    return assetInfo.name === 'app.css' ? 'css/app.css' : 'assets/[name][extname]';
                }
            }
        }
    }
});
