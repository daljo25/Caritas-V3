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
    protected static ?int $navigationSort = 2   ;
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
                ->helperText('Usa usar los simbolos para formatear tu texto como en WhatsApp: *negrita*, _cursiva_, ~tachado~ y ``monoespaciado``.')
                ->required(),
        ];
    }



    public function generateLinks()
    {
        if (empty($this->selectedBeneficiaries) || empty($this->message)) {
            Notification::make()
                ->title('Error')
                ->body('Debes seleccionar beneficiarios y escribir un mensaje.')
                ->danger()
                ->send();
            return;
        }

        $links = [];
        foreach ($this->selectedBeneficiaries as $beneficiaryId) {
            $beneficiary = Beneficiary::find($beneficiaryId);
            if ($beneficiary && $beneficiary->phone) {
                $phone = preg_replace('/\D/', '', $beneficiary->phone); // Elimina caracteres no numéricos
                $message = urlencode($this->message);
                $links[] = "https://wa.me/+34{$phone}?text={$message}";
            }
        }

        session()->flash('links', $links);

        Notification::make()
            ->title('Enlaces Generados')
            ->body('Los enlaces de WhatsApp se han generado correctamente.')
            ->success()
            ->send();
    }


    protected function getHeaderActions(): array
    {
        return [
            Action::make('generateLinks')
                ->label('Generar Links')
                ->color('success')
                ->action('generateLinks'),
        ];
    }
}
