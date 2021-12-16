@extends('layouts.layout')

@section('survey_content')
<div class="wide" id="all">
        <!-- HERO SLIDER SECTION-->
      <section class="text-white bg-cover bg-center primary-overlay overlay-dense" style="background: url('img/photogrid.jpg') repeat">
        <div class="overlay-content py-5">
          <div class="container py-4">
            <!-- Hero slider-->
            <div class="swiper-container homepage-slider">
              <div class="swiper-wrapper">
                <!-- Hero Slide-->
                <div class="swiper-slide h-auto mb-5">
                  <div class="row gy-5 h-100 align-items-center">
                    <div class="col-lg-5 text-lg-end">
                      <h1 class="text-uppercase">Online Qualifikationstool für J+S-Kurse</h1>
                      <ul class="list-unstyled text-uppercase fw-bold mb-0">
                        <li class="mb-2">Verwalte und Erstelle Qualifikationen für deinen J+S-Lagersport/Trekking-Kurs nach dem offiziellen Dokument.</li>
                      </ul>
                    </div>
                    <div class="col-lg-7"><img class="img-fluid" src="img/template-homepage.png" alt=""></div>
                  </div>
                </div>
              </div>
              {{-- <div class="swiper-pagination swiper-pagination-light"></div> --}}
            </div>
          </div>
        </div>
      </section>
      <!-- SERVICES SECTION-->
      <section class="py-5">
        <div class="container py-4">
          <div class="row gy-4">
            <!-- Service-->
            <div class="col-lg-4 col-md-6 block-icon-hover text-center">
              <div class="icon icon-outlined icon-outlined-primary icon-thin mx-auto mb-3"><i class="fas fa-desktop"></i></div>
              <h4 class="text-uppercase mb-3">Alles Online</h4>
              <p class="text-gray-600 text-sm">Erstelle deinen Kurs, importiere deine Leitende und Teilnehmende und erstelle die Qualifikationen, alles Online. Die Teilnehmenden und Leitenden füllen die Qualifikationen Online aus.</p>
            </div>
            <!-- Service-->
            <div class="col-lg-4 col-md-6 block-icon-hover text-center">
              <div class="icon icon-outlined icon-outlined-primary icon-thin mx-auto mb-3"><i class="fas fa-lock"></i></div>
              <h4 class="text-uppercase mb-3">Sicher</h4>
              <p class="text-gray-600 text-sm">Jeder Teilnehmende sieht nur die eigenen Eingaben der Qualifikation. Und auch die Leitenden sehen nur die Qualifikationen der zugewiesenen Teilnehmenden.</p>
            </div>
            <!-- Service-->
            <div class="col-lg-4 col-md-6 block-icon-hover text-center">
              <div class="icon icon-outlined icon-outlined-primary icon-thin mx-auto mb-3"><i class="fas fa-file-import"></i></div>
              <h4 class="text-uppercase mb-3">Import aus der Cevi-DB</h4>
              <p class="text-gray-600 text-sm">Importiere alle Leitende und Teilnehmende direkt aus der Cevi-DB inklusive Profilbild, Benutzernahmen und E-Mail.</p>
            </div>
            <!-- Service-->
            <div class="col-lg-4 col-md-6 block-icon-hover text-center">
              <div class="icon icon-outlined icon-outlined-primary icon-thin mx-auto mb-3"><i class="fas fa-id-card"></i></div>
              <h4 class="text-uppercase mb-3">Teilnehmendenprofil</h4>
              <p class="text-gray-600 text-sm">Hinterlasse einen Kommentar zu den Teilnehmenden oder lege ein Dokument ab. Teile die Teilnehmenden in drei Kategorien ein (grün, gelb, rot).</p>
            </div>
            <!-- Service-->
            <div class="col-lg-4 col-md-6 block-icon-hover text-center">
              <div class="icon icon-outlined icon-outlined-primary icon-thin mx-auto mb-3"><i class="fas fa-print"></i></div>
              <h4 class="text-uppercase mb-3">Ausdruckbar</h4>
              <p class="text-gray-600 text-sm">Falls gewünscht können die Qualifikationen auch inklusive Radar-Diagramm als PDF heruntergeladen werden.</p>
            </div>
            <!-- Service-->
            <div class="col-lg-4 col-md-6 block-icon-hover text-center">
              <div class="icon icon-outlined icon-outlined-primary icon-thin mx-auto mb-3"><i class="fas fa-mobile-alt"></i></div>
              <h4 class="text-uppercase mb-3">Mobile Tauglich</h4>
              <p class="text-gray-600 text-sm">Das Tool kann sowohl auf einem Laptop wie auch auf dem Smartphone bedient werden und kann so auch für eine rasche Rückmeldung genutzt werden.</p>
            </div>
          </div>
        </div>
      </section>
	  <!-- BLOCK SECTION-->
      <section class="py-5 bg-gray-200">
        <div class="container">
          <div class="row gy-4 align-items-center">
            <div class="col-md-6 text-center">
              <h2 class="text-uppercase">Kursleiter Dashboard</h2>
              <p class="lead mb-3">Erhalte den Überblick über deinen Kurs</p>
              <p class="mb-3">Behalte den Überblick über deine Teilnehmenden, die Qualifikationen und alle wichtigen Daten deines Kurses im Dashboard.</p>
            </div>
            <div class="col-md-6"><img class="img-fluid d-block mx-auto" src="img/template-easy-customize.png" alt="..."></div>
          </div>
        </div>
      </section>
	        <!-- BANNER SECTION-->
      <section class="py-5 bg-fixed bg-cover bg-center dark-overlay" style="background: url(img/fixed-background-2.jpg)">
        <div class="overlay-content">
          <div class="container py-4 text-white text-center">
            <div class="icon icon-outlined icon-lg mx-auto mb-4">
              <img src="img/cevi_pfad.svg" alt="Cevi Logo">
            </div>
            <h2 class="text-uppercase mb-3">Du willst deinen Kurs online qualifizieren lassen?</h2>
            <p class="lead mb-4">Registrier dich jetzt und erstell gleich deinen Kurs.</p><a class="btn btn-outline-light btn-lg" href="/register">Jetzt registrieren</a>
          </div>
        </div>
      </section>
      <!-- BLOCK SECTION-->
      <section class="py-5 bg-primary text-white">
        <div class="container">
          <div class="row align-items-center">
            <div class="col-md-6"><img class="img-fluid d-block mx-auto" src="img/template-easy-code.png" alt="..."></div>
            <div class="col-md-6 text-center">
              <h2 class="text-uppercase">Qualifikation online ausfüllen</h2>
              <p class="mb-3">Fülle die Qualifikation direkt Online aus und vergleiche als Leitende deine Einschätzung mit der Selbsteinschätzung der Teilnehmenden.</p>
            </div>
          </div>
        </div>
      </section>
      <!-- BLOCK SECTION-->
      <section class="py-5 bg-gray-200">
        <div class="container">
          <div class="row gy-4 align-items-center">
            <div class="col-md-6">
              <h2 class="text-uppercase">Sammle Rückmeldungen zu deinen Teilnehmenden</h2>
              <p class="mb-3">Hinterlege direkt einen Kommentar, wenn Teilnehmende auffällig wurden. Sammle die Rückmeldungen und verschaff dir als Kursleitung einen Überblick über deine Teilnehmende.<p>
            </div>
            <div class="col-md-6"><img class="img-fluid d-block mx-auto" src="img/template-mac.png" alt="..."></div>
          </div>
        </div>
      </section>
      

      
      <!-- GET IT-->
      <div class="bg-primary py-5 text-white">
        <div class="container text-center">
          <div class="row">
            <div class="col-lg-6 p-3">
              <h3 class="text-uppercase mb-0">Versuche es gleich selbst mit einem Test-Login aus.</h3>
            </div>
            <div class="col-lg-2 p-3">   <a class="btn btn-outline-light" href="/loginTN">Teilnehmende</a></div>
            <div class="col-lg-2 p-3">   <a class="btn btn-outline-light" href="/loginLeiter">Leitende</a></div>
            <div class="col-lg-2 p-3">   <a class="btn btn-outline-light" href="/loginKursleiter">Kursleitende</a></div>
          </div>
        </div>
      </div>
    </div>
@endsection
