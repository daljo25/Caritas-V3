<?php

namespace App\Support;

use Illuminate\Support\Arr;
use Outerweb\Settings\Services\Setting;

/**
 * Settings reader that loads the whole table once per request.
 *
 * The package's service runs a query for every call to setting(), and each PDF
 * view calls it several times (the logo alone twice), so rendering a single
 * receipt repeated the same select over and over. Lerd reads those repeats as
 * an N+1. Reading everything once and answering from memory removes them.
 */
class CachedSetting extends Setting
{
    /**
     * @var array<string, mixed>|null
     */
    private ?array $items = null;

    public function get(string|array|null $key = null, mixed $default = null): mixed
    {
        if (is_null($key)) {
            return $this->items();
        }

        if (is_array($key)) {
            return $this->getMany($key);
        }

        return data_get($this->items(), $key) ?? $default;
    }

    public function getMany(array $keys): array
    {
        $values = [];

        foreach ($keys as $key) {
            $value = $this->get($key);

            if ($value !== null) {
                $values[$key] = $value;
            }
        }

        return $values;
    }

    public function has(string $key): bool
    {
        return Arr::has($this->items(), $key);
    }

    public function set(array|string $key, mixed $value = null): void
    {
        parent::set($key, $value);

        // Saving the settings page must not be answered by the stale copy.
        $this->items = null;
    }

    /**
     * @return array<string, mixed>
     */
    private function items(): array
    {
        return $this->items ??= parent::get() ?? [];
    }
}
