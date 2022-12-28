<p align="center"><img src="https://quali.cevi.tools/img/logo.svg" width="400"></p>
<p align="center"><img src="https://quali.cevi.tools/img/photogrid.jpg"></p>

## Qualifikations-Tool für J+S-Kurse

Das Qualifikations-Tool bietet viele Funktionen, welche deinen Qualifikations-Prozess bei einem J+S-Kurs vereinfachen:

- Erstelle deinen Kurs, importiere deine Leitende und Teilnehmende und erstelle die Qualifikationen, alles Online. Die Teilnehmenden und Leitenden füllen die Qualifikationen Online aus.
- Jeder Teilnehmende sieht nur die eigenen Eingaben der Qualifikation. Und auch die Leitenden sehen nur die Qualifikationen der zugewiesenen Teilnehmenden.
- Importiere alle Leitende und Teilnehmende direkt aus der Cevi-DB inklusive Profilbild, Benutzernahmen und E-Mail.
- Hinterlasse einen Kommentar zu den Teilnehmenden oder lege ein Dokument ab. Teile die Teilnehmenden in drei Kategorien ein (grün, gelb, rot).
- Falls gewünscht können die Qualifikationen auch inklusive Radar-Diagramm als PDF heruntergeladen werden.
- Das Tool kann sowohl auf einem Laptop wie auch auf dem Smartphone bedient werden und kann so auch für eine rasche Rückmeldung genutzt werden.


## Lokale Installation

Das Tool ist ein PHP-Projekt basiernd auf dem Framework [Laravel](https://laravel.com/). Um es lokal auszuführen brauchst du einen [Docker Container](https://docs.docker.com/).

Um das Tool lokal bei dir benutzen zu können musst du den Quellcode herunterladen und mittels [Laravel Sail](https://laravel.com/docs/9.x/sail) starten:

```bash
# clone the GitRepo
git clone https://github.com/cevi/qualifikation
cd qualifikation

# install the dependencies
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v $(pwd):/var/www/html \
    -w /var/www/html \
    laravelsail/php81-composer:latest \
    composer install --ignore-platform-reqs
    
cp .env.example .env

# launch the application
./vendor/bin/sail up

# initialize the database
./vendor/bin/sail artisan migrate --seed
```
Anschliessend kannst du dein Tool unter [http://localhost](http://localhost) aufrufen.

## Run in Production

```bash
docker-compose -f docker-compose.prod.yml up -d --build
```

Anschliessend musst du einmalig den folgenden Command laufen lassen,
damit die Datenbank migriert und initialisiert wird:

```bash
docker exec -it qualifikation_php_1 composer install
docker exec -it qualifikation_php_1 php artisan migrate --seed
```

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
