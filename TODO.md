# TODO

Pendientes reales, después de cerrar los 12 puntos de la lista anterior
(scheduler, prefijo de caché, `APP_URL`, Pint, dependencias parcheadas,
lockfile, N+1 de los widgets, `storage:link`, disco de subidas, valores por
defecto de Filament 5, Tailwind 4 y verificación de email).

## 1. Dependencia local parcheada (recordatorio)

Queda un solo paquete copiado en `packages/` (`stidges/laravel-country-flags`),
solo para ampliar su restricción de framework (ver `packages/README.md`).
`outerweb/settings` y `outerweb/filament-settings` ya se instalan desde Packagist
en su v2.

- [ ] Revisar si `stidges/laravel-country-flags` publica versión con Laravel 13
      y, cuando salga, borrar `packages/laravel-country-flags`, quitar la entrada
      del `require` y ejecutar `composer update stidges/laravel-country-flags`

## 2. Repetir en cada entorno nuevo (no es código)

La copia actual venía de un Mac y traía el enlace de storage y el lockfile rotos;
en un despliegue limpio hay que hacer esto:

- [ ] `php artisan storage:link` (los PDF leen `public/storage/...`)
- [ ] `npm ci && npm run build` (`public/build` no se versiona)
- [ ] `php artisan migrate --force`
- [ ] Arrancar el worker `schedule` (`php artisan schedule:work` o el worker de
      lerd) o las tareas mensuales no se ejecutan

## 3. Detalles menores conocidos

- `resources/views/welcome.blade.php` no tiene ruta (el panel ocupa `/`), pero
  conserva dentro el CSS precompilado de Tailwind 3 como fallback para cuando no
  hay build. Solo se usa si falta `public/build`.
- En local, `SESSION_DRIVER=database` y `CACHE_STORE=database` comparten el
  fichero SQLite: cada escritura cuesta 12-18 ms y un update de Livewire hace
  varias. En producción (MySQL) no aplica.
- `admin@example.com` tiene la contraseña `password` en esta copia local, puesta
  durante las pruebas de navegador. Cambiarla antes de exponer el entorno.
