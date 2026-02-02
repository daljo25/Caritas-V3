<x-filament::page>
    <div class="flex flex-col lg:flex-row gap-6">
        <!-- Tarjeta del formulario -->
        <div class="flex-1 max-w-md md:max-w-lg p-6 border border-gray-200 rounded-lg shadow dark:border-gray-700">
            <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                Enviar Mensajes de WhatsApp
            </h5>
            <form>
                {{ $this->form }}
            </form>
        </div>
        <!-- Tarjeta de enlaces generados -->
        <div class="flex-1 p-6 border border-gray-200 rounded-lg shadow dark:border-gray-700">
            <h5 class="mb-4 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                Enlaces Generados
            </h5>
            @if (session('links'))
                <ul class="space-y-3">
                    @foreach (session('links') as $link)
                        <li x-data="{ sent: false }"
                            class="flex items-center justify-between gap-4 p-4 border rounded-lg dark:border-gray-700">
                            <!-- Nombre + Teléfono -->
                            <div class="flex items-center gap-2 text-sm">
                                <span class="font-semibold text-gray-900 dark:text-white">
                                    {{ $link['nombre'] }}
                                </span>
                                <span class="text-gray-500">
                                    ({{ $link['telefono'] }})
                                </span>
                            </div>

                            <!-- Botón Filament -->
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
                <p class="text-gray-500">No se han generado enlaces aún.</p>
            @endif
        </div>
    </div>
</x-filament::page>
