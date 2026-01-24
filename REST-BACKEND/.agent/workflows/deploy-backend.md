---
description: Deploy Affitrace backend to Linux server with queue workers (Nginx already configured)
---

# Backend Deployment Guide

**Path:** `/var/www/affitrace/backend`

---

## Step 1: Upload/Pull Code

```bash
cd /var/www/affitrace/backend
git pull origin main

# Or if fresh deployment:
# git clone <your-repo> /var/www/affitrace/backend
```

---

## Step 2: Install Dependencies

```bash
composer install --optimize-autoloader --no-dev
```

---

## Step 3: Set Permissions

```bash
sudo chown -R www-data:www-data /var/www/affitrace/backend
sudo chmod -R 755 /var/www/affitrace/backend/storage
sudo chmod -R 755 /var/www/affitrace/backend/bootstrap/cache
```

---

## Step 4: Configure Environment

```bash
nano /var/www/affitrace/backend/.env
```

**Required settings:**
```env
APP_ENV=production
APP_DEBUG=false
QUEUE_CONNECTION=database
MAXMIND_LICENSE_KEY=your_license_key_here
```

---

## Step 5: Run Migrations & Optimize

```bash
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Step 5b: Update GeoIP Database

```bash
php artisan geoip:update
```

> **Note:** Run this on first deployment and schedule weekly via cron or Supervisor.

---

## Step 6: Setup Queue Workers (Supervisor)

Create supervisor config:

```bash
sudo nano /etc/supervisor/conf.d/affitrace-worker.conf
```

**Paste this:**

```ini
[program:affitrace-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/affitrace/backend/artisan queue:work --queue=reports,default --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/affitrace/backend/storage/logs/worker.log
stopwaitsecs=3600
```

**Start workers:**

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start affitrace-worker:*
```

---

## Step 7: Nginx Configuration (Reference)

If needed, update nginx to point to the backend:

```nginx
server {
    listen 80;
    server_name api.your-domain.com;
    root /var/www/affitrace/backend/public;

    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

---

## Quick Commands

| Command | Purpose |
|---------|---------|
| `sudo supervisorctl status` | Check worker status |
| `sudo supervisorctl restart affitrace-worker:*` | Restart workers |
| `tail -f /var/www/affitrace/backend/storage/logs/worker.log` | View worker logs |
| `tail -f /var/www/affitrace/backend/storage/logs/laravel.log` | View app logs |

---

## Update Deployment (Quick)

```bash
cd /var/www/affitrace/backend
git pull origin main
composer install --optimize-autoloader --no-dev
php artisan migrate --force
php artisan geoip:update
php artisan config:cache
php artisan route:cache
sudo supervisorctl restart affitrace-worker:*
```
