<?php

namespace Database\Seeders;

use App\Models\Answer;
use App\Models\CampType;
use App\Models\Chapter;
use App\Models\Classification;
use App\Models\Competence;
use App\Models\Group;
use App\Models\Question;
use App\Models\SurveyStatus;
use Illuminate\Database\Seeder;

class BasisdatenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //

        Answer::create(['name' => '-', 'count' => -1, 'description' => 'Nicht in ausreichendem Masse. Es sind Defizite vorhanden']);
        Answer::create(['name' => '0', 'count' => 0, 'description' => 'Knapp. Vereinzelte Defizite können kompensiert werden.']);
        Answer::create(['name' => '+', 'count' => 1, 'description' => 'In gutem Masse']);
        Answer::create(['name' => '++', 'count' => 2, 'description' => 'Ausgezeichnet. Diese Kompetenz ist eine Stärke des Teilnehmenden.']);

        $camp_type_1 = CampType::create(['id' => config('status.camptype_JS1'), 'name' => 'J+S Leiter 1']);
        $camp_type_2 = CampType::create(['id' => config('status.camptype_JS2'), 'name' => 'J+S Leiter 2']);
        $camp_type_3 = CampType::create(['id' => config('status.camptype_Exp'), 'name' => 'Expertenkurs']);

        $chapter_1 = Chapter::create(['number' => 1, 'name' => 'Fachkompetenz']);
        $chapter_2 = Chapter::create(['number' => 2, 'name' => 'Methodenkompetenz']);
        $chapter_3 = Chapter::create(['number' => 3, 'name' => 'Selbstkompetenz']);
        $chapter_4 = Chapter::create(['number' => 4, 'name' => 'Sozialkompetenz']);

        Classification::create(['id' => config('status.classification_red'), 'name' => 'Rot']);
        Classification::create(['id' => config('status.classification_yellow'), 'name' => 'Gelb']);
        Classification::create(['id' => config('status.classification_green'), 'name' => 'Grün']);

        Group::create(['name' => 'Cevi Schweiz', 'shortname' => 'Cevi', 'foreign_id' => 1, 'campgroup' => 1]);
        Group::create(['name' => 'Region Zürich', 'foreign_id' => 2, 'campgroup' => 1]);
        Group::create(['name' => 'Regionalverband AG-SO-LU-ZG', 'foreign_id' => 2569, 'campgroup' => 1]);
        Group::create(['name' => 'RV Winterthur-Schaffhausen', 'foreign_id' => 3, 'campgroup' => 1]);
        Group::create(['name' => 'Region Bern', 'foreign_id' => 1273, 'campgroup' => 1]);

        $question_11 = Question::create(['name' => 'Orientieren / Lagerbau / Seiltechnik / 1. Hilfe', 'chapter_id' => $chapter_1['id'], 'competence' => 'Cevitechnik', 'number' => '1.1']);
        $question_12 = Question::create(['name' => 'Grundlagen LS/T (LA, LS) / Kernlehrmittel (Pädagogisches, Sportmotorisches, Methodisches Konzept)', 'chapter_id' => $chapter_1['id'], 'competence' => 'J+S-Sportfachtechnik', 'number' => '1.2']);
        $question_13 = Question::create(['name' => 'Einschätzen von Risiken (3x3) / Sicherheitskonzept', 'chapter_id' => $chapter_1['id'], 'competence' => 'Sicherheit', 'number' => '1.3']);
        Question::create(['name' => 'Kopf, Herz, Hand / Programmideen / Methodenvielfalt', 'chapter_id' => $chapter_2['id'], 'competence' => 'Vielseitigkeit und Kreativität', 'number' => '2.1']);
        $question_22 = Question::create(['name' => 'Zielgruppengerechte Vermittlung / Motivierendes Lernklima', 'chapter_id' => $chapter_2['id'], 'competence' => 'Führen, Leiten und Auftreten', 'number' => '2.2']);
        $question_23 = Question::create(['name' => 'Planung, Durchführung und Auswertung einer Einheit / Selbstorganisation / Strukturieren von Problemen', 'chapter_id' => $chapter_2['id'], 'competence' => 'Planungs- und Organisationsfähigkeit', 'number' => '2.3']);
        $question_31 = Question::create(['name' => 'Eigene Stärken, Schwächen reflektieren / Lernbereitschaft', 'chapter_id' => $chapter_3['id'], 'competence' => 'Engagement und Selbstreflexion', 'number' => '3.1']);
        $question_32 = Question::create(['name' => 'Rollenbewusstsein / Entscheidungsfähigkeit / Situationsgerechtes Handeln', 'chapter_id' => $chapter_3['id'], 'competence' => 'Verantwortung und Flexibilität', 'number' => '3.2']);
        Question::create(['name' => 'Konzentrationsfähigkeit / Umgang mit Stress, Leistungsdruck / Frustrationstoleranz, Selbstmotivation', 'chapter_id' => $chapter_3['id'], 'competence' => 'Ausdauer und Belastbarkeit (physisch & psychisch)', 'number' => '3.3']);
        $question_41 = Question::create(['name' => 'Offenheit / Anpassungsfähigkeit / Hilfsbereitschaft, Respekt', 'chapter_id' => $chapter_4['id'], 'competence' => 'Teamfähigkeit und Einfühlungsvermögen', 'number' => '4.1']);
        $question_42 = Question::create(['name' => 'Konstruktive Feedbacks / Ansprechen kritischer Aspekte / Annehmen von Kritik', 'chapter_id' => $chapter_4['id'], 'competence' => 'Interaktionsvermögen und Konfliktfähigkeit', 'number' => '4.2']);
        $question_43 = Question::create(['name' => 'Echtheit / Nähe-Distanz / Wille zum Austausch', 'chapter_id' => $chapter_4['id'], 'competence' => 'Kommunikation und Kontaktfähigkeit', 'number' => '4.3']);

        Competence::create(['name' => 'Die Teilnehmenden erfüllen die Mindestanforderungen gemäss Zulassungsprüfung. Sie können ihre Fähigkeiten und Defizite diesbezüglich benennen und übernehmen entsprechend angemessene Aktivitäten bei der Lagerdurchführung.',
            'camp_type_id' => $camp_type_1['id'],
            'question_id' => $question_11['id'], ]);
        Competence::create(['name' => 'Die Teilnehmenden treten als Leiter selbstbewusst auf und vermitteln die Programminhalte motivierend und altersgerecht.',
            'camp_type_id' => $camp_type_1['id'],
            'question_id' => $question_22['id'], ]);
        Competence::create(['name' => 'Die Teilnehmenden können selbständig Lagersportblöcke und Lageraktivitäten planen, durchführen und auswerten. Sie wenden bei der Durchführung methodische Grundsätze (z.B. GAG-Methode, Lernen-Lachen-Leisten) an.',
            'camp_type_id' => $camp_type_1['id'],
            'question_id' => $question_23['id'], ]);
        Competence::create(['name' => 'Die Teilnehmenden engagieren sich als J+S-Leiter und sind sich ihrer Vorbildfunktion bewusst. Sie zeigen Interesse, Lernbereitschaft und Zuverlässigkeit.',
            'camp_type_id' => $camp_type_1['id'],
            'question_id' => $question_31['id'], ]);
        Competence::create(['name' => 'Die Teilnehmenden bringen sich aktiv im Lagerteam ein. Gegenüber den Kindern und Jugendlichen zeigen sie Einfühlungsvermögen und fördern diese bei den Aktivitäten.',
            'camp_type_id' => $camp_type_1['id'],
            'question_id' => $question_41['id'], ]);

        Competence::create(['name' => 'Die Teilnehmenden können Sicherheitskonzepte für Lager erstellen. Bei der Durchführung von Aktivitäten können sie allfällige Risiken beurteilen und angemessen reagieren.',
            'camp_type_id' => $camp_type_2['id'],
            'question_id' => $question_13['id'], ]);
        Competence::create(['name' => 'Die Teilnehmenden können mit ihrem Lagerteam altersgerechte Lager und Aktivitäten planen, durchführen und auswerten. Sie erkennen die unterschiedlichen Fähigkeiten im Team und übergeben entsprechend Verantwortungen. Als Lagerleiter organisieren sie Arbeitsschritte und koordinieren diese. Sie behalten dabei den Gesamtüberblick.',
            'camp_type_id' => $camp_type_2['id'],
            'question_id' => $question_23['id'], ]);
        Competence::create(['name' => 'Die Teilnehmenden sind sich der Rolle als J+S-Lagerleiter bewusst. Sie können auch unter Stress Situationen im Gesamtzusammenhang analysieren, fällen Entscheidungen unter Miteinbezug des Teams und verantworten diese.',
            'camp_type_id' => $camp_type_2['id'],
            'question_id' => $question_32['id'], ]);
        Competence::create(['name' => 'Die Teilnehmenden fördern eine positive Teamkultur. Konflikte in der Lagergemeinschaft nehmen sie frühzeitig wahr. Auch bei Gleichaltrigen können sie kritische Aspekte angemessen thematisieren, allenfalls unter Miteinbezug des J+S-Coachs.',
            'camp_type_id' => $camp_type_2['id'],
            'question_id' => $question_42['id'], ]);
        Competence::create(['name' => 'Die Teilnehmenden kommunizieren adressatengerecht. Sie suchen das Gespräch mit Kindern und Eltern, diskutieren mit Teamlern und informieren extern Ansprechpersonen.',
            'camp_type_id' => $camp_type_2['id'],
            'question_id' => $question_43['id'], ]);

        Competence::create(['name' => 'Die Teilnehmenden können die Konzepte des Kernlehrmittels in sportartspezifischen Unterrichtssituationen anwenden und deren wichtigsten Begriffe anhand erlebter Praxissequenzen situationsbezogen erklären.',
            'camp_type_id' => $camp_type_3['id'],
            'question_id' => $question_12['id'], ]);
        Competence::create(['name' => 'Die Teilnehmenden schaffen mit einer zielgruppengerechten Vermittlung ein förderndes Lernklima. Sie berücksichtigen in ihrem eigenen Unterricht wesentliche Merkmale der Erwachsenenbildung (z.B. Metaebene).',
            'camp_type_id' => $camp_type_3['id'],
            'question_id' => $question_22['id'], ]);
        Competence::create(['name' => 'Die Teilnehmenden können (z.B. nach ARIVA) strukturierte Ausbildungssequenzen planen, durchführen und auswerten. Sie erreichen mittels zielgerichteter Methodenwahl klar fokussierte Ausbildungsziele.',
            'camp_type_id' => $camp_type_3['id'],
            'question_id' => $question_23['id'], ]);
        Competence::create(['name' => 'Die Teilnehmenden können Situationen analysieren und sich selber reflektieren. Sie benennen ihre Stärken und Schwächen und leiten daraus gewinnbringende Handlungsstrategien ab.',
            'camp_type_id' => $camp_type_3['id'],
            'question_id' => $question_31['id'], ]);
        Competence::create(['name' => 'Die Teilnehmenden sind sich der Rolle als J+S-Experte im Rahmen der Erwachsenenbildung bewusst. Sie handeln situationsgerecht und bringen sich ihrer Rolle entsprechend ein.',
            'camp_type_id' => $camp_type_3['id'],
            'question_id' => $question_32['id'], ]);
        Competence::create(['name' => 'Die Teilnehmenden geben konstruktives Feedback, auch betreffend kritischer Aspekte. Im Rahmen von Qualifikationen wenden sie die Grundsätze von Fördergesprächen an.',
            'camp_type_id' => $camp_type_3['id'],
            'question_id' => $question_42['id'], ]);

        SurveyStatus::create(['id' => config('status.survey_neu'), 'name' => 'Neu']);
        SurveyStatus::create(['id' => config('status.survey_1offen'), 'name' => '1. Selbsteinschätzung Offen']);
        SurveyStatus::create(['id' => config('status.survey_2offen'), 'name' => '2 Selbsteinschätzung Offen']);
        SurveyStatus::create(['id' => config('status.survey_tnAbgeschlossen'), 'name' => 'TN Selbsteinschätzung Abgeschlossen']);
        SurveyStatus::create(['id' => config('status.survey_fertig'), 'name' => 'Qualifikationsprozess Abgeschlossen']);
    }
}
