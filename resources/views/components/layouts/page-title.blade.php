<section>
    @if($header)
        <div class="breadcrumb-holder">
            <div class="px-[15px] md:px-12">
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
    <div class="px-[15px] md:px-12">
        <header>
            <h3 class="text-3xl font-bold dark:text-white">{{$title}} {{$subtitle}}
                @if(isset($help['content']))
                    <x-layouts.help-button/>
                @endif
            </h3>
        </header>

        {{-- Nur mit Hilfe-Knopf: ohne ihn initialisiert Flowbite die Leiste nicht und meldet einen Fehler. --}}
        @if(isset($help['content']))
            <x-layouts.drawer-content :help="$help"/>
        @endif
    </div>
</section>
