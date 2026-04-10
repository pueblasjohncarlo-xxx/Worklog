# HOSTINGER DEPLOYMENT CHECKLIST

## Phase 1: Pre-Deployment (Local)
- [ ] All code changes committed to git
- [ ] `routes/web.php` cleaned (debug routes removed) ✅ DONE
- [ ] Blade templates validated (no duplicates) ✅ DONE
- [ ] `APP_DEBUG=true` is still in local .env (OK for now)

## Phase 2: Database Preparation
- [ ] Backup current database
- [ ] Note down all migration names (`php artisan migrate:status`)
- [ ] Test migrations on local (optional): `php artisan migrate:refresh`
- [ ] Prepare Hostinger database:
  - [ ] Create new database in Hostinger cPanel
  - [ ] Create database user
  - [ ] Grant all privileges to user
  - [ ] Note: hostname (usually `localhost`), username, password, database name

## Phase 3: File Upload to Hostinger

### Option A: Via FTP/File Manager
- [ ] Upload all files except:
  - [ ] `/vendor` (will install with composer)
  - [ ] `/node_modules` (will install with npm)
  - [ ] `.env` (create separately on server)
  - [ ] `.git` (optional, can include)

### Option B: Via Git (Recommended)
- [ ] Login to Hostinger SSH
- [ ] Clone repository: `git clone https://github.com/your-repo.git`
- [ ] Navigate to folder: `cd worklog`

## Phase 4: Production Setup on Hostinger
- [ ] Navigate to project root
- [ ] Install PHP dependencies:
  ```bash
  composer install --no-dev --optimize-autoloader
  ```
- [ ] Create `.env` file:
  ```bash
  cp .env.production.example .env
  ```
- [ ] Edit `.env` with production values (use nano editor):
  ```bash
  nano .env
  ```
  - [ ] Set `APP_ENV=production`
  - [ ] Set `APP_DEBUG=false`
  - [ ] Set `APP_URL=https://yourdomain.com`
  - [ ] Set database credentials (from Hostinger cPanel)
  - [ ] Set mail configuration
- [ ] Generate application key:
  ```bash
  php artisan key:generate
  ```
- [ ] Run database migrations:
  ```bash
  php artisan migrate --force
  ```
  - [ ] Watch for errors
  - [ ] All migrations should say "Migrated"
- [ ] Seed database (if needed):
  ```bash
  php artisan db:seed --force
  ```

## Phase 5: Optimization & Security
- [ ] Cache routes:
  ```bash
  php artisan route:cache
  ```
- [ ] Cache config:
  ```bash
  php artisan config:cache
  ```
- [ ] Cache views:
  ```bash
  php artisan view:cache
  ```
- [ ] Set directory permissions:
  ```bash
  chmod -R 755 storage
  chmod -R 755 bootstrap/cache
  ```
- [ ] Install front-end dependencies (if needed):
  ```bash
  npm install && npm run build
  ```

## Phase 6: Verify Deployment
- [ ] Visit: `https://yourdomain.com`
  - [ ] See login page
  - [ ] No 500 errors
  - [ ] No 403 errors
- [ ] Test login with test user:
  - [ ] Email verification link works
  - [ ] Login successful
  - [ ] Dashboard accessible
  - [ ] Role-based access works
- [ ] Test profile editing:
  - [ ] Navigate to profile
  - [ ] Can see profile information
  - [ ] Can update name
  - [ ] Can update email (triggers re-verification)
  - [ ] Can upload profile photo
  - [ ] Can change password
- [ ] Test critical features:
  - [ ] Can view announcements
  - [ ] Can submit work logs (if student)
  - [ ] Can view reports
  - [ ] Can send messages

## Phase 7: Monitoring & Logs
- [ ] Check application logs:
  ```bash
  tail -f storage/logs/laravel.log
  ```
  - [ ] No critical errors
  - [ ] Warning/info messages only
- [ ] Monitor error rate for 24 hours
- [ ] Check Hostinger error logs in cPanel

## Phase 8: Post-Deployment
- [ ] Create admin user (if not already created):
  ```bash
  php artisan tinker
  # Inside tinker:
  User::create(['name'=>'Admin','email'=>'admin@example.com','password'=>Hash::make('password'),'role'=>'admin','is_approved'=>true,'email_verified_at'=>now()])
  ```
- [ ] Restrict debug tools:
  ```bash
  php artisan down --check="/debug"
  ```
- [ ] Setup database backups in Hostinger cPanel
- [ ] Setup SSL/HTTPS (usually automatic with Hostinger)
- [ ] Enable all caching in .htaccess or php.ini

## Phase 9: Troubleshooting

### If you see 403 errors:
```bash
# Check user roles in database
php artisan tinker
User::where('email','your-email@example.com')->first()
# Should show role: 'student' (or admin/coordinator/etc)

# If role is NULL, fix it:
User::where('email','your-email@example.com')->update(['role'=>'student'])
```

### If you see 500 errors:
```bash
# Check logs
tail -f storage/logs/laravel.log

# Verify file permissions
ls -la storage/
ls -la bootstrap/cache/

# Clear caches if corrupted:
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### If migrations fail:
```bash
# Check migration status
php artisan migrate:status

# Rollback and retry (careful!)
php artisan migrate:rollback --force
php artisan migrate --force
```

## Critical Reminders:
1. ⚠️ **NEVER** leave `APP_DEBUG=true` in production
2. ⚠️ **NEVER** commit `.env` to git
3. ⚠️ **ALWAYS** backup database before migrations
4. ⚠️ **ALWAYS** test in staging before production
5. ✅ **DO** set appropriate file permissions
6. ✅ **DO** enable HTTPS
7. ✅ **DO** monitor logs after deployment

---

**Status:** Ready for Deployment
**Last Updated:** April 7, 2026
**Deployed By:** [Your Name]
**Deployment Date:** ___________
