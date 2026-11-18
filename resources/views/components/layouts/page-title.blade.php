<section>
    @if($header)
        <div class="breadcrumb-holder">
            <div>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/admin">Dashboard</a></li>
                    @if(isset($help['main_title']))
                        <li class="breadcrumb-item"><a href="{{$help['main_route']}}">{{$help['main_title']}}</a></li>
                    @endif
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ul>
            </div>
        </div>
    @endif
    <div>
        {{-- Optional <x-slot:actions>: Knöpfe rechts auf Höhe des Titels (auf dem Handy darunter) --}}
        <header @class(['flex flex-wrap items-center justify-between gap-x-4 gap-y-2' => isset($actions)])>
            <h1 class="text-3xl font-bold dark:text-white">{{$title}} {{$subtitle}}
                @if(isset($help['content']))
                    <x-layouts.help-button/>
                @endif
            </h1>
            @isset($actions)
                <div class="flex flex-wrap items-center gap-2">{{ $actions }}</div>
            @endisset
        </header>

        {{-- Nur mit Hilfe-Knopf: ohne ihn initialisiert Flowbite die Leiste nicht und meldet einen Fehler. --}}
        @if(isset($help['content']))
            <x-layouts.drawer-content :help="$help"/>
        @endif
    </div>
</section>
