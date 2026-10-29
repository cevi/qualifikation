import vue from '@vitejs/plugin-vue';
import { defineConfig } from 'vitest/config';

// JS-Tests (npm test). Eigene Konfiguration ohne laravel-vite-plugin, das braucht nur der Build.
export default defineConfig({
    plugins: [vue()],
    test: {
        environment: 'happy-dom',
        include: ['tests/js/**/*.test.js'],
        // restoreMocks gilt nur für vi.spyOn; clearMocks setzt auch die Aufrufe von vi.fn() zurück.
        clearMocks: true,
        restoreMocks: true,
        unstubGlobals: true,
    },
});
