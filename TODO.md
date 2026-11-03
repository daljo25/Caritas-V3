# TODO

Cosas detectadas durante la actualización a Laravel 13 que **no** se tocaron
(son previas al upgrade, no las provocó). Ordenadas por impacto.

## 1. El scheduler no ejecuta nada (alto)

`app/Console/Kernel.php` existe y define dos tareas mensuales, pero desde
Laravel 11 ese archivo ya no se registra: `bootstrap/app.php` no lo enlaza y
`artisan schedule:list` sale vacío.

Consecuencia: `aids:update-status` y `beneficiaries:update-state` **nunca se
ejecutan automáticamente**.

- [ ] Mover las dos tareas a `routes/console.php` con `Schedule::command(...)`
      conservando `monthly()` y `sendOutputTo(...)`
- [ ] Borrar `app/Console/Kernel.php` (o dejarlo solo si se registra
      explícitamente con `->withKernel()` en `bootstrap/app.php`)
- [ ] Verificar con `php artisan schedule:list`
- [ ] Confirmar que el worker `schedule` de lerd está arrancado

## 2. `CACHE_PREFIX` vacío en `.env` (medio)

`.env` tiene `CACHE_PREFIX=` (vacío), y `env()` devuelve cadena vacía en vez del
default de `config/cache.php`, así que todas las claves de cache se guardan sin
prefijo. Con `CACHE_STORE=database` en una base compartida puede haber
colisiones con otra app.

- [ ] Quitar la línea de `.env` para usar el default, o darle un valor
      (`CACHE_PREFIX=caritas_v5_`)
- [ ] Revisar lo mismo para `SESSION_COOKIE` / `REDIS_PREFIX` si se llegan a usar

## 3. `APP_URL` no coincide con el dominio de lerd (bajo)

`.env` apunta a `http://localhost:8000` (modo `artisan serve`) pero el sitio se
sirve en `http://caritas-v5.test`. Afecta a enlaces generados, `asset()`,
redirecciones y a la cookie de sesión si se cambia de host.

- [ ] Decidir el modo de trabajo (lerd o `artisan serve`) y alinear `APP_URL`
- [ ] Si es lerd: `lerd env setup` o `APP_URL=http://caritas-v5.test`

## 4. Estilo de código con Pint (bajo)

`vendor/bin/pint --test` marca prácticamente todo `app/` y algunos `config/`,
`routes/`, `database/` y `tests/`. Es deuda previa, no del upgrade.

- [ ] Ejecutar `vendor/bin/pint` en un commit dedicado (solo formato, sin lógica)
- [ ] Opcional: excluir `packages/` en `pint.json` para no formatear las copias
      locales de paquetes de terceros

## 5. Dependencias locales parcheadas (recordatorio)

Los tres paquetes de `packages/` están copiados solo para ampliar su restricción
de framework (ver `packages/README.md`).

- [ ] Revisar periódicamente si `stidges/laravel-country-flags` publica versión
      con soporte Laravel 13 y eliminarlo
- [ ] Lo mismo con `outerweb/settings` y `outerweb/filament-settings`, que solo
      se pueden actualizar a su v2 cuando se migre el panel a **Filament 4**

## 6. Front-end sin lockfile (bajo)

`site_doctor` avisa: no hay lockfile de Node commiteado, así que las
instalaciones no son reproducibles.

- [ ] Commitear el lockfile del gestor que se use (npm/pnpm/yarn/bun)

## 7. Ruta lenta `POST /livewire/update` (bajo)

`site_doctor` marca `POST /livewire/update` ~13x por encima del tiempo típico
del sitio (1608 ms). Puede ser N+1 en algún listado o guardado del panel.

- [ ] Repetir con `diag` `dumps_toggle` + `profiler_toggle` y `optimize_route`
      sobre las acciones de Livewire que más se usan
