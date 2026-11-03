# TODO

Pendientes reales, después de cerrar los 12 puntos de la lista anterior
(scheduler, prefijo de caché, `APP_URL`, Pint, dependencias parcheadas,
lockfile, N+1 de los widgets, `storage:link`, disco de subidas, valores por
defecto de Filament 5, Tailwind 4 y verificación de email).

## 1. `stidges/laravel-country-flags` y Laravel 13 (esperando al mantenedor)

El paquete 3.0.1 (última versión) sigue requiriendo `illuminate/support
^11.0|^12.0`, así que la copia local de `packages/laravel-country-flags` solo
amplía esa restricción. El código funciona sin cambios en Laravel 13.

Estado a 2026-11-03:

- El PR automático de Laravel Shift #13 (`Laravel 13.x Compatibility`) está
  abierto desde 2026-02-24 sin revisar.
- El repositorio no recibe cambios desde 2025-03-05, así que el mantenedor
  parece inactivo.
- 2026-11-03: comentario publicado en el PR #13 confirmando que el paquete
  funciona en Laravel 13.40 / PHP 8.4 sin tocar el código
  (https://github.com/stidges/laravel-country-flags/pull/13#issuecomment-6613795966).

- [ ] Si no hay release antes del **2026-11-10**, publicar fork propio:
      `composer.json` con `"name": "daljo25/laravel-country-flags"` y
      `"illuminate/support": "^11.0|^12.0|^13.0"`, tag `3.1.0`, enviarlo a
      Packagist y sustituir `packages/laravel-country-flags` por
      `composer require daljo25/laravel-country-flags:^3.1`
- [ ] Cuando exista versión upstream (o fork), borrar la carpeta, quitar la
      entrada del `require` y la del repositorio `path` si queda vacío

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
