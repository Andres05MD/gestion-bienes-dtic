<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-brand-purple/20 rounded-2xl">
                    <x-mary-icon name="o-clock" class="w-8 h-8 text-brand-lila" />
                </div>
                <div>
                    <h2 class="font-black text-3xl text-gray-800 dark:text-white leading-tight tracking-tight drop-shadow-md">
                        {{ __('Historial de Actividad') }}
                    </h2>
                    <p class="text-sm font-medium text-gray-500 dark:text-dark-text uppercase tracking-widest mt-1">Registro de Auditoría del Sistema</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-dark-850 overflow-hidden shadow-2xl sm:rounded-2xl border border-dark-800">
                <div class="p-6">

                    <!-- Barra de Búsqueda y Filtros -->
                    <form method="GET" action="{{ route('activity-log.index') }}" class="mb-8 flex flex-col xl:flex-row gap-4">
                        <!-- Búsqueda -->
                        <div class="xl:w-1/3 relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar por descripción, usuario..." class="block w-full pl-11 pr-4 h-12 bg-dark-900 border-none rounded-2xl text-white placeholder-gray-500 focus:ring-2 focus:ring-brand-purple/20 transition-all text-sm" />
                        </div>

                        <!-- Filtros -->
                        <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                            <x-select-premium
                                name="log_name"
                                placeholder="Módulo"
                                icon="o-funnel"
                                :options="$logNames->map(fn($l) => ['value' => $l, 'label' => ucfirst(str_replace('-', ' ', $l))])->toArray()"
                                :value="request('log_name')"
                            />

                            <x-select-premium
                                name="event"
                                placeholder="Evento"
                                icon="o-bolt"
                                :options="[
                                    ['value' => 'created', 'label' => 'Creado'],
                                    ['value' => 'updated', 'label' => 'Actualizado'],
                                    ['value' => 'deleted', 'label' => 'Eliminado'],
                                ]"
                                :value="request('event')"
                            />

                            <div class="flex gap-2">
                                <button type="submit" class="shrink-0 p-4 bg-brand-purple/20 text-brand-lila rounded-2xl hover:bg-brand-purple/30 transition-colors shadow-lg shadow-brand-purple/5" title="Buscar">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </button>
                                @if(request()->anyFilled(['buscar', 'log_name', 'event']))
                                    <a href="{{ route('activity-log.index') }}" class="shrink-0 p-4 bg-rose-500/10 text-rose-400 rounded-2xl hover:bg-rose-500/20 transition-colors shadow-lg shadow-rose-500/5 flex items-center" title="Limpiar Filtros">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>

                    <div class="overflow-x-auto rounded-xl">
                        <table class="min-w-full">
                            <thead>
                                <tr class="bg-dark-800/50">
                                    <th scope="col" class="px-6 py-5 text-left text-xs font-bold text-dark-text uppercase tracking-widest">Fecha</th>
                                    <th scope="col" class="px-6 py-5 text-left text-xs font-bold text-dark-text uppercase tracking-widest">Usuario</th>
                                    <th scope="col" class="px-6 py-5 text-left text-xs font-bold text-dark-text uppercase tracking-widest">Evento</th>
                                    <th scope="col" class="px-6 py-5 text-left text-xs font-bold text-dark-text uppercase tracking-widest">Módulo</th>
                                    <th scope="col" class="px-6 py-5 text-left text-xs font-bold text-dark-text uppercase tracking-widest">Descripción</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-dark-800">
                                @forelse ($activities as $activity)
                                    @php
                                        $eventColor = match($activity->event) {
                                            'created' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                                            'updated' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                                            'deleted' => 'bg-rose-500/10 text-rose-400 border-rose-500/20',
                                            default => 'bg-gray-500/10 text-gray-400 border-gray-500/20',
                                        };
                                        $eventLabel = match($activity->event) {
                                            'created' => 'Creado',
                                            'updated' => 'Actualizado',
                                            'deleted' => 'Eliminado',
                                            default => $activity->event ?? 'N/A',
                                        };
                                        $eventIcon = match($activity->event) {
                                            'created' => 'o-plus-circle',
                                            'updated' => 'o-pencil-square',
                                            'deleted' => 'o-trash',
                                            default => 'o-information-circle',
                                        };
                                        $moduleColor = match($activity->log_name) {
                                            'bienes' => 'bg-brand-purple/10 text-brand-lila',
                                            'bienes-externos' => 'bg-amber-500/10 text-amber-400',
                                            'transferencias' => 'bg-blue-500/10 text-blue-400',
                                            'desincorporaciones' => 'bg-rose-500/10 text-rose-400',
                                            'distribuciones' => 'bg-emerald-500/10 text-emerald-400',
                                            default => 'bg-gray-500/10 text-gray-400',
                                        };
                                    @endphp
                                    <tr class="hover:bg-dark-800/30 transition-all duration-300">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-bold text-white">{{ $activity->created_at->diffForHumans() }}</div>
                                            <div class="text-[10px] text-gray-500 font-medium">{{ $activity->created_at->format('d') }} {{ Str::upper(substr(str_replace('.', '', $activity->created_at->translatedFormat('M')), 0, 3)) }}, {{ $activity->created_at->format('Y H:i') }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center gap-3">
                                                <div class="h-8 w-8 rounded-full bg-linear-to-tr from-brand-purple to-brand-lila flex items-center justify-center text-white font-bold text-xs shadow-lg">
                                                    {{ substr($activity->causer?->name ?? '?', 0, 1) }}
                                                </div>
                                                <span class="text-sm font-bold text-white">{{ $activity->causer?->name ?? 'Sistema' }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 text-[10px] font-black rounded-xl border {{ $eventColor }} uppercase tracking-widest">
                                                <x-mary-icon name="{{ $eventIcon }}" class="w-3.5 h-3.5" />
                                                {{ $eventLabel }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-3 py-1.5 text-[10px] font-black rounded-xl {{ $moduleColor }} uppercase tracking-widest">
                                                {{ ucfirst(str_replace('-', ' ', $activity->log_name ?? 'general')) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-dark-text max-w-md truncate" title="{{ $activity->description }}">
                                            {{ $activity->description }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center gap-3">
                                                <svg class="w-12 h-12 text-dark-800" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                <p class="text-gray-500 dark:text-gray-400 font-medium">
                                                    @if(request()->anyFilled(['buscar', 'log_name', 'event']))
                                                        No se encontraron registros con los filtros aplicados.
                                                    @else
                                                        No hay actividad registrada aún.
                                                    @endif
                                                </p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $activities->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
