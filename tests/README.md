# Tests

Übersicht über die automatisierten Tests von Qualitool: wie man sie ausführt, wie sie aufgebaut sind und was sie absichern.

> **Beim Hinzufügen eines Tests bitte diese Datei ergänzen**, siehe [Neuen Test schreiben](#neuen-test-schreiben).

Stand 25.10.2026: **270 Testfälle in 36 Dateien**, Laufzeit rund 10 Sekunden.
Aktuelle Zahlen: `vendor/bin/phpunit --list-tests`

## Inhalt

- [Tests ausführen](#tests-ausführen)
- [Aufbau](#aufbau)
- [Übersicht aller Tests](#übersicht-aller-tests)
- [JS-Tests (Vitest)](#js-tests-vitest)
- [Browser-Tests (Dusk)](#browser-tests-dusk)
- [Fachliche Regeln, die die Tests festhalten](#fachliche-regeln-die-die-tests-festhalten)
- [Neuen Test schreiben](#neuen-test-schreiben)

---

## Tests ausführen

```bash
# Alle Tests (im Container)
vendor/bin/phpunit

# Von aussen über Sail
./vendor/bin/sail artisan test

# Eine Datei oder einzelne Tests
vendor/bin/phpunit tests/Feature/SurveyFlowTest.php
vendor/bin/phpunit --filter test_closing_first_round

# JS-Tests (Vitest), siehe "JS-Tests (Vitest)"
npm test
npm run test:watch

# Code-Stil prüfen bzw. korrigieren
vendor/bin/pint --test
vendor/bin/pint
```

- **Datenbank:** Die Tests laufen immer gegen SQLite im Arbeitsspeicher. Die Datenbank aus der `.env` wird nie verwendet, `tests/TestCase.php` bricht sonst ab. Das schützt zum Beispiel bei aktivem Config-Cache (`php artisan config:clear`).
- **CI:** [`.github/workflows/tests.yml`](../.github/workflows/tests.yml) führt die Jobs `pint`, `phpunit` und `vitest` bei jedem Push auf `develop` und bei jedem Pull Request aus. Der Prod-Build ([`prod_build.yml`](../.github/workflows/prod_build.yml)) startet nur, wenn alle grün sind.
- **Browser-Tests (Dusk):** siehe [Browser-Tests](#browser-tests-dusk). Laufen nicht in der CI und nicht mit `vendor/bin/phpunit`.
- **Code-Stil:** Laravel Pint mit den Regeln aus [`pint.json`](../pint.json).
  - Der Git-Hook [`.githooks/pre-commit`](../.githooks/pre-commit) formatiert gestagte PHP-Dateien automatisch. Aktiviert wird er durch `composer install` oder manuell mit `git config core.hooksPath .githooks`.
  - VS Code formatiert PHP-Dateien beim Speichern, wenn die empfohlene Erweiterung „Laravel Pint“ installiert ist.

---

## Aufbau

### Konfiguration

| Datei | Zweck |
|---|---|
| [`phpunit.xml`](../phpunit.xml) | Testsuites `Unit` und `Feature`, SQLite in-memory. Alle Umgebungswerte sind mit `force="true"` gesetzt (Mail und Cache als `array`, feste Test-Adresse für `MAIL_CAMP_ADDRESS`), damit Shell oder `.env` die Tests nie auf echte Dienste umleiten. |
| [`TestCase.php`](TestCase.php) | Basis aller Tests: `RefreshDatabase` mit `TestingSeeder`, Abbruch wenn keine Test-Datenbank, `withoutVite()` (kein gebautes Frontend nötig), Excel-Temp-Dateien ins System-Temp. |
| [`TestingSeeder.php`](../database/seeders/TestingSeeder.php) | Stammdaten jedes Tests: Rollen, Quali-Status, Antworten, Klassifizierungen, Gruppen, Kurstypen, Kapitel, Fragen, Kompetenzen, Admin (User 1) und Global-Camp. Der Admin muss existieren, weil `Camp::created()` ihn in jeden neuen Kurs einträgt. |

### Testdaten: `CampScenario`

[`Support/CampScenario.php`](Support/CampScenario.php) baut mit einem Aufruf einen laufenden Kurs mit allen Rollen auf:

```php
$s = CampScenario::create();

$this->actingAs($s->gruppenleiter)->get(route('home.profile', $s->participant))->assertOk();
```

| Eigenschaft | Inhalt |
|---|---|
| `$s->camp` | Kurs (Kurstyp J+S Leiter 1), erstellt von `$s->kursleiter` |
| `$s->group` | Gruppe des Kurses und der Personen |
| `$s->admin` | Admin (User 1, im Global-Camp) |
| `$s->kursleiter` | Kursleitung des Kurses |
| `$s->gruppenleiter` | Gruppenleitung |
| `$s->participant` | Teilnehmer*in mit `$s->gruppenleiter` als zuständiger Leitung |
| `$s->unassignedParticipant` | Teilnehmer*in **ohne** zugewiesene Leitung |
| `$s->survey` / `$s->unassignedSurvey` | Quali der beiden Teilnehmenden, Status „Neu“, alle Kapitel und Fragen, Antworten „0“ |
| `$s->post` | Rückmeldung von `$s->gruppenleiter` zu `$s->participant` |
| `$s->feedback`, `$s->help`, `$s->standardText` | Feedback zum Tool, Hilfetext „Profil“, Standardtext des Kurses |

Hilfsmethoden:
- `$s->campUser($user)` liefert den `CampUser` einer Person in diesem Kurs.
- `$s->users()` liefert die Rollen als Map (`gast`, `teilnehmer`, `gruppenleiter`, `kursleiter`, `admin`) für Data Provider.

Für Tests über mehrere Kurse einfach zwei Szenarien erzeugen: `$a = CampScenario::create(); $b = CampScenario::create();`.

### Factories

| Factory | Standard | States / Hilfen |
|---|---|---|
| `User::factory()` | Teilnehmende, verifiziert, kein Demo-User, UUID-Slug, Passwort `password` | `demo()`, `unverified()`, `admin()`, `kursleiter()`, `gruppenleiter()`, `stabsleiter()`, `inCamp($camp, $leader = null)` (setzt den Kurs und legt den `CampUser` mit gleicher Rolle an) |
| `Camp::factory()` | neuer Kursleiter als Ersteller, Kurstyp J+S Leiter 1, Enddatum in einem Monat | `finished()`, `withStatusControl()` |
| `Post::factory()` | Text, nicht auf der Quali sichtbar | `shownOnSurvey()`; z. B. `Post::factory()->for($campUser, 'campUser')->for($leader, 'leader')->create(['camp_id' => $camp->id])` |
| `Group::factory()` | Cevi-Gruppe mit Kurzname und Cevi-DB-ID | |

---

## Übersicht aller Tests

| Bereich | Datei | Fälle | Prüft |
|---|---|---:|---|
| Grundlagen | [`TestInfrastructureTest`](Feature/TestInfrastructureTest.php) | 5 | Test-DB, Seeder, Factories |
| Grundlagen | [`Unit/RoleAndClassificationTest`](Unit/RoleAndClassificationTest.php) | 4 | Enums `Role` und `Classification` passen zur Datenbank |
| Smoke | [`Smoke/AllRoutesSmokeTest`](Feature/Smoke/AllRoutesSmokeTest.php) | 5 | Jede GET-Route liefert für jede Rolle keinen Serverfehler |
| Smoke | [`Smoke/PagesRenderTest`](Feature/Smoke/PagesRenderTest.php) | 42 | Alle verlinkten Seiten laden pro Rolle mit 200 |
| Smoke | [`Smoke/DemoLoginTest`](Feature/Smoke/DemoLoginTest.php) | 6 | Öffentliche Demo-Logins |
| Sicherheit | [`Security/AccountUpdateTest`](Feature/Security/AccountUpdateTest.php) | 6 | Eigenes Konto ändern |
| Sicherheit | [`Security/CampSwitchTest`](Feature/Security/CampSwitchTest.php) | 4 | Kurswechsel im Dropdown |
| Sicherheit | [`Security/AdminOnlyAreasTest`](Feature/Security/AdminOnlyAreasTest.php) | 21 | Globale Stammdaten nur für Admin |
| Sicherheit | [`Security/CrossCampManagementTest`](Feature/Security/CrossCampManagementTest.php) | 20 | Admin-Bereich nur für den eigenen Kurs |
| Sicherheit | [`Security/CampTypeContentTest`](Feature/Security/CampTypeContentTest.php) | 6 | Kurstypen, Kapitel, Fragen, Kompetenzen |
| Sicherheit | [`Security/UserCreationTest`](Feature/Security/UserCreationTest.php) | 4 | Personen anlegen |
| Sicherheit | [`Security/UserDataExposureTest`](Feature/Security/UserDataExposureTest.php) | 6 | Keine Passwort-Hashes oder fremden Daten in JSON |
| Sicherheit | [`Security/AdminTableEscapingTest`](Feature/Security/AdminTableEscapingTest.php) | 4 | Namen in Admin-Tabellen als Text, kein eingeschleuster Code |
| Sicherheit | [`Security/CommentEscapingTest`](Feature/Security/CommentEscapingTest.php) | 5 | Rückmeldungen und Bemerkungen als Text mit Zeilenumbrüchen |
| Sicherheit | [`Security/SurveyAnswerTest`](Feature/Security/SurveyAnswerTest.php) | 6 | Antworten speichern, Quali abschliessen |
| Sicherheit | [`Security/PostAccessTest`](Feature/Security/PostAccessTest.php) | 5 | Rückmeldungen nur durch Autor*in |
| Sicherheit | [`Security/MaintenanceRoutesTest`](Feature/Security/MaintenanceRoutesTest.php) | 9 | Wartungsseite nur für Admin, Aktionen nur per POST, alte GET-URLs weg |
| Quali-Ablauf | [`SurveyFlowTest`](Feature/SurveyFlowTest.php) | 30 | Runden, Abschliessen, Freigeben, Zurücksetzen |
| Quali-Ablauf | [`SurveyAccessTest`](Feature/SurveyAccessTest.php) | 3 | Zugriff von Leitenden auf Qualis |
| Quali-Ablauf | [`SurveyOverviewTest`](Feature/SurveyOverviewTest.php) | 6 | Übersicht nach dem Login: Aufgaben, Zähler, Karten mit Links je nach Rolle und Status, kein Diagramm |
| Quali-Ablauf | [`CampChecksTest`](Feature/CampChecksTest.php) | 21 | Regeln der Aufgabenliste (`App\Services\CampChecks`) |
| Quali-Ablauf | [`BottomNavigationTest`](Feature/BottomNavigationTest.php) | 5 | Menü unten auf dem Handy: Punkte je Rolle |
| Quali-Ablauf | [`PwaTest`](Feature/PwaTest.php) | 3 | Manifest, Service Worker speichert keine Seiten, Offline-Seite |
| Quali-Ablauf | [`Admin/SurveyGenerationTest`](Feature/Admin/SurveyGenerationTest.php) | 9 | „Qualis erstellen“ |
| Quali-Ablauf | [`Admin/SurveyEditTest`](Feature/Admin/SurveyEditTest.php) | 3 | Quali im Admin-Bereich bearbeiten |
| Quali-Ablauf | [`Unit/SurveyStatusTest`](Unit/SurveyStatusTest.php) | 8 | Enum `SurveyStatus`: Werte, Regeln, Farbe und Filtergruppe |
| Quali-Ablauf | [`DailyTaskTest`](Feature/DailyTaskTest.php) | 2 | Nächtlicher Reset des Demo-Kurses |
| Quali-Ablauf | [`CampFinishReminderTest`](Feature/CampFinishReminderTest.php) | 4 | Monatliche Mail „Kurs abschliessen“ an die Kursleitung |
| Graphen | [`SpiderChartTest`](Feature/SpiderChartTest.php) | 12 | Beschriftungen, Werte und Sichtbarkeit der Spider-Graphen |
| Import & Login | [`CeviDbImportTest`](Feature/CeviDbImportTest.php) | 12 | Import aus der Cevi-DB (API und JSON-Datei) |
| Import & Login | [`HitobitoLoginTest`](Feature/HitobitoLoginTest.php) | 11 | Login über Cevi-DB und JEMK-DB |
| Import & Login | [`AuthPagesTest`](Feature/AuthPagesTest.php) | 3 | Login und Registrieren zeigen Fehler unter dem Feld (falsches Passwort, Cevi-DB, doppelte E-Mail), behalten Eingaben, aber nie das Passwort |
| Rückmeldungen | [`PostFilesTest`](Feature/PostFilesTest.php) | 9 | Dateianhänge |
| Exporte | [`ExportsTest`](Feature/ExportsTest.php) | 5 | Excel-Export und Druckansicht (PDF) |
| Profil | [`ProfileTest`](Feature/ProfileTest.php) | 3 | Profilseite und Vor/Zurück-Navigation |
| Profil | [`AvatarUploadTest`](Feature/AvatarUploadTest.php) | 1 | Profilbilder |
| Profil | [`ClassificationTest`](Feature/ClassificationTest.php) | 4 | Ampel rot/gelb/grün auf der Profilseite |
| Admin | [`Admin/GroupsTest`](Feature/Admin/GroupsTest.php) | 3 | Gruppen anlegen; löschen nur, wenn keine Personen und Kurse mehr daran hängen (sonst Meldung statt Datenbankfehler) |
| Admin | [`Admin/UserEditTest`](Feature/Admin/UserEditTest.php) | 1 | Person bearbeiten |
| Admin | [`Admin/AdminTablesTest`](Feature/Admin/AdminTablesTest.php) | 7 | Zeilen der Personen-, Quali- und Kursliste als JSON (Rolle und Klassifizierung im aktuellen Kurs, Status mit Farbe und Schritt x von 4 bzw. 5 (mit 2. Runde), Filtergruppe, Aktionen nur für den eigenen Kurs bzw. Admin), Seiten binden die Tabelle mit ihrer Daten-Adresse ein |
| Beispiel | `Feature/ExampleTest`, `Unit/ExampleTest` | 2 | Laravel-Beispiele (Startseite lädt) |

### Grundlagen und Smoke-Tests

**[`TestInfrastructureTest`](Feature/TestInfrastructureTest.php)**: Voraussetzungen, auf die sich alle anderen Tests verlassen.

| Test | Prüft |
|---|---|
| `test_runs_against_sqlite_in_memory` | Tests laufen gegen SQLite in-memory |
| `test_base_data_is_seeded` | Rollen, Admin und Global-Camp sind vorhanden |
| `test_new_camp_gets_admin_camp_user` | Neuer Kurs trägt den Admin automatisch ein |
| `test_user_factory_in_camp_creates_camp_user` | `inCamp()` legt `CampUser` mit Rolle und Leitung an |
| `test_leader_can_open_participant_profile` | Ganzer Stack einmal durch: Profil lädt |

**[`Unit/RoleAndClassificationTest`](Unit/RoleAndClassificationTest.php)**: die Enums [`Role`](../app/Enums/Role.php) und [`Classification`](../app/Enums/Classification.php).

| Test | Prüft |
|---|---|
| `test_role_enum_matches_seeded_roles` | Enum-Werte entsprechen den IDs der Tabelle `roles` |
| `test_role_rights_match_seeded_flags` | Rechte (`is_admin`, `is_campleader`, `is_leader`) passen zu den Rollen |
| `test_classification_enum_matches_seeded_classifications` | Enum-Werte entsprechen den IDs der Tabelle `classifications` |
| `test_classification_css_classes` | CSS-Klassen der Kreise: `red`, `yellow`, `green` |

**[`Smoke/AllRoutesSmokeTest`](Feature/Smoke/AllRoutesSmokeTest.php)**: liest **alle GET-Routen** aus dem Router und ruft sie als Gast, Teilnehmende, Gruppenleitung, Kursleitung und Admin auf. Routen mit `{user}` oder `{survey}` laufen zusätzlich mit der Person ohne Leitung. Erwartung: kein Status ≥ 500.
- Neue Routen werden automatisch mitgetestet. Fehlt ein Wert für einen neuen Routenparameter, schlägt der Test fehl. Dann `routeParameters()` ergänzen.
- `EXCLUDED`: Routen, die bewusst nicht aufgerufen werden, mit Begründung (z. B. OAuth-Redirects).
- `KNOWN_ISSUES`: bekannte, noch nicht behobene Serverfehler mit Rolle und Grund. Ein **neuer** Fehler macht den Test rot. Ebenso ein **behobener** Fehler, bis sein Eintrag entfernt ist. Die Liste ist aktuell leer.

**[`Smoke/PagesRenderTest`](Feature/Smoke/PagesRenderTest.php)**: alle Seiten, die die Navigation einer Rolle verlinkt, müssen mit 200 laden (Teilnehmende, Gruppenleitung, Kursleitung, Admin). Neue Menüpunkte hier ergänzen.

**[`Smoke/DemoLoginTest`](Feature/Smoke/DemoLoginTest.php)**: `/loginTN`, `/loginLeiter`, `/loginKursleiter` mit den Daten aus dem `DemoCampSeeder`.

| Test | Prüft |
|---|---|
| `test_demo_login_leads_to_home` | Login klappt, Startseite lädt |
| `test_demo_login_only_logs_in_demo_users` | Die Demo-Konten sind als Demo markiert und haben damit eingeschränkte Rechte |

### Sicherheit und Berechtigungen (`Feature/Security`)

Die Regeln dazu stehen als Policies in [`app/Policies`](../app/Policies), siehe [Berechtigungen](#berechtigungen-policies).

**[`AccountUpdateTest`](Feature/Security/AccountUpdateTest.php)**: `PATCH /user/{id}`, die eigene Kontoseite.

| Test | Prüft |
|---|---|
| `test_user_cannot_change_password_of_another_user` | Fremdes Passwort ändern → 403 |
| `test_public_demo_login_cannot_change_password_of_admin` | Auch nicht über den Demo-Login |
| `test_user_cannot_change_role_of_another_user` | Fremde Rolle ändern → 403 |
| `test_user_can_change_own_email_and_password` | Eigene E-Mail und eigenes Passwort ändern geht |
| `test_user_cannot_change_role_camp_or_name_of_own_account` | Nur E-Mail und Passwort werden übernommen |
| `test_email_must_be_unique` | E-Mail eines anderen Kontos wird abgelehnt |

**[`CampSwitchTest`](Feature/Security/CampSwitchTest.php)**: `PUT /camps/{camp}`.

| Test | Prüft |
|---|---|
| `test_user_can_switch_to_a_camp_they_belong_to` | Wechsel in eigenen Kurs, Rolle kommt aus dem Kurs |
| `test_kursleiter_cannot_join_a_foreign_camp` | Fremder Kurs → 403, keine Mitgliedschaft entsteht |
| `test_user_can_switch_back_to_global_camp` | Global-Camp geht immer |
| `test_admin_can_switch_to_any_camp` | Admin darf in jeden Kurs |

**[`AdminOnlyAreasTest`](Feature/Security/AdminOnlyAreasTest.php)**: Gruppen, Antworten, Klassifizierungen, Hilfe, Feedback (Middleware `admin`).

| Test | Prüft |
|---|---|
| `test_kursleiter_is_redirected_from_admin_only_area` | 17 Anfragen (ansehen, anlegen, ändern, löschen, an GitHub senden) → Umleitung |
| `test_kursleiter_cannot_change_or_delete_global_data` | Daten bleiben unverändert |
| `test_public_demo_kursleiter_cannot_delete_global_data` | Auch nicht über den Demo-Login |
| `test_kursleiter_can_still_submit_feedback` | Feedback einreichen geht weiterhin, Mail an `MAIL_CAMP_ADDRESS` |
| `test_admin_can_manage_global_data` | Admin darf alles |

**[`CrossCampManagementTest`](Feature/Security/CrossCampManagementTest.php)**: Kurs, Personen, Qualis und Standardtexte im Admin-Bereich.

| Test | Prüft |
|---|---|
| `test_kursleiter_cannot_manage_objects_of_foreign_camp` | 12 Anfragen auf Objekte eines fremden Kurses → 403 |
| `test_foreign_camp_data_stays_unchanged` | Fremde Daten bleiben unverändert |
| `test_kursleiter_manages_own_camp` | Im eigenen Kurs geht alles |
| `test_kursleiter_cannot_make_anyone_admin` | Rolle Administrator nicht vergebbar |
| `test_kursleiter_cannot_change_admin_although_admin_is_in_every_camp` | Admin-Konto nicht änderbar |
| `test_kursleiter_cannot_set_password_of_camp_member` | Keine Passwörter von Mitgliedern setzen |
| `test_kursleiter_cannot_create_global_standard_text` | Standardtexte nur für den eigenen Kurs |
| `test_admin_finishing_a_camp_moves_its_members_to_global_camp` | Kurs abschliessen setzt dessen Mitglieder ins Global-Camp |
| `test_public_demo_kursleiter_cannot_delete_qualis` | Demo darf keine Qualis löschen |

**[`CampTypeContentTest`](Feature/Security/CampTypeContentTest.php)**: Kurstypen und deren Kapitel, Fragen und Kompetenzen.

| Test | Prüft |
|---|---|
| `test_kursleiter_can_change_content_of_own_camp_type` | Eigener Kurstyp: alles änderbar |
| `test_kursleiter_cannot_change_content_of_foreign_camp_type` | Fremder Kurstyp: ansehen, ändern und löschen → 403 |
| `test_kursleiter_cannot_change_default_content` | Standard-Kurstypen und Standardkapitel nur Admin |
| `test_kursleiter_cannot_create_content_for_foreign_camp_type` | Keine Kompetenz für fremden Kurstyp anlegen |
| `test_kursleiter_cannot_move_question_into_foreign_chapter` | Frage nicht in fremdes Kapitel verschieben |
| `test_admin_can_change_default_content` | Admin darf Standardinhalte ändern |

**[`UserCreationTest`](Feature/Security/UserCreationTest.php)**: `POST admin/users`, Personen anlegen.

| Test | Prüft |
|---|---|
| `test_kursleiter_creates_person_with_initial_password` | Startpasswort wird gesetzt, Person landet im Kurs |
| `test_kursleiter_cannot_create_admin` | Rolle Administrator nur durch Admin |
| `test_kursleiter_cannot_create_person_in_foreign_camp` | Neue Person immer im eigenen Kurs |
| `test_admin_may_choose_administrator_role` | Admin darf die Rolle wählen |

**[`UserDataExposureTest`](Feature/Security/UserDataExposureTest.php)**: Personensuche und Personenliste (JSON).

| Test | Prüft |
|---|---|
| `test_participant_cannot_search_users` | Suche nur für Kursleitung |
| `test_public_demo_login_cannot_search_users` | Auch nicht über den Demo-Login |
| `test_user_search_returns_only_fields_needed_for_autocomplete` | Nur `id`, `username`, `email` |
| `test_user_search_excludes_admins_and_members_of_own_camp` | Admin und eigene Mitglieder nicht in der Suche |
| `test_user_list_does_not_expose_password_hashes` | Personenliste ohne Passwort-Hashes |
| `test_serialized_user_hides_secrets` | `password`, `remember_token`, `last_login_ip` sind `$hidden` |

**[`AdminTableEscapingTest`](Feature/Security/AdminTableEscapingTest.php)**: Personen-, Quali- und Kursliste (Vue-Tabelle, `components/DataTable.vue`). Die Endpunkte liefern nur Text und URLs; Links, Bilder und Knöpfe baut die Tabelle mit Vue-Render-Funktionen, Text und Attribute werden dabei maskiert. Namen kommen aus der öffentlichen Registrierung, dem Spitznamen in der Cevi-DB oder vom Kursleiter (Kursname). Geprüft wird mit einem Namen, der Text, unquotierte und quotierte Attribute angreift.

| Test | Prüft |
|---|---|
| `test_user_list_returns_names_unchanged_as_text` | Personenliste: Name und Leitung kommen unverändert als Text |
| `test_user_list_returns_avatar_url_unchanged` | Profilbild-URL unverändert (die Tabelle setzt sie als `src`) |
| `test_survey_list_returns_names_unchanged_as_text` | Quali-Liste: Person und Leitung |
| `test_camp_list_returns_names_unchanged_as_text` | Kursliste (Admin): Kursname und Kursleitung |
| `test_table_components_never_insert_html` | Kein `v-html`, `innerHTML`, `outerHTML`, `insertAdjacentHTML` in Tabelle, Zelle, Hilfsfunktionen und Einstieg; im Browser prüft `data-table.test.js` den Namen `<img …>` als Text |

**[`CommentEscapingTest`](Feature/Security/CommentEscapingTest.php)**: Texte von Rückmeldungen und Bemerkungen zur Quali. Leitende schreiben sie (auch über den Demo-Login `/loginLeiter`), die Kursleitung liest sie. Ein Kommentar mit Zeilenumbruch und `<img onerror>` muss als `Gut gemacht<br />` plus maskiertem Text erscheinen, auf Profil, Vergleich (nur Rückmeldungen "Auf Quali") und in beiden Druckansichten (5 Fälle).

**[`SurveyAnswerTest`](Feature/Security/SurveyAnswerTest.php)**: `PATCH survey/{survey}` und `PATCH compare/{id}`.

| Test | Prüft |
|---|---|
| `test_participant_saves_answers_of_own_quali` | Eigene Antworten speichern |
| `test_participant_cannot_save_answers_into_quali_of_another_participant` | Fremde Quali → 403 |
| `test_participant_cannot_smuggle_foreign_question_ids_into_own_quali` | Nur Fragen der eigenen Quali werden gespeichert |
| `test_public_demo_login_cannot_change_answers_of_real_qualis` | Demo verändert keine echten Qualis |
| `test_leader_of_foreign_camp_cannot_finish_quali` | Abschliessen nur im eigenen Kurs |
| `test_assigned_leader_finishes_quali` | Zuständige Leitung schliesst ab |

**[`PostAccessTest`](Feature/Security/PostAccessTest.php)**: Rückmeldungen bearbeiten und löschen.

| Test | Prüft |
|---|---|
| `test_author_can_edit_and_delete_own_post` | Autor*in darf alles |
| `test_other_leader_cannot_open_or_delete_post` | Andere Leitung → 403 |
| `test_participant_cannot_delete_post_about_them` | Teilnehmende → 403 |
| `test_other_leader_cannot_overwrite_post_via_profile_form` | Auch nicht über das Profil-Formular |
| `test_author_can_update_post_via_profile_form` | Autor*in ändert über das Profil-Formular |

**[`MaintenanceRoutesTest`](Feature/Security/MaintenanceRoutesTest.php)**: Wartungsseite `admin/maintenance` (Migrationen ausführen, Cache leeren). Gäste und Kursleitung lösen kein Artisan aus, GET auf die Aktionen gibt 405, `admin/run-migrations` und `admin/run-deployment` gibt es nicht mehr (404); die Seite listet offene Migrationen, Admin-Aktionen rufen `migrate --force` bzw. `optimize:clear` auf und zeigen die Ausgabe.

### Quali-Ablauf

**[`SurveyFlowTest`](Feature/SurveyFlowTest.php)**: der Ablauf einer Quali.

| Test | Prüft |
|---|---|
| `test_participant_access_to_own_quali` | Zugriffstabelle unten, 9 Fälle |
| `test_participant_can_save_only_when_allowed` | Dieselbe Tabelle fürs Speichern, 9 Fälle |
| `test_assigned_leader_fills_in_until_quali_is_finished` | Leitung füllt aus bis „Fertig“ |
| `test_first_round_saves_into_first_answers_and_opens_quali` | 1. Runde → `answer_first`, Status „1. offen“ |
| `test_closing_first_round_finishes_self_assessment` | Abschliessen → „TN abgeschlossen“ |
| `test_closing_first_round_moves_on_to_second_round_if_already_released` | … oder direkt „2. offen“, wenn bereits freigegeben |
| `test_second_round_saves_into_second_answers_and_closes` | 2. Runde → `answer_second` |
| `test_leader_saves_leader_assessment_without_changing_status` | Leiter-Einschätzung und Kommentar, Status bleibt |
| `test_releasing_first_round_with_status_control_lets_participants_start` | „Erste Selbsteinschätzung freigeben“ |
| `test_releasing_second_round_reopens_closed_self_assessments_only` | „Zweite Selbsteinschätzung freigeben“ |
| `test_leader_finishes_only_closed_quali` | Fertigstellen nur aus „TN abgeschlossen“ |
| `test_resetting_to_new_clears_both_rounds` | Zurücksetzen auf „Neu“ leert beide Runden |
| `test_resetting_to_first_round_clears_only_second_round` | Zurücksetzen auf „1. offen“ leert nur die 2. Runde |
| `test_changing_status_without_reset_keeps_answers` | Ohne Häkchen bleiben die Antworten |

**[`SurveyAccessTest`](Feature/SurveyAccessTest.php)**

| Test | Prüft |
|---|---|
| `test_leader_is_redirected_from_survey_of_participant_without_leader` | Teilnehmende ohne Leitung: Umleitung statt Fehler |
| `test_leader_has_access_to_survey_of_own_participant` | Eigene Teilnehmende: Zugriff |
| `test_access_does_not_depend_on_loaded_relations_of_the_camp` | Kursvergleich mit `is()`, nicht `==` |

**[`SurveyOverviewTest`](Feature/SurveyOverviewTest.php)**: Übersicht nach dem Login (`/home`): Aufgaben, Zähler, eine Karte pro Quali.

| Test | Prüft |
|---|---|
| `test_participant_sees_own_survey_and_task_without_compare_and_profile_links` | Teilnehmende: Aufgabe „1. Selbsteinschätzung ausfüllen“, Link zum Ausfüllen, kein Vergleich, kein Profil, keine Zähler |
| `test_participant_gets_compare_link_and_no_task_after_closing` | Nach dem Abschliessen: Vergleich statt Ausfüllen, keine Aufgabe mehr |
| `test_leader_sees_own_participants_with_links_and_counters` | Leitende: nur eigene Teilnehmende, Vergleich, „+ Rückmeldung“ (`?neu=1`), Anzahl Rückmeldungen, Zähler |
| `test_closed_self_assessment_is_counted_but_no_task` | „TN abgeschlossen“ zählt bei „Zum Abschliessen“, ist aber keine Aufgabe |
| `test_campleader_sees_all_participants_and_camp_tasks` | Kursleitung: alle Teilnehmenden, „Noch ohne Leitende“, Kurs-Aufgaben, „Teilnehmende ohne Rückmeldung“ orange |
| `test_overview_does_not_load_chart_data` | Kein Radar-Diagramm mehr auf der Übersicht |

**[`CampChecksTest`](Feature/CampChecksTest.php)**: Regeln der Aufgabenliste (`App\Services\CampChecks`).

| Test | Prüft |
|---|---|
| `test_leader_sees_only_own_participants_and_campleader_all` | Welche Qualis jemand auf der Übersicht sieht |
| `test_closed_self_assessment_is_no_task` | „TN abgeschlossen“ ist keine Aufgabe (das Abschliessen ist fester Teil des Kursprogramms) |
| `test_not_started_is_a_task_only_after_release` | „Noch nicht begonnen“ nur, wenn die 1. Runde frei ist (ohne Status-Steuerung immer, mit Steuerung ab Kurs-Status „1. offen“), 4 Fälle |
| `test_participant_task_is_the_own_open_self_assessment` | Teilnehmende: eigene offene Selbsteinschätzung, nach dem Abschliessen keine |
| `test_camp_tasks_only_for_campleader` | Kurs-Aufgaben nur für die Kursleitung |
| `test_participants_without_survey` | Teilnehmende ohne Quali |
| `test_camp_must_be_finished_three_months_after_end_date` | Kurs abschliessen: erst 3 Monate nach Schlussdatum, nie für Demo-, Global- und abgeschlossene Kurse, ohne Schlussdatum oder mit `0000-00-00`, 7 Fälle |
| `test_finishing_the_camp_is_a_red_campleader_task_on_top` | „Kurs abschliessen“ ist rot, steht zuoberst und führt zu „Kurs bearbeiten“ |
| `test_leader_sees_own_participants_without_feedback_in_orange` | „X hat noch keine Rückmeldung“ orange, nur für eigene Teilnehmende, Link öffnet „Neue Rückmeldung“ im Profil |
| `test_campleader_sees_one_summary_line_for_all_participants_without_feedback` | Kursleitung: eine orange Zeile „X Teilnehmende ohne Rückmeldung“ für alle, keine Zeile pro Person |
| `test_tasks_are_sorted_red_orange_grey` | Reihenfolge rot, orange, grau |
| `test_second_round_can_be_released_when_all_have_closed_the_first` | „2. Selbsteinschätzung freigeben“ erst, wenn alle die 1. abgeschlossen haben, nicht mehr nach der Freigabe |

**[`BottomNavigationTest`](Feature/BottomNavigationTest.php)**: Menü unten auf dem Handy (`includes/bottomnav.blade.php`).

| Test | Prüft |
|---|---|
| `test_guest_has_no_bottom_navigation` | Gäste sehen kein Menü unten |
| `test_participant_gets_overview_own_quali_and_account` | Teilnehmende: Übersicht (aktiv), eigene Quali, Konto; kein Feedback, Neu, Kursadmin, Mehr |
| `test_participant_quali_link_goes_to_compare_after_closing` | Nach dem Abschliessen führt „Quali“ zum Vergleich |
| `test_leader_gets_feedback_new_feedback_account_and_more` | Leitende: Feedback (aktiv auf `/posts`), Neu (`/posts?neu=1`), Konto, Mehr; kein Kursadmin |
| `test_campleader_gets_course_admin_instead_of_account` | Kursleitung: Kursadmin statt Konto |

**[`PwaTest`](Feature/PwaTest.php)**: Installierbare App.

| Test | Prüft |
|---|---|
| `test_manifest_starts_on_overview_with_icons_and_shortcut` | Start auf `/home`, Icons vorhanden, Verknüpfung „Neue Rückmeldung“ |
| `test_service_worker_never_caches_pages` | `public/sw.js` holt Seiten nur aus dem Netz (ohne Netz `offline.html`), speichert nur Dateien aus `/build/` und Bilder |
| `test_layout_links_manifest` | Das Layout verlinkt das Manifest |

**[`Admin/SurveyGenerationTest`](Feature/Admin/SurveyGenerationTest.php)**: „Qualis erstellen“ (`POST admin/surveys`).

| Test | Prüft |
|---|---|
| `test_quali_has_all_default_chapters_and_questions_for_default_camp_type` | Standard-Kurstyp: Standardkapitel mit allen Fragen |
| `test_quali_uses_chapters_of_custom_camp_type` | Eigener Kurstyp: nur dessen Kapitel |
| `test_all_answers_start_with_answer_zero` | Alle Antworten starten mit „0“ |
| `test_quali_is_created_only_for_participants_without_one` | Nur Teilnehmende ohne Quali, keine Leitenden |
| `test_quali_is_created_only_in_current_camp` | Nur im aktuellen Kurs |
| `test_demo_user_creates_no_quali` | Demo erstellt nichts |
| `test_admin_in_global_camp_creates_no_quali` | Global-Camp ohne Kurstyp erstellt nichts |
| `test_quali_is_not_created_by_a_get_request` | GET erstellt nichts |
| `test_button_on_quali_overview_sends_post_to_store_route` | Button ruft die POST-Route auf (JS nur als Text geprüft) |

**[`Admin/SurveyEditTest`](Feature/Admin/SurveyEditTest.php)**: Quali bearbeiten (`admin/surveys/{survey}/edit`).

| Test | Prüft |
|---|---|
| `test_form_preselects_current_status` | Statusauswahl zeigt den aktuellen Status (sonst setzt Speichern still einen anderen) |
| `test_invalid_status_is_rejected` | Ungültiger Status wird mit Validierungsfehler abgelehnt |
| `test_overview_shows_status_name` | Statusname erscheint auf der Übersicht |

**[`Unit/SurveyStatusTest`](Unit/SurveyStatusTest.php)**: das Enum [`App\Enums\SurveyStatus`](../app/Enums/SurveyStatus.php).

| Test | Prüft |
|---|---|
| `test_enum_matches_seeded_status_table` | Enum-Werte entsprechen den IDs der Tabelle `survey_statuses` |
| `test_named_rules` | `isFirstRound()`, `hasReachedSecondRound()`, `isClosedByParticipant()`, `isFinished()` je Status (5 Fälle) |
| `test_order` | `isBefore()` und `isAtLeast()` |
| `test_tone_and_phase` | Farbe (grau/blau/orange/grün) und Filtergruppe (offen/abschliessen/fertig) je Status |

**[`DailyTaskTest`](Feature/DailyTaskTest.php)**: `php artisan task:daily`.

| Test | Prüft |
|---|---|
| `test_demo_camp_is_reset` | Demo-Kurs: Einstellungen, Status und Kommentare zurückgesetzt |
| `test_real_camps_are_not_touched` | Echte Kurse bleiben unverändert |

**[`CampFinishReminderTest`](Feature/CampFinishReminderTest.php)**: `php artisan camps:finish-reminder`, geplant am letzten Tag jedes Monats um 08:00.

| Test | Prüft |
|---|---|
| `test_only_overdue_camps_are_reminded_to_their_campleader` | Nur Kurse 3 Monate nach Schlussdatum, an die Kursleitung; nicht: jünger, abgeschlossen, Demo, Global, ohne Schlussdatum |
| `test_one_mail_per_campleader_with_all_overdue_camps` | Eine Mail pro Person mit allen offenen Kursen, älteste zuerst |
| `test_mail_names_camp_end_date_and_way_to_finish` | Betreff, Kursname maskiert, Schlussdatum, Weg zum Abschliessen, Link ins Tool, „jeweils Ende Monat“ |
| `test_reminder_is_scheduled_on_the_last_day_of_each_month` | Eintrag im Scheduler am letzten Tag des laufenden Monats, 08:00 |

### Spider-Graphen

**[`SpiderChartTest`](Feature/SpiderChartTest.php)**: `Helper::GetSurveysLabels()` und `Helper::GetSurveysDataset()`.

| Test | Prüft |
|---|---|
| `test_labels_follow_sort_index_and_mark_core_competences` | Reihenfolge nach `sort-index`, `*` bei Kernkompetenzen |
| `test_values_and_ids_follow_label_order` | Werte und `dataset_ids` in derselben Reihenfolge (Graph-Update) |
| `test_visible_datasets` | Wer welche Datensätze sieht, 8 Fälle (Tabelle unten) |
| `test_participant_never_receives_leader_assessment_on_overview` | Startseite der Teilnehmenden ohne Leiter-Einschätzung |
| `test_quali_without_questions_does_not_break_pages` | Quali ohne Fragen: Seiten laden trotzdem |

### Import und Login

**[`CeviDbImportTest`](Feature/CeviDbImportTest.php)**: Import aus der Cevi-DB. Die API wird mit `Http::fake()` ersetzt.

| Test | Prüft |
|---|---|
| `test_imports_participants_and_leaders_into_camp` | Rollen, Namen (Nickname vor Vorname), Cevi-DB-ID, Kurs |
| `test_people_without_email_are_skipped_and_reported_by_name` | Ohne E-Mail: übersprungen und namentlich gemeldet |
| `test_second_run_creates_no_duplicates` | Zweiter Lauf legt nichts doppelt an |
| `test_existing_account_is_matched_by_email_ignoring_case` | Bestehendes Konto wird erkannt (Gross-/Kleinschreibung egal) |
| `test_underscore_in_email_does_not_match_another_person` | `_` in der E-Mail ist kein Platzhalter |
| `test_only_course_leaders_may_import` | Nur Kursleitende der Cevi-DB dürfen importieren |
| `test_missing_person_data_is_reported_as_api_key_problem` | Fehlende Personendaten → Hinweis auf API-Key-Rechte |
| `test_follows_pagination_and_sorts_by_id` | Paginierung, Sortierung nach `id`, Token-Header |
| `test_failure_leaves_no_half_imported_people` | Fehler mitten im Import hinterlässt nichts |
| `test_cevi_db_export_can_be_uploaded_as_json` | JSON-Export hochladen |
| `test_csv_files_are_no_longer_imported` | CSV wird nicht mehr verarbeitet |
| `test_camp_without_cevi_db_id_is_rejected_without_request` | Ohne Cevi-DB-ID keine Anfrage an die API |

**[`HitobitoLoginTest`](Feature/HitobitoLoginTest.php)**: Login über Cevi-DB und JEMK-DB. Socialite wird gemockt.

| Test | Prüft |
|---|---|
| `test_new_person_gets_an_account_and_is_logged_in` | Neue Person: Konto wird angelegt |
| `test_returning_person_is_recognised_by_cevi_db_id_and_email_is_updated` | Erkennung über Cevi-DB-ID, E-Mail wird aktualisiert |
| `test_email_of_another_account_is_not_taken_over` | Besetzte E-Mail wird nicht übernommen |
| `test_existing_local_account_cannot_be_taken_over_via_hitobito` | Lokales Konto mit gleicher E-Mail wird nicht übernommen |
| `test_denied_access_shows_error` | Abgelehnter Zugriff → Fehlermeldung |
| `test_jemk_login_follows_the_same_rules` | JEMK-Login wie Cevi-DB |
| `test_username_falls_back_through_profile_fields` | Name: Nickname → Vorname → Nachname → E-Mail (4 Fälle) |
| `test_avatar_uses_profile_picture_or_default` | Profilbild aus der Cevi-DB oder Standardbild |

### Rückmeldungen, Exporte, Profil

**[`PostFilesTest`](Feature/PostFilesTest.php)**: Dateianhänge an Rückmeldungen. Arbeitet in einem eigenen Temp-`storage`.

| Test | Prüft |
|---|---|
| `test_leader_attaches_file_to_feedback` | Datei wird gespeichert, Originalname bleibt lesbar |
| `test_leaders_of_same_camp_download_file` | Leitende des Kurses laden herunter |
| `test_participants_and_other_camps_cannot_download_file` | Teilnehmende und fremde Kurse nicht |
| `test_file_of_general_feedback_can_be_downloaded` | Auch bei Rückmeldungen ohne zugeordnete Person |
| `test_feedback_cannot_be_attached_to_participant_of_other_camp` | Nur Personen des eigenen Kurses |
| `test_editing_text_keeps_the_attached_file` | Text ändern behält die Datei |
| `test_deleting_feedback_removes_file` | Löschen entfernt die Datei |
| `test_feedback_can_be_deleted_when_file_is_already_gone` | Löschen klappt auch, wenn die Datei fehlt |
| `test_demo_users_cannot_upload_files` | Demo lädt keine Dateien hoch |

**[`ExportsTest`](Feature/ExportsTest.php)**

| Test | Prüft |
|---|---|
| `test_kursleiter_downloads_feedback_as_excel` | Excel-Download der Rückmeldungen |
| `test_excel_contains_own_camp_feedback_including_general_feedback` | Nur eigener Kurs, auch allgemeine Rückmeldungen |
| `test_leaders_open_print_view_of_quali` | Druckansicht für Leitende |
| `test_participant_cannot_open_print_view_with_leader_assessment` | Nicht für Teilnehmende (enthält die Leiter-Einschätzung) |
| `test_leader_of_other_camp_cannot_open_print_view` | Nicht für fremde Kurse und fremde Teilnehmende |

**[`ProfileTest`](Feature/ProfileTest.php)**

| Test | Prüft |
|---|---|
| `test_profile_of_user_outside_own_camp_redirects_back` | Person ausserhalb des Kurses → zurück |
| `test_profile_navigates_between_participants` | Vor/Zurück zeigt auf die richtigen Teilnehmenden |
| `test_profile_renders_when_camp_has_no_participants` | Ohne Teilnehmende keine Navigation, kein Fehler |

**[`AvatarUploadTest`](Feature/AvatarUploadTest.php)**: `test_cropped_avatar_is_stored_as_jpeg_with_generated_name`. Profilbilder werden immer als `<uuid>.jpg` gespeichert, unabhängig vom hochgeladenen Dateinamen.

**[`Admin/GroupsTest`](Feature/Admin/GroupsTest.php)**: `test_admin_can_create_group_without_cevi_db_id`. Gruppe ohne Cevi-DB-ID anlegen.

**[`ClassificationTest`](Feature/ClassificationTest.php)**: Ampel auf der Profilseite (`PATCH changeClassifications/{id}/{color}`).

| Test | Prüft |
|---|---|
| `test_assigned_leader_changes_classification` | Zuständige Leitung setzt die Farbe |
| `test_profile_marks_current_classification` | Profilseite markiert die aktuelle Farbe |
| `test_unknown_color_is_rejected` | Unbekannte Farbe → 404, nichts ändert sich |
| `test_other_leader_cannot_change_classification` | Andere Leitung kann nichts ändern |

**[`Admin/UserEditTest`](Feature/Admin/UserEditTest.php)**: `test_form_preselects_current_role`. Die Rollenauswahl zeigt die aktuelle Rolle (sonst setzt Speichern still eine andere).

---

## JS-Tests (Vitest)

Seitenskripte mit eigener Logik liegen als Module in [`resources/js/pages/`](../resources/js/pages) und werden in der jeweiligen Ansicht mit `@vite(...)` eingebunden (Einstiegspunkte in [`vite.config.js`](../vite.config.js)). Die Tests in [`js/`](js) laufen mit Vitest und happy-dom (ein simuliertes DOM, kein Browser) in unter einer Sekunde, Konfiguration in [`vitest.config.js`](../vitest.config.js).

- **Vue-Komponenten** (`resources/js/components/`) mit `mount()` aus `@vue/test-utils`, `flushPromises()` nach dem Laden. Die Vue-Inseln werden über `resources/js/islands.js` in Blade eingebunden (`data-vue-component`, Props in `data-props`).
- **Muster:** Pro Test das Markup der Blade-Ansicht nachbauen (`document.body.innerHTML`), dann `vi.resetModules()` und das Modul mit `await import(...)` laden; es startet sich beim Import selbst. `fetch` per `vi.stubGlobal`, SweetAlert2 per `vi.mock('sweetalert2')`, Zeit per `vi.useFakeTimers()`.
- **Grenzen:** Ob Modul, Server und Seite zusammenspielen, prüfen die Browser-Tests (z. B. `SurveyAutosaveTest`, `StandardTextsTest`).
- **Node:** Vitest 5 braucht Node ≥ 22.12 (und Vite ≥ 6.4). Container (`docker/8.5/Dockerfile`), CI und Prod-Build stehen auf Node 22.

| Datei | Fälle | Prüft |
|---|---:|---|
| [`survey-autosave.test.js`](js/survey-autosave.test.js) | 6 | Quali speichert sich 1 s nach der letzten Änderung selbst (einmal, ganzes Formular mit `action=save`), Zustand im Speichern-Knopf, Fehler bei abgelehnter Anfrage und ohne Verbindung, mehrere Formulare getrennt |
| [`standard-texts.test.js`](js/standard-texts.test.js) | 11 | `//` öffnet die Textbausteine (nur zwei schnelle `/` direkt vor dem Cursor), Pfeiltasten mit Umspringen, Enter und Klick fügen an der Stelle der `//` ein (auch mitten im Text), ein weiteres `/` danach öffnet nicht erneut, Escape und Kreuz schliessen, Tasten bei geschlossener Auswahl unberührt |
| [`users-import.test.js`](js/users-import.test.js) | 3 | Import aus der Cevi-DB: Adresse aus `data-url` und CSRF-Token, übersprungene Namen als Text, Fehlermeldung des Servers im Dialog |
| [`escape-html.test.js`](js/escape-html.test.js) | 2 | `escapeHtml()` für HTML-Vorlagen |
| [`user-search.test.js`](js/user-search.test.js) | 6 | Person hinzufügen: Suche ab drei Zeichen und nach kurzer Pause, Vorschlag übernimmt Name und `user_id`, Ändern löscht die ID, keine veralteten oder späten Antworten, Netzwerkfehler |
| [`counter-up.test.js`](js/counter-up.test.js) | 3 | Startseite: Zähler zählen erst hoch, wenn sie ins Bild kommen (`IntersectionObserver` simuliert), in Schritten bis zum Zielwert |
| [`theme-toggle.test.js`](js/theme-toggle.test.js) | 4 | Hell/Dunkel-Umschalter: Klasse `dark`, gespeicherte Wahl und Symbol, auch ohne gespeicherte Wahl (Systemeinstellung) |
| [`table-data.test.js`](js/table-data.test.js) | 10 | Hilfsfunktionen der Admin-Tabellen: Anzeige- und Sortierwert (`{ display, sort }`), Vergleich (Zahlen, natürlich, Umlaute, ISO-Daten), Suche über Textspalten, Sortierung in der Adresse (`?order=role:asc,name:desc`, alte Spaltennummern verworfen) |
| [`data-table.test.js`](js/data-table.test.js) | 16 | `DataTable.vue` mit `@vue/test-utils`: Laden mit Platzhaltern, Link/Bild/Status (Badge, Balken „Schritt x von y“)/Aktionen aus Daten, Namen mit `<` als Text, Sortieren per Klick und Shift-Klick, Status nach Ablauf statt Alphabet, Filter-Knöpfe, Suche mit Zähler, Blättern, Ladefehler mit "Erneut laden", Sortierung in Adresse und Verlauf, Karten auf dem Handy (`matchMedia` gestubbt) mit Sortier-Auswahl |
| [`surveys-index.test.js`](js/surveys-index.test.js) | 4 | Quali-Liste: "Qualifikationen erstellen" nach Rückfrage, Freigeben ohne Rückfrage, POST mit CSRF-Token, Fehlermeldung statt Neuladen |
| [`profile-ampel.test.js`](js/profile-ampel.test.js) | 2 | Ampel auf dem Profil: PATCH an `data-remote`, Neuladen nur bei Erfolg |
| [`tooltips.test.js`](js/tooltips.test.js) | 3 | Erklärungen auf Quali und Vergleich (tippy.js): erst beim Antippen/Klick, Text nie als HTML, leere Erklärungen ohne Tooltip |

## Browser-Tests (Dusk)

Die PHP-Tests sehen nur das ausgelieferte HTML. Die Browser-Tests in [`Browser/`](Browser) laufen in einem echten Chrome (Selenium-Container) gegen den Webserver von Sail und prüfen, was die Skripte daraus machen.

```bash
# Einmal vom Host aus: Selenium starten (nur diesen Dienst, der App-Container bleibt unverändert)
./vendor/bin/sail up -d selenium

# Im Container: Frontend bauen (Dusk testet public/build), dann die Tests
npm run build
php artisan dusk
php artisan dusk tests/Browser/DeleteConfirmationTest.php
```

- **Umgebung:** Dusk ersetzt während des Laufs die `.env` durch [`.env.dusk.local`](../.env.dusk.local): SQLite-Datei `database/dusk.sqlite` (Testprozess und Webserver brauchen dieselbe Datenbank), Mail ins Log, externe Dienste ins Leere. [`phpunit.dusk.xml`](../phpunit.dusk.xml) statt `phpunit.xml`, weil dieses SQLite im Arbeitsspeicher erzwingt.
- **Datenbank:** [`DuskTestCase`](DuskTestCase.php) migriert einmal pro Lauf und leert vor jedem Test alle Tabellen (`DatabaseTruncation`), danach `TestingSeeder`. Es bricht ab, wenn nicht `dusk.sqlite` konfiguriert ist. `DatabaseMigrations` geht nicht: Sein `migrate:rollback` scheitert am `down()` von `fix_implicit_mysql_defaults`.
- **Bei Fehlern:** Screenshot, Browser-Konsole und HTML liegen in `Browser/screenshots`, `Browser/console` und `Browser/source` (nicht im Repo).

| Datei | Fälle | Prüft |
|---|---:|---|
| [`PagesInBrowserTest`](Browser/PagesInBrowserTest.php) | 2 | Alle Seiten aus `PagesRenderTest` pro Rolle plus Gast-Seiten: keine Fehler in der Browser-Konsole, jede `id` nur einmal |
| [`DeleteConfirmationTest`](Browser/DeleteConfirmationTest.php) | 5 | Person, Kurs, Gruppe, Rückmeldung (gemeinsames Modul `confirm-submit.js`, Knöpfe mit `data-confirm`): "Abbrechen" löscht nichts, "Ja" löscht genau den gewählten Eintrag; Selbsteinschätzung abschliessen erst nach "Ja" (Knopfwert `action=close` kommt an) |
| [`SurveyAutosaveTest`](Browser/SurveyAutosaveTest.php) | 1 | Leitung wählt Antwort und schreibt Kommentar, ohne zu speichern: nach "Gespeichert ✓" in der DB und nach dem Neuladen noch da |
| [`StandardTextsTest`](Browser/StandardTextsTest.php) | 2 | `//` im Fenster "Neue Rückmeldung" fügt einen Textbaustein ein, auch mitten im Text an der Cursor-Stelle; Escape schliesst nur die Auswahl, nicht das Fenster |
| [`UserSearchTest`](Browser/UserSearchTest.php) | 1 | Person hinzufügen: Vorschlag aus der Personensuche erscheint, die Wahl setzt Name und `user_id`, die Person ist danach im Kurs |
| [`AdminTablesTest`](Browser/AdminTablesTest.php) | 4 | Personenliste lädt, sucht, sortiert per Klick und merkt sich die Sortierung in Adresse und Verlauf (Neuladen, Zurück); Quali- und Kursliste zeigen Zeilen mit Status bzw. Knöpfen; Quali-Liste filtert Offen/Zum Abschliessen, Kursliste Alle/Offene/Abgeschlossene |
| [`MobileNavigationTest`](Browser/MobileNavigationTest.php) | 3 | Menü unten (390 px): „Mehr“ öffnet das Menü oben, „Neu“ öffnet „Neue Rückmeldung“ mit Cursor im Textfeld; auf dem Desktop ausgeblendet; Service Worker speichert die Offline-Seite, aber keine besuchte Seite (Chrome behandelt die Testadresse dafür als sicher, siehe `DuskTestCase`) |
| [`TooltipsTest`](Browser/TooltipsTest.php) | 1 | Quali: Antippen einer Antwort zeigt ihre Bedeutung; kein Skript mehr von unpkg.com |
| [`PhotoCropperTest`](Browser/PhotoCropperTest.php) | 1 | Profilbild: Datei wählen öffnet den Dialog, "Abbrechen" lässt das Feld leer, "Zuschneiden" legt ein PNG als data-URL ab |
| [`VisualSnapshotTest`](Browser/VisualSnapshotTest.php) | 1 | Nur mit `SNAPSHOT_SET`, sonst übersprungen: ganzseitige Fotos aller Seiten aus `PagesRenderTest` und der Gast-Seiten, in 390, 1000 und 1440 px sowie Dark Mode (184 Fotos), für den Pixelvergleich |

### Pixelvergleich (vor und nach einem CSS-Umbau)

```bash
SNAPSHOT_SET=before php artisan dusk --filter=VisualSnapshotTest     # vor der Änderung (~6 min)
# ändern, npm run build
SNAPSHOT_SET=after php artisan dusk --filter=VisualSnapshotTest
php tests/Browser/compare-snapshots.php tests/Browser/screenshots/before tests/Browser/screenshots/after tests/Browser/screenshots/diff
```

- **Zuerst ein Kontrolllauf:** zweimal `before` mit demselben Stand, die beiden müssen pixelgleich sein. Der Test hält dafür fest, was der Server sonst je Lauf anders zeigt: Zeitstempel ("vor X Minuten"), "Letztes Login" und die Slugs der Personen (stecken in Links; bis Oktober 2026 sortierte die Quali-Liste danach).
- **Nur einzelne Seiten:** `SNAPSHOT_ONLY="admin: Gruppen,Kurs erstellen"` (Teile der Namen aus `PagesRenderTest`, Komma als Trenner), z. B. mit `SNAPSHOT_SET=check` für einen schnellen Blick nach einer Änderung.
- **Unterschiede:** `compare-snapshots.php` meldet Fotos mit anderer Grösse und abweichende Pixel, im Ordner `diff` sind sie rot markiert.
- **Nicht abbrechen:** Dusk ersetzt während des Laufs `.env` durch `.env.dusk.local`. Wird der Lauf abgebrochen, liegt das Original in `.env.backup`, und die App läuft mit der Dusk-Umgebung, bis `.env.backup` zurück nach `.env` kopiert ist (das macht auch der nächste vollständige Dusk-Lauf).
- **Grenzen:** Geschlossene Fenster und Menüs sind auf den Fotos nicht zu sehen, ebenso Fokus- und Hover-Zustände.

## Fachliche Regeln, die die Tests festhalten

Diese Regeln sind bewusst so umgesetzt. Wer sie ändert, muss auch die Tests anpassen.

### Zugriff von Teilnehmenden auf die eigene Quali

Aus `SurveyFlowTest` (`Survey::TNIsAllowed()`). Die Status sind das Enum [`App\Enums\SurveyStatus`](../app/Enums/SurveyStatus.php): `Neu`, `ErsteRundeOffen`, `ZweiteRundeOffen`, `TnAbgeschlossen`, `Fertig`. Die Regeln haben dort Namen (z. B. `isClosedByParticipant()`), bitte diese statt Zahlenvergleichen verwenden. `$survey->status()` liefert den Status, alte Qualis ohne Status gelten als `Neu`.

| Situation | Ausfüllen erlaubt? |
|---|---|
| Ohne „Quali-Ablauf kontrollieren“: 1. Runde (Neu / 1. offen) | Ja |
| Mit Status-Steuerung, bevor „Erste Selbsteinschätzung freigeben“ geklickt wurde | Nein |
| Mit Status-Steuerung, nach der Freigabe | Ja |
| Eigene Selbsteinschätzung abgeschlossen („TN abgeschlossen“) | Nein |
| 2. Runde, bevor „Zweite Selbsteinschätzung freigeben“ geklickt wurde | Nein |
| 2. Runde nach der Freigabe | Ja |
| Quali „Fertig“ | Nein |

Zugewiesene Leitende dürfen ihre Einschätzung bis „Fertig“ ausfüllen. Kursleitende arbeiten über die Vergleichsseite.

### Sichtbare Datensätze im Spider-Graph

Aus `SpiderChartTest`:

| Wer | 1. Selbsteinschätzung | 2. Selbsteinschätzung | Leiter-Einschätzung |
|---|---|---|---|
| Teilnehmende | immer | wenn 2. Runde freigegeben und Status ≥ „2. offen“ | **nie** |
| Leitende, Kursleitung | immer | wenn 2. Runde freigegeben und Status ≥ „2. offen“ | immer |

### Berechtigungen (Policies)

Alle Regeln mit der Fähigkeit `manage` in [`app/Policies`](../app/Policies), registriert im `AuthServiceProvider`. In Controllern: `$this->authorize('manage', $objekt)`.

| Objekt | Wer darf bearbeiten/löschen |
|---|---|
| Kurs, Qualis (Admin-Bereich), Standardtexte | Kursleitung nur im **aktuellen** Kurs, Admin überall. Globale Standardtexte nur Admin. |
| Personen | Kursleitung nur Mitglieder des aktuellen Kurses, nie den Admin, nie die Rolle Administrator |
| Kurstypen, Kapitel, Fragen, Kompetenzen | Wer den Kurstyp erstellt hat (`user_id`). Standard-Kurstypen und Standardkapitel nur Admin. |
| Rückmeldungen | Nur wer sie geschrieben hat |
| Gruppen, Antworten, Klassifizierungen, Hilfe, Feedback | Nur Admin (Middleware `admin`). Feedback einreichen dürfen alle Kursleitenden. |

### Ampel (Klassifizierung)

Aus `ClassificationTest`: Die Farbe einer TN-Person setzen die zuständige Leitung und die Kursleitung. Gültig sind nur die drei Werte des Enums `Classification` (Rot, Gelb, Grün). Neue Personen starten mit Gelb.

### Feste Werte als Enums

Quali-Status, Rollen und Klassifizierungen sind Enums in [`app/Enums`](../app/Enums). Ihre Werte sind die IDs der gleichnamigen Tabellen, dort stehen die Anzeigenamen. In `config/status.php` stehen nur noch die IDs der drei Standard-Kurstypen, weil Kurstypen sonst Daten sind (Kursleitende legen eigene an).

Die App ist auf **genau einen Admin** ausgelegt (User 1). Weitere Admins anzulegen ist kein unterstützter Ablauf.

---

## Neuen Test schreiben

### Checkliste

1. **Ort wählen:** `tests/Feature/Security` für Berechtigungen, `tests/Feature/Smoke` für seitenweite Prüfungen, `tests/Feature/Admin` für den Admin-Bereich, sonst `tests/Feature`.
2. **Von `Tests\TestCase` erben.** Die Datenbank wird pro Test automatisch zurückgesetzt.
3. **Testdaten** mit `CampScenario::create()` oder den Factories aufbauen.
4. **Bei Fehlern zuerst den Test, dann den Fix:** Der Test muss vor dem Fix rot sein, sonst prüft er nicht das Richtige.
5. **Benennung** wie im Bestand: Methoden `test_<englisch_snake_case>`, Erklärungen in Docblocks auf Deutsch.
6. **Ausführen:** `vendor/bin/phpunit`. Den Stil korrigiert der Git-Hook beim Commit.
7. **Diese Datei ergänzen:** Eintrag in der [Übersicht](#übersicht-aller-tests) und in der Tabelle des Bereichs.

### Bewährte Muster

**Rollen:** `actingAs($s->kursleiter)`. Für mehrere Requests mit derselben Person `actingAs($user->fresh())` verwenden, sonst hängen geladene Relationen vom vorherigen Request am Objekt.

**Data Provider:** Sie laufen, bevor Laravel gestartet ist. Enums (`SurveyStatus`, `Role`, `Classification`) lassen sich direkt verwenden, `config()` dagegen noch nicht:

```php
public static function cases(): array
{
    return ['Quali fertig' => [SurveyStatus::Fertig, false]];
}

#[DataProvider('cases')]
public function test_example(SurveyStatus $status, bool $allowed)
{
    $s = CampScenario::create();
    $s->survey->update(['survey_status_id' => $status]);
    // ...
}
```

**Enums in Requests und URLs:** Formulardaten und Routenparameter sind Zahlen, deshalb dort `->value` übergeben, z. B. `['role_id' => Role::Teilnehmer->value]`. In Models, Abfragen und Assertions das Enum selbst verwenden.

**Externe Dienste nie wirklich aufrufen:**

| Dienst | Vorgehen | Beispiel |
|---|---|---|
| Cevi-DB-API | `Http::fake([...])` und `Http::assertSent(...)` | `CeviDbImportTest` |
| Hitobito-/JEMK-Login | `Socialite::shouldReceive('driver')` mit Mockery-Provider | `HitobitoLoginTest` |
| Mails | `Mail::fake()` und `Mail::assertSent(...)` | `AdminOnlyAreasTest` |
| GitHub-Issues (Feedback senden) | `config(['auth.github.api_url' => 'http://127.0.0.1:9'])` im `setUp()` | `AdminOnlyAreasTest` |
| Artisan-Befehle | `Artisan::shouldReceive('call')` vor der ersten Anfrage | `MaintenanceRoutesTest` |

**Dateien:** Code, der direkt nach `storage_path()` schreibt, bekommt ein eigenes Temp-Verzeichnis (siehe `PostFilesTest`):

```php
protected function setUp(): void
{
    parent::setUp();
    $this->storage = sys_get_temp_dir().'/quali-test-storage-'.uniqid();
    File::makeDirectory($this->storage, 0775, true);
    $this->app->useStoragePath($this->storage);
}

protected function tearDown(): void
{
    File::deleteDirectory($this->storage);
    parent::tearDown();
}
```

**Neue Seite oder Route:**
- `AllRoutesSmokeTest` testet sie automatisch. Bei neuen Routenparametern einen Wert in `routeParameters()` ergänzen.
- Ist die Seite in der Navigation verlinkt, in `PagesRenderTest` bei der passenden Rolle eintragen.
- Kann ein Serverfehler nicht sofort behoben werden, kommt er mit Grund in `KNOWN_ISSUES`.

### Vorlage für einen neuen Eintrag

```markdown
**[`NeuerTest`](Feature/NeuerTest.php)**: wofür der Test zuständig ist.

| Test | Prüft |
|---|---|
| `test_...` | ... |
```
