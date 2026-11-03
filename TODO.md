# TODO

Pendientes reales, después de cerrar los 12 puntos de la primera lista
(scheduler, prefijo de caché, `APP_URL`, Pint, lockfile, N+1 de los widgets,
`storage:link`, disco de subidas, valores por defecto de Filament 5, Tailwind 4
y verificación de email).

## 1. Repetir en cada entorno nuevo (no es código)

La copia actual venía de un Mac y traía el enlace de storage roto; en un
despliegue limpio hay que hacer esto:

- [ ] `php artisan storage:link` (los PDF leen `public/storage/...`)
- [ ] `npm ci && npm run build` (`public/build` no se versiona)
- [ ] `php artisan migrate --force`
- [ ] Arrancar el worker `schedule` (`php artisan schedule:work` o el worker de
      lerd) o las tareas mensuales no se ejecutan

## 2. Dependencias de terceros a vigilar

- [ ] `devonab/filament-easy-footer`: ya se pinta bien porque el panel compila
      un tema propio (`resources/css/filament/dashboard/theme.css`) con el
      `@source` que pide su README. Si algún día se añade otro plugin que use
      Tailwind en sus views, hay que añadirle su `@source` en ese mismo fichero.
- [ ] Cualquier plugin nuevo de Filament: revisar si necesita `@source` o si
      trae su propio CSS compilado.

## 3. Detalles menores conocidos

- `resources/views/welcome.blade.php` no tiene ruta (el panel ocupa `/`), pero
  conserva dentro el CSS precompilado de Tailwind 3 como fallback para cuando no
  hay build. Solo se usa si falta `public/build`.
- En local, `SESSION_DRIVER=database` y `CACHE_STORE=database` comparten el
  fichero SQLite: cada escritura cuesta 12-18 ms y un update de Livewire hace
  varias. En producción (MySQL) no aplica.
- `admin@example.com` tiene la contraseña `password` en esta copia local, puesta
  durante las pruebas de navegador. Cambiarla antes de exponer el entorno.

## 4. Despliegue en producción (dploy + CloudPanel)

Producción corre **v5.0.1**: Laravel 13.40, Filament 5.11, Livewire 4.4 y
PHP 8.4. El rollback es `v3.0.0` (Laravel 12 + Filament 3).

- [ ] **Desplegar tags, nunca ramas con `/`**: dploy crea
      `releases/<fecha>-<ref>` y enlaza los compartidos con
      `ln -srn ../../shared/<dir>`. Con `upgrade/filament-5` la release queda
      dos niveles dentro y el symlink apunta a `releases/shared`, así que
      `storage/logs` no existe y el deploy falla en `package:discover`.
- [ ] En `~/.dploy/config.yml` usar `php8.4` (CLI y `php8.4-fpm`), quitar
      `--ignore-platform-reqs` y compilar assets antes del switch:
      `nvm use 24 && npm ci && npm run build` (con guarda `if [ -f
      package-lock.json ]` para que `v3.0.0` siga siendo redesplegable).
- [ ] `storage:link` en `before_commands` (no en `after_commands`) para que la
      release esté completa antes de cambiar el symlink.
- [ ] `.env` de `~/.dploy/overlays/.env` con `APP_ENV=production`,
      `APP_DEBUG=false` y `APP_URL=https://...`.
- [ ] Cron del scheduler en CloudPanel:
      `* * * * * php8.4 .../current/artisan schedule:run >/dev/null 2>&1`
- [ ] Worker de cola (supervisor/systemd) apuntando a
      `.../current/artisan queue:work`; sin él los imports de Filament se
      quedan en cola.
- [ ] Rollback: `dploy deploy v3.0.0` o
      `ln -sfn ~/backup-release-v3 current && sudo systemctl reload php8.4-fpm`.

## 5. Filament 5 y acceso al panel

Filament 5 solo deja entrar a usuarios cuyo modelo implemente
`Filament\Models\Contracts\FilamentUser`: si no, exige `app.env = 'local'` y
devuelve 403 en cualquier otro entorno. `App\Models\User` ya lo implementa
(`canAccessPanel()` devuelve `true`) y hay un test de regresión en
`tests/Feature/PanelAccessTest.php`.

- [ ] Si algún día hay que restringir quién entra, hacerlo en
      `canAccessPanel()` (email, rol…) en vez de quitar la interfaz.
