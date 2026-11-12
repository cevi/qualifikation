<?php

namespace Tests\Feature;

use App\Mail\CampCreated;
use App\Mail\CampFinishReminder;
use App\Mail\FeedbackCreated;
use App\Models\Feedback;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Support\CampScenario;
use Tests\TestCase;

/**
 * Alle Mails im Look der App (Theme resources/views/vendor/mail/html/themes/qualitool.css): Logo als PNG, blauer
 * Knopf, du-Form, keine englischen Reste, kein Codeblock aus eingerückten Zeilen, Text von Benutzern maskiert.
 */
class MailLookTest extends TestCase
{
    public static function mails(): array
    {
        return [
            'Kurs erstellt' => ['created'],
            'Kurs abschliessen' => ['reminder'],
            'Feedback' => ['feedback'],
            'Passwort zurücksetzen' => ['reset'],
            'E-Mail bestätigen' => ['verify'],
        ];
    }

    #[DataProvider('mails')]
    public function test_mail_looks_like_the_app(string $mail): void
    {
        $html = $this->render($mail, CampScenario::create());

        $this->assertStringContainsString('<img src="'.asset('img/logo-mail.png').'"', $html);
        $this->assertStringContainsString('ein Tool von', $html);
        $this->assertStringNotContainsString('All rights reserved', $html);
        $this->assertStringNotContainsString('<pre', $html, 'eingerückte Zeilen werden in Markdown ein Codeblock');
        $this->assertDoesNotMatchRegularExpression('/\b(Sie|Ihr|Ihre|Ihnen)\b/', strip_tags($html));
        $this->assertDoesNotMatchRegularExpression('/\b(Regards|Hello|trouble clicking|Whoops)\b/', $html);
        if ($mail !== 'feedback') {
            // Theme-Farbe blue-700 steht nach dem Versand inline am Knopf
            $this->assertMatchesRegularExpression('/class="button button-primary"[^>]*background-color: #1a56db/', $html);
        }
    }

    public function test_feedback_text_is_shown_as_text_with_line_breaks(): void
    {
        $s = CampScenario::create();
        $s->feedback->update(['feedback' => "- erste Zeile <script>alert(1)</script>\n    [Link](https://example.com)"]);

        $html = $this->render('feedback', $s);

        $this->assertStringContainsString('- erste Zeile &lt;script&gt;alert(1)&lt;/script&gt;<br>    [Link](https://example.com)', $html);
        $this->assertStringNotContainsString('<script>alert(1)', $html);
        $this->assertStringNotContainsString('href="https://example.com"', $html);
        $this->assertStringNotContainsString('<li>', $html);
    }

    public function test_camp_created_mail_points_to_the_overview(): void
    {
        $s = CampScenario::create();

        $html = $this->render('created', $s);

        // Laravel hängt beim Versand Inline-Stile an jedes Element
        $this->assertMatchesRegularExpression('/<strong[^>]*>'.preg_quote(e($s->camp->name), '/').'<\/strong>/', $html);
        $this->assertStringContainsString('href="'.url('/home').'"', $html);
        $this->assertStringContainsString('«Erste Schritte»', $html);
    }

    private function render(string $mail, CampScenario $s): string
    {
        return match ($mail) {
            'created' => (new CampCreated($s->kursleiter, $s->camp))->render(),
            'reminder' => (new CampFinishReminder($s->kursleiter, collect([$s->camp])))->render(),
            'feedback' => (new FeedbackCreated(Feedback::findOrFail($s->feedback->id)))->render(),
            'reset' => (string) (new ResetPassword('token-123'))->toMail($s->kursleiter)->render(),
            'verify' => (string) (new VerifyEmail)->toMail($s->kursleiter)->render(),
        };
    }
}
