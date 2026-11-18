{{--
    Menü unten auf dem Handy (unter md). Teilnehmende: Übersicht, Quali, Konto. Leitende und Kursleitung: Übersicht,
    Rückmeldungen ("Feedback", passt in die Spalte), Neue Rückmeldung, Kursadmin bzw. Konto, Mehr (öffnet das Menü oben, resources/js/bottom-nav.js).
    $surveyLink kommt aus dem View-Composer in AppServiceProvider.
--}}
@php
    $user = auth()->user();
    $item = 'flex flex-col items-center gap-0.5 py-2 text-[11px] text-gray-600 hover:text-blue-700 dark:text-gray-400 dark:hover:text-white';
    $active = 'text-blue-700 font-semibold dark:text-white';
    $items = [['label' => 'Übersicht', 'icon' => 'fa-house', 'url' => url('/home'), 'active' => request()->is('home')]];
    if ($user->isTeilnehmer()) {
        if ($surveyLink) {
            $items[] = ['label' => 'Quali', 'icon' => 'fa-clipboard-check', 'url' => $surveyLink, 'active' => request()->routeIs('survey.*')];
        }
    } elseif ($user->camp) {
        $items[] = ['label' => 'Feedback', 'title' => 'Rückmeldungen', 'icon' => 'fa-comments', 'url' => url('/posts'), 'active' => request()->is('posts*') && ! request()->has('neu')];
        $items[] = ['label' => 'Neu', 'icon' => 'fa-plus', 'url' => url('/posts').'?neu=1', 'active' => request()->is('posts') && request()->has('neu'), 'primary' => true, 'title' => 'Neue Rückmeldung'];
    }
    if ($user->isCampleader() && $user->camp) {
        $items[] = ['label' => 'Kursadmin', 'icon' => 'fa-chart-line', 'url' => url('/admin'), 'active' => request()->is('admin*')];
    } else {
        $items[] = ['label' => 'Konto', 'icon' => 'fa-user', 'url' => route('home.user', $user->slug), 'active' => request()->routeIs('home.user')];
    }
@endphp
<nav id="bottom-navigation" aria-label="Schnellmenü"
     class="fixed inset-x-0 bottom-0 z-40 border-t border-gray-200 bg-gray-100 pb-[env(safe-area-inset-bottom)] md:hidden dark:border-gray-700 dark:bg-gray-800">
    <ul class="m-0 flex list-none p-0">
        @foreach ($items as $entry)
            {{-- Der runde Knopf ist schmal, damit die anderen Beschriftungen Platz haben --}}
            <li @class(['min-w-0', 'w-16 flex-none' => $entry['primary'] ?? false, 'flex-1' => ! ($entry['primary'] ?? false)])>
                <a href="{{ $entry['url'] }}" @class([$item, $active => $entry['active']]) @if ($entry['active']) aria-current="page" @endif
                   @isset($entry['title']) title="{{ $entry['title'] }}" @endisset
                   @if ($entry['primary'] ?? false) aria-label="{{ $entry['title'] }}" @endif>
                    @if ($entry['primary'] ?? false)
                        <span class="-mt-5 flex size-11 items-center justify-center rounded-full bg-blue-700 text-white shadow-md dark:bg-blue-600" aria-hidden="true">
                            <i class="fas {{ $entry['icon'] }} text-xl"></i>
                        </span>
                    @else
                        <i class="fas {{ $entry['icon'] }} text-lg" aria-hidden="true"></i>
                    @endif
                    <span class="max-w-full truncate">{{ $entry['label'] }}</span>
                </a>
            </li>
        @endforeach
        @unless ($user->isTeilnehmer())
            <li class="min-w-0 flex-1">
                <button type="button" data-bottom-nav-more aria-controls="mobile-menu-2" class="{{ $item }} w-full">
                    <i class="fas fa-bars text-lg" aria-hidden="true"></i>
                    <span>Mehr</span>
                </button>
            </li>
        @endunless
    </ul>
</nav>
