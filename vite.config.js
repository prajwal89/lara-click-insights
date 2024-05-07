import {
    defineConfig
} from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            hotFile: 'lara-click-insight.hot',
            buildDirectory: 'build',
            // buildDirectory: 'vendor/prajwal89/lara-click-insight',
            input: ['resources/js/track-event.js'],
            refresh: true,
        }),
    ],
});

// export default defineConfig({
//     build: {
//         outDir: 'build', // Output directory
//         minify: 'terser', // Minify using terser
//         sourcemap: true, // Generate source maps
//     },
//     optimizeDeps: {
//         include: ['resources/js/track-event.js'], // Input file(s)
//     },
//     server: {
//         strictPort: true,
//         host: 'localhost', // If you want to specify a host
//         port: 3000, // Port number
//     },
// });
