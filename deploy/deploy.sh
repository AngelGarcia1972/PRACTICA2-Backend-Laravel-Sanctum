#!/bin/bash

echo "🚀 Iniciando despliegue..."

# ── Backend Laravel ──────────────────────────────────
echo "📦 Actualizando backend..."
cd /var/www/backend
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
php artisan storage:link
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# ── Frontend Vue ──────────────────────────────────────
echo "🎨 Compilando frontend..."
cd /var/www/frontend
git pull origin main
npm ci
npm run build

# ── Reiniciar workers ─────────────────────────────────
echo "⚙️ Reiniciando workers..."
supervisorctl restart laravel-worker
supervisorctl restart reverb-server

echo "✅ Despliegue completado!"
supervisorctl status