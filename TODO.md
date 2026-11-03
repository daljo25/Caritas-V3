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

- [ ] `devonab/filament-easy-footer`: sus views usan utilidades de Tailwind que
      Filament 5 no compila sin tema propio. Mientras no se cree un tema, el
      arreglo vive en `public/css/filament-footer.css`; cuando el plugin lo
      resuelva (o se compile un tema) se puede borrar ese fichero y la entrada
      `->assets([...])` de `DashboardPanelProvider`.
- [ ] Si algún día se quiere personalizar el panel: `php artisan
      make:filament-theme` (Tailwind 4 ya está instalado) y añadir
      `@source` de los plugins que se usen.

## 3. Detalles menores conocidos

- `resources/views/welcome.blade.php` no tiene ruta (el panel ocupa `/`), pero
  conserva dentro el CSS precompilado de Tailwind 3 como fallback para cuando no
  hay build. Solo se usa si falta `public/build`.
- En local, `SESSION_DRIVER=database` y `CACHE_STORE=database` comparten el
  fichero SQLite: cada escritura cuesta 12-18 ms y un update de Livewire hace
  varias. En producción (MySQL) no aplica.
- `admin@example.com` tiene la contraseña `password` en esta copia local, puesta
  durante las pruebas de navegador. Cambiarla antes de exponer el entorno.
