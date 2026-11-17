<?php

use App\Filament\Resources\AidResource;

/**
 * Extrae del recibo los literales contra los que compara `$record->type`.
 *
 * @return list<string>
 */
$receiptConcepts = function (): array {
    $view = file_get_contents(resource_path('views/pdf/receipt.blade.php'));

    preg_match_all("/\\\$record->type\s*===\s*'((?:\\\\'|[^'])*)'/", $view, $matches);

    return array_values(array_map(
        fn (string $concept): string => str_replace("\\'", "'", $concept),
        $matches[1],
    ));
};

/**
 * @return list<string>
 */
$formConcepts = fn (): array => array_keys(array_merge(...array_values(AidResource::typeOptions())));

it('marca en el recibo cada opción del formulario', function () use ($formConcepts, $receiptConcepts) {
    $missingInReceipt = array_diff($formConcepts(), $receiptConcepts());

    expect($missingInReceipt)->toBe(
        [],
        'Opciones sin casilla en pdf.receipt: '.implode(' | ', $missingInReceipt),
    );
});

it('solo compara en el recibo conceptos que existen en el formulario', function () use ($formConcepts, $receiptConcepts) {
    $missingInForm = array_diff($receiptConcepts(), $formConcepts());

    expect($missingInForm)->toBe(
        [],
        'Conceptos del recibo que no existen en AidResource::typeOptions(): '.implode(' | ', $missingInForm),
    );
});
