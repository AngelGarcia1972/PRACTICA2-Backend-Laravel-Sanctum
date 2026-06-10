# Guía de Despliegue en Producción

## Requisitos del servidor
- Ubuntu 22.04
- Nginx
- PHP 8.2-FPM
- MySQL 8.0
- Redis
- Node.js 18+
- Supervisor

## Pasos de instalación

### 1. Clonar repositorios
```bash
cd /var/www
git clone https://github.com/AngelGarcia1972/PRACTICA2-Backend-Laravel-Sanctum backend
git clone https://github.com/AngelGarcia1972/PRACTICA2-Frontend-Vue-Pinia-Router frontend
```

### 2. Configurar backend
```bash
cd /var/www/backend
composer install --no-dev --optimize-autoloader
cp deploy/env.production.example .env
nano .env  # editar con tus valores reales
php artisan key:generate
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 3. Configurar frontend
```bash
cd /var/www/frontend
npm install
npm run build
```

### 4. Configurar Nginx
```bash
cp /var/www/backend/deploy/nginx.conf /etc/nginx/sites-available/tienda
ln -s /etc/nginx/sites-available/tienda /etc/nginx/sites-enabled/
nginx -t
systemctl reload nginx
```

### 5. Configurar Supervisor
```bash
cp /var/www/backend/deploy/supervisor.conf /etc/supervisor/conf.d/tienda.conf
supervisorctl reread
supervisorctl update
supervisorctl start all
supervisorctl status
```

### 6. GitHub Actions CI/CD
Agregar estos secrets en GitHub → Settings → Secrets:
- `SERVER_HOST` — IP del servidor
- `SERVER_USER` — usuario SSH (deploy)
- `SERVER_SSH_KEY` — llave privada SSH

## Verificación
- Frontend: https://tu-dominio.com
- API: https://tu-dominio.com/api/v1/productos
- Telescope: https://tu-dominio.com/telescope