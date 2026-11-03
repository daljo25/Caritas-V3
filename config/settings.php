<?php

use Outerweb\Settings\Models\Setting;

return [
    /**
     * The model used to read and write the settings.
     */
    'model' => Setting::class,

    /**
     * The database table where the settings are stored.
     */
    'database' => [
        'table' => 'settings',
    ],
];
