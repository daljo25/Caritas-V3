# Local package patches

These three packages are patched copies of packages published on Packagist. They
are loaded through a `path` repository (see `repositories` in the root
`composer.json`), so Composer installs them as symlinks into `vendor/`.

The only change in each copy is a widened framework constraint, which is what
blocks Laravel 13 when the upstream releases are installed:

| Folder                        | Package                          | Upstream version | Change                                                          |
| ----------------------------- | -------------------------------- | ---------------- | --------------------------------------------------------------- |
| `laravel-country-flags`       | `stidges/laravel-country-flags`  | 3.0.1            | `illuminate/support` allows `^13.0`                              |
| `settings`                    | `outerweb/settings`              | 1.1.0            | `laravel/framework` allows `^13.0`                               |
| `filament-settings`           | `outerweb/filament-settings`     | 1.3.0            | `laravel/framework` allows `^13.0`                               |

The code itself is unchanged and works on Laravel 13 as-is.

## How to get rid of these

Delete a folder, drop the matching entry from the root `composer.json` `require`
block, and run `composer update <package>`:

- `stidges/laravel-country-flags` has no Laravel 13 release yet (3.0.1 is the
  latest).
- `outerweb/settings` v2 and `outerweb/filament-settings` v2 do support Laravel
  13, but they require **Filament 4**, so they can only replace these copies
  once the panel is upgraded to Filament 4.

When upstream releases a Laravel 13 compatible version for the Filament 3 line,
remove the folder and the `path` repository entry.
