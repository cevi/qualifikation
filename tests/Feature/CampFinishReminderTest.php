<?php

namespace Tests\Feature;

use App\Mail\CampFinishReminder;
use App\Models\Camp;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Tests\Support\CampScenario;
use Tests\TestCase;

/**
 * camps:finish-reminder (letzter Tag im Monat, 08:00): Erinnerung an die Kursleitung, Kurse 3 Monate nach dem Schlussdatum
 * abzuschliessen. Die Regel selbst: CampChecksTest::test_camp_must_be_finished_three_months_after_end_date.
 */
class CampFinishReminderTest extends TestCase
{
    public function test_only_overdue_camps_are_reminded_to_their_campleader(): void
    {
        Mail::fake();
        $s = CampScenario::create();
        $s->camp->update(['end_date' => now()->subMonths(4)->toDateString()]);
        $past = now()->subMonths(4)->toDateString();
        Camp::factory()->create(['end_date' => now()->subMonths(2)->toDateString()]);
        Camp::factory()->create(['end_date' => $past, 'finish' => true]);
        Camp::factory()->create(['end_date' => $past, 'demo' => true]);
        Camp::factory()->create(['end_date' => $past, 'global_camp' => true]);
        Camp::factory()->create(['end_date' => null]);

        $this->artisan('camps:finish-reminder')->expectsOutput('1 Erinnerung(en) verschickt')->assertSuccessful();

        Mail::assertSent(CampFinishReminder::class, 1);
        Mail::assertSent(CampFinishReminder::class, fn (CampFinishReminder $mail) => $mail->build()->hasTo($s->kursleiter->email)
            && $mail->camps->pluck('id')->all() === [$s->camp->id]);
    }

    public function test_one_mail_per_campleader_with_all_overdue_camps(): void
    {
        Mail::fake();
        $s = CampScenario::create();
        $s->camp->update(['end_date' => now()->subMonths(4)->toDateString()]);
        $older = Camp::factory()->create(['user_id' => $s->kursleiter->id, 'end_date' => now()->subYear()->toDateString()]);

        $this->artisan('camps:finish-reminder')->assertSuccessful();

        Mail::assertSent(CampFinishReminder::class, 1);
        Mail::assertSent(CampFinishReminder::class, function (CampFinishReminder $mail) use ($s, $older) {
            $mail->build();

            return $mail->camps->pluck('id')->all() === [$older->id, $s->camp->id]
                && $mail->subject === 'Bitte schliesse deine 2 vergangenen Kurse ab';
        });
    }

    public function test_mail_names_camp_end_date_and_way_to_finish(): void
    {
        $s = CampScenario::create();
        $s->camp->update(['name' => 'Kurs <b>Belp</b>', 'end_date' => '2026-03-05']);

        $mail = new CampFinishReminder($s->kursleiter, collect([$s->camp->fresh()]));
        $html = $mail->render();

        $this->assertSame('Bitte schliesse deinen Kurs «Kurs <b>Belp</b>» ab', $mail->subject);
        $this->assertStringContainsString('Kurs &lt;b&gt;Belp&lt;/b&gt;', $html);
        $this->assertStringNotContainsString('<b>Belp</b>', $html);
        $this->assertStringContainsString('05.03.2026', $html);
        $this->assertStringContainsString('Kurs abschliessen / löschen', $html);
        $this->assertStringContainsString(url('/home'), $html);
        $this->assertStringContainsString('jeweils Ende Monat', $html);
    }

    public function test_reminder_is_scheduled_on_the_last_day_of_each_month(): void
    {
        // lastDayOfMonth() rechnet den Tag aus, wenn der Scheduler startet (schedule:run jede Minute neu)
        $lastDay = now()->endOfMonth()->day;
        Artisan::call('schedule:list');

        $this->assertMatchesRegularExpression("/0 8 $lastDay \\* \\*\\s+php artisan camps:finish-reminder/", Artisan::output());
    }
}
