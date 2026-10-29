{{-- Mit Menü unten (eingeloggt, Handy) steht der Footer darüber; die Seite hält unten Platz frei (layouts/*.blade.php) --}}
<footer class="main-footer @auth max-md:bottom-[calc(4.5rem+env(safe-area-inset-bottom))]! @endauth">
    <div class="px-[15px] md:px-12">
        <div class="flex flex-wrap -mx-[15px]">
            <div class="px-[15px] w-full sm:w-1/3 text-left">
                <p>Finde weitere Lösungen auf <a href="https://www.cevi.tools" target="_blank">cevi.tools</a> / <a href="https://cevi.tools/datenschutzerklaerung.html"  target="_blank">Datenschutzerklärung</a></p>
            </div>
            <div class="px-[15px] w-full sm:w-1/3 text-center">
                <p>Made by Amirli, {{config('app.version')}}</p>
            </div>
            <div class="px-[15px] w-full sm:w-1/3 text-right">
                <p>Anleitungen und Erklärungen sind auf unserem <a
                        href="https://github.com/cevi/qualifikation/wiki">Wiki</a></p>
            </div>
        </div>
    </div>
</footer>
