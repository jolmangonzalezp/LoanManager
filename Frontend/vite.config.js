import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import path from 'path'

export default defineConfig({
  server: {
    host: true,
  },
  plugins: [vue()],
  resolve: {
    alias: {
      '@': path.resolve(__dirname, './src'),
      '@Modules': path.resolve(__dirname, './src/Modules'),
      '@Shared': path.resolve(__dirname, './src/Shared')
    }
  }
})