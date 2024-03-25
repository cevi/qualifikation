<section>
    @if($header)
        <div class="breadcrumb-holder">
            <div class="container-fluid">
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
    <div class="container-fluid">
        <header>
            <h3 class="text-3xl font-bold dark:text-white">{{$title}} {{$subtitle}}
                @if(isset($help['content']))
                    <x-help-button/>
                @endif
            </h3>
        </header>

        <x-drawer-content :help="$help"/>
    </div>
</section>
