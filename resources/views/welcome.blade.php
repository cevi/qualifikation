@extends('layouts.layout')

@section('survey_content')
    <div class="wide" id="all">
        <!-- HERO SLIDER SECTION-->
        <section class="text-dark__white bg-cover bg-center primary-overlay overlay-dense"
                 style="background: url('img/photogrid.jpg') repeat">
            <div class="overlay-content py-5">
                <div class="container mx-auto px-[15px] py-4">
                    <!-- Hero slider-->
                    <div class="homepage-slider">
                        <!-- Hero Slide-->
                        <div class="h-auto mb-5">
                            <div class="flex flex-wrap -mx-[15px] h-full items-center">
                                <div class="px-[15px] w-full lg:w-5/12">
                                    <h1 class="mb-4 text-4xl font-extrabold leading-none tracking-tight md:text-5xl lg:text-6xl">
                                        Online Qualifikationstool für J+S-Kurse</h1>
                                        <p class="mb-6 text-lg font-normal lg:text-xl8">
                                            Verwalte und Erstelle Qualifikationen für deinen
                                                J+S-Lagersport/Trekking-Kurs nach dem offiziellen Dokument.
                                        </p>
                                </div>
                                <div class="px-[15px] w-full lg:w-7/12"><img class="max-w-full h-auto" src="img/template-homepage.png" alt="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- SERVICES SECTION-->
        <section class="py-5">
            <div class="container mx-auto px-[15px] py-4">
                <div class="flex flex-wrap -mx-[15px]">
                    <!-- Service-->
                    <div class="px-[15px] w-full lg:w-1/3 md:w-1/2 block-icon-hover text-center">
                        <div class="icon icon-outlined icon-outlined-primary icon-thin mx-auto mb-3"><i
                                class="fas fa-desktop"></i></div>
                                <h4 class="text-2xl font-bold dark:text-white">Alles Online</h4>
                                <p class="text-gray-600 dark:text-gray-200 text-sm">Erstelle deinen Kurs, importiere deine Leitende und
                            Teilnehmende und erstelle die Qualifikationen, alles Online. Die Teilnehmenden und Leitenden
                            füllen die Qualifikationen Online aus.</p>
                    </div>
                    <!-- Service-->
                    <div class="px-[15px] w-full lg:w-1/3 md:w-1/2 block-icon-hover text-center">
                        <div class="icon icon-outlined icon-outlined-primary icon-thin mx-auto mb-3"><i
                                class="fas fa-lock"></i></div>
                                <h4 class="text-2xl font-bold dark:text-white">Sicher</h4>
                                <p class="text-gray-600 dark:text-gray-200 text-sm">Jeder Teilnehmende sieht nur die eigenen Eingaben der
                            Qualifikation. Und auch die Leitenden sehen nur die Qualifikationen der zugewiesenen
                            Teilnehmenden. Nach Beendigung des Kurses werden alle Qualifikationen und Rückmeldungen gelöscht.</p>
                    </div>
                    <!-- Service-->
                    <div class="px-[15px] w-full lg:w-1/3 md:w-1/2 block-icon-hover text-center">
                        <div class="icon icon-outlined icon-outlined-primary icon-thin mx-auto mb-3"><i
                                class="fas fa-file-import"></i></div>
                                <h4 class="text-2xl font-bold dark:text-white">Import aus der Cevi-DB</h4>
                                <p class="text-gray-600 dark:text-gray-200 text-sm">Importiere alle Leitende und Teilnehmende direkt aus der
                            Cevi-DB inklusive Profilbild, Benutzernahmen und E-Mail.</p>
                    </div>
                    <!-- Service-->
                    <div class="px-[15px] w-full lg:w-1/3 md:w-1/2 block-icon-hover text-center">
                        <div class="icon icon-outlined icon-outlined-primary icon-thin mx-auto mb-3"><i
                                class="fas fa-id-card"></i></div>
                                <h4 class="text-2xl font-bold dark:text-white">Teilnehmendenprofil</h4>
                                <p class="text-gray-600 dark:text-gray-200 text-sm">Hinterlasse einen Kommentar zu den Teilnehmenden oder lege ein
                            Dokument ab. Teile die Teilnehmenden in drei Kategorien ein (grün, gelb, rot).</p>
                    </div>
                    <!-- Service-->
                    <div class="px-[15px] w-full lg:w-1/3 md:w-1/2 block-icon-hover text-center">
                        <div class="icon icon-outlined icon-outlined-primary icon-thin mx-auto mb-3"><i
                                class="fas fa-print"></i></div>
                                <h4 class="text-2xl font-bold dark:text-white">Ausdruckbar</h4>
                                <p class="text-gray-600 dark:text-gray-200 text-sm">Falls gewünscht können die Qualifikationen auch inklusive
                            Radar-Diagramm als PDF heruntergeladen werden.</p>
                    </div>
                    <!-- Service-->
                    <div class="px-[15px] w-full lg:w-1/3 md:w-1/2 block-icon-hover text-center">
                        <div class="icon icon-outlined icon-outlined-primary icon-thin mx-auto mb-3"><i
                                class="fas fa-mobile-alt"></i></div>
                                <h4 class="text-2xl font-bold dark:text-white">Mobile Tauglich</h4>
                                <p class="text-gray-600 dark:text-gray-200 text-sm">Das Tool kann sowohl auf einem Laptop wie auch auf dem
                            Smartphone bedient werden und kann so auch für eine rasche Rückmeldung genutzt werden.</p>
                    </div>
                </div>
            </div>
        </section>
        <!-- BLOCK SECTION-->
        <section class="py-5 bg-gray-400 text-dark__white">
            <div class="container mx-auto px-[15px]">
                <div class="flex flex-wrap -mx-[15px] items-center">
                    <div class="px-[15px] w-full md:w-1/2 text-center">
                        <h2 class="text-4xl font-bold">Kursleiter Dashboard</h2>
                        <p class="text-xl font-light mb-3">Erhalte den Überblick über deinen Kurs</p>
                        <p class="mb-3">Behalte den Überblick über deine Teilnehmenden, die Qualifikationen und alle
                            wichtigen Daten deines Kurses im Dashboard.</p>
                    </div>
                    <div class="px-[15px] w-full md:w-1/2"><img class="max-w-full h-auto block mx-auto" src="img/template-easy-customize.png"
                                               alt="..."></div>
                </div>
            </div>
        </section>

        <!-- BANNER SECTION-->
        <section class="py-5 bg-fixed bg-cover bg-center dark-overlay"
                 style="background: url(img/fixed-background-2.jpg)">
            <div class="overlay-content">
                <div class="container mx-auto px-[15px] py-4 text-white text-center">
                    <div class="icon icon-outlined icon-outlined-white icon-lg mx-auto mb-4">
                        <img src="img/cevi_pfad.svg" alt="Cevi Logo">
                    </div>
                    <h2 class="text-4xl font-bold text-white">Du willst deinen Kurs online qualifizieren lassen?</h2>
                    <p class="text-xl font-light mb-4">Registrier dich jetzt und erstell gleich deinen Kurs.</p><a
                        class="inline-block border border-gray-50 text-gray-50 hover:bg-gray-50 hover:text-gray-900 transition-colors px-4 py-2 text-xl leading-normal" href="/register">Jetzt registrieren</a>
                </div>
            </div>
        </section>
        <!-- SHOWCASE SECTION-->
        <section class="py-5" style="background: url(img/texture-bw.png) center center repeat;">
            <div class="container mx-auto px-[15px] py-4">
                <!-- Counters-->
                <div class="flex flex-wrap -mx-[15px] text-center text-gray-900" id="counterUp">
                    <div class="px-[15px] w-full lg:w-1/2 sm:w-1/2">
                        <div class="text-center">
                            <div class="icon-outlined text-gray-900 icon-lg mx-auto mb-3 icon-thin"><i
                                    class="fa-solid fa-campground"></i></div>
                            <h2 class="text-4xl counter font-bold" data-counter="{{$camp_counter}}">0</h2>
                            <h3 class="text-3xl font-bold">Kurse</h2>
                        </div>
                    </div>
                    <div class="px-[15px] w-full lg:w-1/2 sm:w-1/2">
                        <div class="text-center">
                            <div class="icon-outlined text-gray-900 icon-lg mx-auto mb-3 icon-thin"><i
                                    class="fa-solid fa-poll-h"></i></div>
                            <h2 class="text-4xl counter font-bold" data-counter="{{$survey_counter}}">0</h2>
                            <h3 class="text-3xl font-bold">Qualifikationen</h3>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- BLOCK SECTION-->
        <section class="py-5 bg-[var(--blue)] text-dark__white">
            <div class="container mx-auto px-[15px]">
                <div class="flex flex-wrap -mx-[15px] items-center">
                    <div class="px-[15px] w-full md:w-1/2"><img class="max-w-full h-auto block mx-auto" src="img/template-easy-code.png"
                                               alt="..."></div>
                    <div class="px-[15px] w-full md:w-1/2 text-center">
                        <h2 class="text-4xl font-bold">Qualifikation online ausfüllen</h2>
                        <p class="mb-3">Fülle die Qualifikation direkt Online aus und vergleiche als Leitende deine
                            Einschätzung mit der Selbsteinschätzung der Teilnehmenden.</p>
                    </div>
                </div>
            </div>
        </section>
        <!-- BLOCK SECTION-->
        <section class="py-5 bg-gray-400 text-dark__white">
            <div class="container mx-auto px-[15px]">
                <div class="flex flex-wrap -mx-[15px] items-center">
                    <div class="px-[15px] w-full md:w-1/2">
                        <h2 class="text-4xl font-bold">Sammle Rückmeldungen zu deinen Teilnehmenden</h2>
                        <p class="mb-3">Hinterlege direkt einen Kommentar, wenn Teilnehmende auffällig wurden. Sammle
                            die Rückmeldungen und verschaff dir als Kursleitung einen Überblick über deine Teilnehmende.
                        <p>
                    </div>
                    <div class="px-[15px] w-full md:w-1/2"><img class="max-w-full h-auto block mx-auto" src="img/template-mac.png" alt="...">
                    </div>
                </div>
            </div>
        </section>
        <!-- GET IT-->
        <div class="bg-[var(--blue)] py-5 text-dark__white">
            <div class="container mx-auto px-[15px] text-center">
                <div class="flex flex-wrap -mx-[15px]">
                    <div class="px-[15px] lg:w-1/2 w-full p-3">
                        <h3 class="text-3xl font-bold">Versuche es gleich selbst mit einem Test-Login aus.</h3>
                    </div>
                    <div class="px-[15px] lg:w-1/6 w-1/3 p-3"><a class="text-gray-100 hover:text-white border border-gray-200 hover:bg-gray-900 focus:ring-4 focus:outline-hidden focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 dark:border-gray-600 dark:text-gray-100 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-800" href="/loginKursleiter">Kursleitende</a></div>
                    <div class="px-[15px] lg:w-1/6 w-1/3 p-3"><a class="text-gray-100 hover:text-white border border-gray-200 hover:bg-gray-900 focus:ring-4 focus:outline-hidden focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 dark:border-gray-600 dark:text-gray-100 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-800" href="/loginLeiter">Leitende</a></div>
                    <div class="px-[15px] lg:w-1/6 w-1/3 p-3"><a class="text-gray-100 hover:text-white border border-gray-200 hover:bg-gray-900 focus:ring-4 focus:outline-hidden focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center me-2 mb-2 dark:border-gray-600 dark:text-gray-100 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-800" href="/loginTN">Teilnehmende</a></div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    @vite('resources/js/pages/counter-up.js')
@endpush
