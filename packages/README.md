# Local package patches

This folder holds patched copies of packages published on Packagist. They are
loaded through a `path` repository (see `repositories` in the root
`composer.json`), so Composer installs them as symlinks into `vendor/`.

| Folder                  | Package                         | Upstream version | Change                          |
| ----------------------- | ------------------------------- | ---------------- | ------------------------------- |
| `laravel-country-flags` | `stidges/laravel-country-flags` | 3.0.1            | `illuminate/support` allows `^13.0` |

The code itself is unchanged.

## How to get rid of this

Delete the folder, drop the matching entry from the root `composer.json`
`require` block, and run `composer update stidges/laravel-country-flags`.

`stidges/laravel-country-flags` has no Laravel 13 release yet (3.0.1 is the
latest), so the copy is only needed until upstream ships one. When it does,
remove the folder and, if no other local package remains, the `path` repository
entry.

`outerweb/settings` and `outerweb/filament-settings` used to live here for
Filament 3. Their v2 releases require Filament 4/5 and Laravel 13, so they are
installed from Packagist since the panel upgrade.
