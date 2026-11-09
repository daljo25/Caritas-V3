<?php

namespace App\Filament\Concerns;

use AmjadIqbal\FilamentRecordPdf\RecordPdf;
use AmjadIqbal\FilamentRecordPdf\Support\PdfRenderOptions;
use AmjadIqbal\FilamentRecordPdf\Support\PreviewStore;
use AmjadIqbal\FilamentRecordPdf\Support\SafePath;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;

/**
 * Abre en el visor de PDF de filament-record-pdf un PDF generado fuera de un registro
 * (listados por año, por día, etc.), donde el plugin no puede usar su acción de registro.
 */
trait HasGeneratedPdfPreview
{
    protected function previewGeneratedPdf(): Action
    {
        return Action::make('previewGeneratedPdf')
            ->modalWidth(Width::FiveExtraLarge)
            ->modalHeading(fn (array $arguments): string => $arguments['title'] ?? 'PDF')
            ->modalContent(fn (array $arguments) => view('filament-record-pdf::preview', [
                'url' => $arguments['inline'],
                'title' => $arguments['title'] ?? 'PDF',
            ]))
            ->modalSubmitAction(false)
            ->modalCancelActionLabel(__('filament-record-pdf::record-pdf.preview.close'))
            ->extraModalFooterActions(fn (array $arguments): array => [
                Action::make('downloadGeneratedPdf')
                    ->label(__('filament-record-pdf::record-pdf.download.label'))
                    ->icon(Heroicon::OutlinedArrowDownTray)
                    ->color('gray')
                    ->url($arguments['download']),
            ])
            ->action(fn () => null);
    }

    protected function openPdfPreview(string $html, string $filename, string $format = 'a4', bool $landscape = false): void
    {
        $pdf = RecordPdf::render($html, new PdfRenderOptions(
            driver: (string) config('filament-record-pdf.driver', 'dompdf'),
            format: $format,
            landscape: $landscape,
        ));

        $filename = SafePath::filename($filename);

        $urls = PreviewStore::put(
            $pdf,
            $filename,
            Filament::auth()->id(),
            Filament::getCurrentOrDefaultPanel()?->getAuthGuard(),
        );

        $this->replaceMountedAction('previewGeneratedPdf', [
            'inline' => $urls['inline'],
            'download' => $urls['download'],
            'title' => $filename,
        ]);
    }
}
