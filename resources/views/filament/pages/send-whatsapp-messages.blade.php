<x-filament::page>
    <div style="display: flex; flex-wrap: wrap; gap: 1.5rem; align-items: flex-start;">
        <div style="flex: 1 1 24rem; max-width: 32rem;">
            <x-filament::section heading="Enviar Mensajes de WhatsApp">
                <form>
                    {{ $this->form }}
                </form>
            </x-filament::section>
        </div>

        <div style="flex: 1 1 24rem;">
            <x-filament::section heading="Enlaces Generados">
                @if (session('links'))
                    <ul style="display: flex; flex-direction: column; gap: 0.75rem; margin: 0; padding: 0; list-style: none;">
                        @foreach (session('links') as $link)
                            <li x-data="{ sent: false }"
                                style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; padding: 1rem; border: 1px solid rgba(128, 128, 128, 0.25); border-radius: 0.5rem;">
                                <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.875rem;">
                                    <span style="font-weight: 600;">
                                        {{ $link['nombre'] }}
                                    </span>
                                    <span style="opacity: 0.7;">
                                        ({{ $link['telefono'] }})
                                    </span>
                                </div>

                                <div>
                                    <template x-if="!sent">
                                        <x-filament::button tag="a" href="{{ $link['url'] }}" target="_blank"
                                            color="success" size="sm" icon="tabler-brand-whatsapp" @click="sent = true">
                                            Enviar
                                        </x-filament::button>
                                    </template>

                                    <template x-if="sent">
                                        <x-filament::button color="info" size="sm" icon="tabler-checks" icon-position="after" disabled>
                                            Enviado
                                        </x-filament::button>
                                    </template>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p style="opacity: 0.7;">No se han generado enlaces aún.</p>
                @endif
            </x-filament::section>
        </div>
    </div>
</x-filament::page>
