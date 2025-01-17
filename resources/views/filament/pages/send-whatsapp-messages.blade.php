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
        <div class="flex-1 max-w p-6 border border-gray-200 rounded-lg shadow dark:border-gray-700">
            <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                Enlaces Generados
            </h5>
            @if (session('links'))
            <ul class="list-disc pl-4 space-y-2">
                @foreach (session('links') as $link)
                <li>
                    <a href="{{ $link }}" target="_blank" class="text-blue-500 underline">{{ $link }}</a>
                </li>
                @endforeach
            </ul>
            @else
            <p class="text-gray-500">No se han generado enlaces aún.</p>
            @endif
        </div>
    </div>
</x-filament::page>
