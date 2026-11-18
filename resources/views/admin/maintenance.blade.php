@extends('layouts.admin', ['withoutNav' => count($pending) > 0])

@section('content')
    <div class="px-[15px] md:px-12">
        <x-layouts.page-title :title="$title" :help="$help"/>

        @if (session('maintenance_output'))
            <div class="panel">
                <h2 class="panel-header m-0 text-base font-semibold text-gray-900 dark:text-white">Ausgabe</h2>
                <pre class="panel-body m-0 overflow-x-auto text-sm whitespace-pre-wrap text-gray-900 dark:text-white">{{ session('maintenance_output') }}</pre>
            </div>
        @endif

        <div class="grid gap-x-8 lg:grid-cols-2">
            <section class="panel">
                <h2 class="panel-header m-0 text-lg font-semibold text-gray-900 dark:text-white">Datenbank-Migrationen</h2>
                <div class="panel-body">
                    @if (count($pending) > 0)
                        <p class="mb-2">{{ count($pending) }} offene {{ count($pending) === 1 ? 'Migration' : 'Migrationen' }}:</p>
                        <p class="mb-2 text-sm text-gray-500 dark:text-gray-400">Bis sie ausgeführt sind, leitet die App Admins hierher, und das Menü ist ausgeblendet.</p>
                        <ul class="mb-4 list-disc ps-5 font-mono text-sm break-all">
                            @foreach ($pending as $migration)
                                <li>{{ $migration }}</li>
                            @endforeach
                        </ul>
                    @else
                        <p class="mb-4">Alle Migrationen sind ausgeführt.</p>
                    @endif
                    <form method="POST" action="{{ route('admin.maintenance.migrate') }}">
                        @csrf
                        <button type="submit" class="button-primary disabled:cursor-not-allowed disabled:opacity-50" @disabled(count($pending) === 0)>Migrationen ausführen</button>
                    </form>
                </div>
            </section>

            <section class="panel">
                <h2 class="panel-header m-0 text-lg font-semibold text-gray-900 dark:text-white">Cache</h2>
                <div class="panel-body">
                    <p class="mb-4">
                        Leert Konfigurations-, Routen-, View- und Anwendungs-Cache (<code>optimize:clear</code>),
                        z. B. nach einem Update, wenn Änderungen nicht sichtbar werden.
                    </p>
                    <form method="POST" action="{{ route('admin.maintenance.clear-cache') }}">
                        @csrf
                        <button type="submit" class="button-secondary">Cache leeren</button>
                    </form>
                </div>
            </section>
        </div>
    </div>
@endsection
