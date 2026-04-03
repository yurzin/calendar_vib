import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import path from 'path'

export default defineConfig({
  plugins: [vue(),
    {
      // Middleware: admin.calendar.local → admin.html
      name: 'subdomain-router',
      configureServer(server) {
        server.middlewares.use((req, res, next) => {
          const host = req.headers.host || '';
          const url = req.url || '';

          // Не трогаем ассеты, JS, CSS и служебные пути Vite
          const isAsset = url.includes('.')
            || url.startsWith('/@')
            || url.startsWith('/node_modules')
            || url.startsWith('/__vite');

          if (isAsset) {
            return next();
          }

          if (host.startsWith('admin.')) {
            req.url = '/admin.html';
          } else {
            req.url = '/index.html';
          }

          next();
        });
      }
    }
  ],
  server: {
    host: '0.0.0.0',
    port: 5173,
    allowedHosts: ['calendar.local', 'admin.calendar.local'],
    proxy: {
      '/api': {
        target: 'http://nginx:80',
        changeOrigin: true,
      },
      '/sanctum': {
        target: 'http://nginx:80',
        changeOrigin: true,
      },
    }
  },
  resolve: {
    alias: {
      '@': path.resolve(__dirname, './src'),
    },
  },
  build: {
    rollupOptions: {
      input: {
        main: './index.html',
        admin: './admin.html',
      },
    },
  },
})
