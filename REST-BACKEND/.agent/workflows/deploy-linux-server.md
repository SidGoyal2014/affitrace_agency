---
description: Deploy Laravel backend to Linux server with queue workers
---

# Linux Server Deployment Guide

## Prerequisites

Install required packages (Ubuntu/Debian):

```bash
sudo apt update
sudo apt install nginx php8.2-fpm php8.2-mysql php8.2-mbstring php8.2-xml php8.2-curl php8.2-zip composer mysql-client supervisor -y
```

---

## Step 1: Deploy Application Code

```bash
# Clone/upload code to server
cd /var/www
git clone <your-repo> affitrace
cd affitrace

# Install dependencies (production)
composer install --optimize-autoloader --no-dev

# Set permissions
sudo chown -R www-data:www-data /var/www/affitrace
sudo chmod -R 755 /var/www/affitrace/storage
sudo chmod -R 755 /var/www/affitrace/bootstrap/cache
```

---

## Step 2: Configure Environment

```bash
cp .env.example .env
nano .env
```

**Required .env settings:**
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_HOST=your-db-host
DB_DATABASE=affitrace
DB_USERNAME=your-db-user
DB_PASSWORD=your-db-password

MAIN_DB_HOST=your-db-host
MAIN_DB_DATABASE=affitrace
MAIN_DB_USERNAME=your-db-user
MAIN_DB_PASSWORD=your-db-password

QUEUE_CONNECTION=database
```

---

## Step 3: Run Migrations & Optimize

```bash
php artisan key:generate
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Step 4: Setup Supervisor for Queue Workers

Create supervisor config:

```bash
sudo nano /etc/supervisor/conf.d/affitrace-worker.conf
```

**Paste this configuration:**

```ini
[program:affitrace-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/affitrace/artisan queue:work --queue=reports,default --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/affitrace/storage/logs/worker.log
stopwaitsecs=3600
```

**Start the workers:**

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start affitrace-worker:*
```

---

## Step 5: Configure Nginx

```bash
sudo nano /etc/nginx/sites-available/affitrace
```

**Paste this configuration:**

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/affitrace/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

**Enable and restart:**

```bash
sudo ln -s /etc/nginx/sites-available/affitrace /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

---

## Step 6: Setup SSL (Optional but Recommended)

```bash
sudo apt install certbot python3-certbot-nginx -y
sudo certbot --nginx -d your-domain.com
```

---

## Useful Commands

| Command | Purpose |
|---------|---------|
| `sudo supervisorctl status` | Check worker status |
| `sudo supervisorctl restart affitrace-worker:*` | Restart all workers |
| `sudo supervisorctl stop affitrace-worker:*` | Stop all workers |
| `tail -f /var/www/affitrace/storage/logs/worker.log` | View worker logs |
| `tail -f /var/www/affitrace/storage/logs/laravel.log` | View app logs |
| `php artisan queue:retry all` | Retry failed jobs |
| `php artisan queue:failed` | List failed jobs |

---

## Updating Application

```bash
cd /var/www/affitrace
git pull origin main
composer install --optimize-autoloader --no-dev
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
sudo supervisorctl restart affitrace-worker:*
```

---

## Troubleshooting

**Workers not starting:**
```bash
sudo supervisorctl tail -f affitrace-worker:00
```

**Permission issues:**
```bash
sudo chown -R www-data:www-data /var/www/affitrace/storage
sudo chmod -R 775 /var/www/affitrace/storage
```

**Clear all caches:**
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```
