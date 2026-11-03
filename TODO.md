# TODO

Cosas detectadas durante las actualizaciones a Laravel 13 y Filament 5 que **no**
se tocaron (son previas al upgrade, no las provocó). Ordenadas por impacto.

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

## 5. Dependencia local parcheada (recordatorio)

Queda un solo paquete copiado en `packages/` (`stidges/laravel-country-flags`),
solo para ampliar su restricción de framework (ver `packages/README.md`).
`outerweb/settings` y `outerweb/filament-settings` ya se instalan desde Packagist
en su v2.

- [ ] Revisar periódicamente si `stidges/laravel-country-flags` publica versión
      con soporte Laravel 13 y eliminar `packages/laravel-country-flags`

## 6. Front-end sin lockfile (bajo)

`site_doctor` avisa: no hay lockfile de Node commiteado, así que las
instalaciones no son reproducibles.

- [ ] Commitear el lockfile del gestor que se use (npm/pnpm/yarn/bun)

## 7. Ruta lenta `POST /livewire/update` (bajo)

`site_doctor` marca `POST /livewire/update` ~13x por encima del tiempo típico
del sitio (1608 ms). Puede ser N+1 en algún listado o guardado del panel.

- [ ] Repetir con `diag` `dumps_toggle` + `profiler_toggle` y `optimize_route`
      sobre las acciones de Livewire que más se usan

## 8. `public/storage` no está enlazado (alto)

`public/storage` existe pero está vacío: falta `php artisan storage:link`. Los
logos que se suben desde Configuración van al disco `public`
(`storage/app/public/settings/logos`), y las plantillas PDF los buscan en
`public/storage/...`, así que los logos no aparecen en los PDF.

- [ ] Ejecutar `php artisan storage:link`
- [ ] Comprobar que los PDF vuelven a mostrar los logos

## 9. Disco de subida frente a `FILESYSTEM_DISK=local` (medio)

`.env` tiene `FILESYSTEM_DISK=local`, pero `config/filament.php` fuerza el disco
`public` para no mover las subidas existentes al pasar a Filament 4. Es una
decisión deliberada del upgrade; conviene confirmarla y, si se prefiere el
comportamiento nuevo, subir los ficheros y cambiar la configuración.

- [ ] Decidir si las subidas del panel siguen en `public` (actual) o pasan al
      disco por defecto
- [ ] Si se cambia, mover `storage/app/public/settings/logos` al disco nuevo

## 10. Cambios de comportamiento asumidos en el panel (bajo)

En `AppServiceProvider::boot()` hay dos `configureUsing` que conservan el
comportamiento de Filament 3:

- `Fieldset::columnSpanFull()`: en v4/v5 los `Fieldset` ocupan una columna.
- `Table::deferFilters(false)`: en v4/v5 los filtros esperan a que se pulse
  "Aplicar".

- [ ] Revisar ambas decisiones con el usuario del panel; si se prefieren los
      valores nuevos de Filament, borrar las líneas

## 11. Tailwind v3 y sin tema propio de Filament (bajo)

`package.json` sigue en Tailwind v3 y el panel no tiene tema propio. Filament
4/5 usa Tailwind v4 internamente, pero al no haber tema personalizado el panel
funciona con el CSS precompilado. Solo importa si se quiere personalizar el
panel o usar clases Tailwind propias en vistas de Filament.

- [ ] Si se necesita un tema: `php artisan make:filament-theme`, subir Tailwind
      a v4 y usar `@source` en el CSS del tema

## 12. Verificación de email activada pero inerte (bajo)

El panel tiene `->emailVerification()`, pero `App\Models\User` no implementa
`Illuminate\Contracts\Auth\MustVerifyEmail`, así que la verificación no hace
nada.

- [ ] Decidir si se activa de verdad (implementar el contrato y la columna
      `email_verified_at` ya existe) o se quita del panel
