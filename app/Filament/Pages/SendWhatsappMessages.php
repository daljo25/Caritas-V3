<?php

namespace App\Filament\Pages;

use App\Filament\Forms\Components\WhatsAppEditor;
use Filament\Pages\Page;
use App\Models\Beneficiary;
use Filament\Forms\Components\Select;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;

class SendWhatsappMessages extends Page
{
    protected static ?string $navigationIcon = 'tabler-brand-whatsapp'; // Cambiado a Tabler Icons
    protected static ?string $title = 'Enviar WhatsApp';
    protected static string $view = 'filament.pages.send-whatsapp-messages';
    protected static ?string $navigationGroup = 'Citas';
    protected static ?int $navigationSort = 2;
    public $selectedBeneficiaries = [];
    public $message;

    protected function getFormSchema(): array
    {
        return [
            Select::make('selectedBeneficiaries')
                ->label('Seleccionar Beneficiarios')
                ->multiple()
                ->options(
                    Beneficiary::whereNotNull('phone')
                        ->get()
                        ->mapWithKeys(fn($beneficiary) => [
                            $beneficiary->id => "{$beneficiary->name} ({$beneficiary->phone})"
                        ])
                        ->toArray()
                )
                ->searchable()
                ->getSearchResultsUsing(function (string $query) {
                    return Beneficiary::query()
                        ->where('name', 'like', "%{$query}%")
                        ->orWhere('phone', 'like', "%{$query}%")
                        ->get()
                        ->mapWithKeys(fn($beneficiary) => [
                            $beneficiary->id => "{$beneficiary->name} ({$beneficiary->phone})"
                        ])
                        ->toArray();
                })
                ->required(),
            Textarea::make('message')
                ->label('Mensaje')
                ->helperText('Usa usar los simbolos para formatear tu texto: *negrita*, _cursiva_, ~tachado~ y ``monoespaciado``.')
                ->required(),
        ];
    }


    // Funcion para Generar los links de WhatsApp
    public function generateLinks()
    {
        // Validación básica
        if (empty($this->selectedBeneficiaries) || empty($this->message)) {
            Notification::make()
                ->title('Error')
                ->body('Debes seleccionar beneficiarios y escribir un mensaje.')
                ->danger()
                ->send();

            return;
        }

        // Obtener beneficiarios seleccionados de forma eficiente (evita N+1)
        $beneficiaries = Beneficiary::whereIn('id', $this->selectedBeneficiaries)
            ->whereNotNull('phone')
            ->get();

        $links = [];

        foreach ($beneficiaries as $beneficiary) {
            // Normalizar teléfono (eliminar no numéricos)
            $phone = preg_replace('/\D/', '', $beneficiary->phone);
            if (!str_starts_with($phone, '34')) {
                $phone = '34' . $phone;
            }

            // Construir la URL correctamente soportando emojis y caracteres especiales
            $query = http_build_query(
                [
                    'phone' => $phone,
                    'text'  => $this->message,
                ],
                '',
                '&',
                PHP_QUERY_RFC3986
            );

            $url = "https://api.whatsapp.com/send?{$query}";

            // Guardar datos estructurados para Blade
            $links[] = [
                'id'       => $beneficiary->id,
                'nombre'   => $beneficiary->name,
                'telefono' => $beneficiary->phone,
                'url'      => $url,
            ];
        }

        // Guardar en sesión
        session()->flash('links', $links);

        // Notificación de éxito
        Notification::make()
            ->title('Enlaces Generados')
            ->body('Los mensajes de WhatsApp están listos para enviar.')
            ->success()
            ->send();
    }

    // Funcion para limpiar los links generados
    public function clearLinks()
    {
        session()->forget('links');
        $this->reset([
            'selectedBeneficiaries',
            'message',
        ]);

        Notification::make()
            ->title('Limpieza Exitosa')
            ->body('Los enlaces y el formulario han sido limpiados.')
            ->success()
            ->send();
    }


    protected function getHeaderActions(): array
    {
        return [
            // boton de limpiar
            Action::make('clearLinks')
                ->label('Limpiar')
                ->color('gray')
                ->icon('tabler-trash')
                ->requiresConfirmation()
                ->action('clearLinks'),
            //boton de generar links
            Action::make('generateLinks')
                ->label('Generar Links')
                ->color('success')
                ->action('generateLinks')
                ->icon('tabler-brand-whatsapp'),
        ];
    }
}
