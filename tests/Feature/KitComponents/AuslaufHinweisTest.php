<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Tests\Feature\KitComponents;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use ITHilbert\LaravelKit\Kit\UnbekanntesThemeException;
use ITHilbert\LaravelKit\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Throwable;

/**
 * #1715: Laufzeit-Hinweis (E_USER_DEPRECATED) für die auslaufenden Blade-Themes bootstrap5 und tailwind.
 */
final class AuslaufHinweisTest extends TestCase
{
    private const ZWEI_COMPONENTS = '<x-kit::card-main>x</x-kit::card-main><x-kit::input-text name="n" />';

    /**
     * Fängt alle E_USER_DEPRECATED während $aktion.
     *
     * @return list<string>
     */
    private function meldungen(callable $aktion): array
    {
        $gefangen = [];
        set_error_handler(static function (int $nr, string $text) use (&$gefangen): bool {
            $gefangen[] = $text;

            return true;
        }, E_USER_DEPRECATED);

        try {
            $aktion();
        } finally {
            restore_error_handler();
        }

        return $gefangen;
    }

    /** Ein echter Request über eine Probe-Route, deren View den Blade-Text rendert. */
    private function anfrage(string $blade): void
    {
        $url = '/auslauf-probe-'.md5($blade);
        Route::get($url, static fn () => Blade::render($blade));

        $this->get($url)->assertOk();
    }

    /** @return array<string, array{?string, string}> */
    public static function auslaufendeThemes(): array
    {
        return [
            'bootstrap5' => ['bootstrap5', 'bootstrap5'],
            'tailwind (Standard ohne LARAVELKIT_THEME)' => [null, 'tailwind'],
        ];
    }

    // AK18
    #[DataProvider('auslaufendeThemes')]
    public function test_auslaufendes_theme_loest_in_einem_request_genau_eine_meldung_aus(?string $konfig, string $erwartetesTheme): void
    {
        if ($konfig !== null) {
            config(['laravelkit.theme' => $konfig]);
        }
        $this->assertSame($erwartetesTheme, config('laravelkit.theme'));

        $meldungen = $this->meldungen(fn () => $this->anfrage(self::ZWEI_COMPONENTS));

        $this->assertCount(1, $meldungen, 'zwei Components in einem Request: genau eine Meldung');
        $this->assertStringStartsWith('laravel-kit:', $meldungen[0]);
        $this->assertStringContainsString($erwartetesTheme, $meldungen[0]);
        $this->assertStringContainsString('läuft aus', $meldungen[0]);
        $this->assertStringContainsString('LARAVELKIT_THEME=kern', $meldungen[0]);
    }

    // AK18
    public function test_theme_kern_loest_keine_meldung_aus(): void
    {
        config(['laravelkit.theme' => 'kern']);
        $mitKern = $this->meldungen(fn () => $this->anfrage(self::ZWEI_COMPONENTS));

        // Gegenprobe: derselbe Aufbau fängt bei einem auslaufenden Theme eine Meldung
        config(['laravelkit.theme' => 'bootstrap5']);
        $mitAltemTheme = $this->meldungen(fn () => $this->anfrage(self::ZWEI_COMPONENTS.'<x-kit::card-body>y</x-kit::card-body>'));

        $this->assertCount(1, $mitAltemTheme, 'Gegenprobe: Aufbau fängt E_USER_DEPRECATED');
        $this->assertSame([], $mitKern);
    }

    // Randfall 6
    public function test_request_ohne_kit_component_loest_keine_meldung_aus(): void
    {
        config(['laravelkit.theme' => 'bootstrap5']);

        $ohne = $this->meldungen(fn () => $this->anfrage('<p>ohne Kit</p>'));
        $mit = $this->meldungen(fn () => $this->anfrage(self::ZWEI_COMPONENTS));

        $this->assertCount(1, $mit, 'Gegenprobe: mit Kit-Component gibt es die Meldung');
        $this->assertSame([], $ohne);
    }

    // Randfall 6
    public function test_ungueltiges_theme_wirft_die_bisherige_exception_ohne_vorherige_meldung(): void
    {
        config(['laravelkit.theme' => 'gibtsnicht']);

        $geworfen = null;
        $ungueltig = $this->meldungen(function () use (&$geworfen): void {
            try {
                Blade::render(self::ZWEI_COMPONENTS);
            } catch (Throwable $e) {
                $geworfen = $e;
            }
        });

        // Gegenprobe: mit gültigem, auslaufendem Theme fängt der Aufbau eine Meldung
        config(['laravelkit.theme' => 'bootstrap5']);
        $gueltig = $this->meldungen(fn () => Blade::render(self::ZWEI_COMPONENTS));

        $this->assertCount(1, $gueltig, 'Gegenprobe: Aufbau fängt E_USER_DEPRECATED');
        // Blade verpackt die Exception in eine ViewException
        $ursache = $geworfen;
        while ($ursache !== null && ! $ursache instanceof UnbekanntesThemeException) {
            $ursache = $ursache->getPrevious();
        }
        $this->assertInstanceOf(UnbekanntesThemeException::class, $ursache);
        $this->assertSame([], $ungueltig);
    }

    // Randfall 7
    public function test_kit_schreibt_nicht_selbst_ins_log_und_aendert_den_deprecations_kanal_nicht(): void
    {
        config(['laravelkit.theme' => 'bootstrap5']);
        $kanalVorher = config('logging.deprecations');
        $envVorher = getenv('LOG_DEPRECATIONS_CHANNEL');
        $log = Log::spy();

        $meldungen = $this->meldungen(fn () => $this->anfrage(self::ZWEI_COMPONENTS));

        // Gegenprobe: der Spion zeichnet Log-Aufrufe auf
        Log::warning('probe');
        $log->shouldHaveReceived('warning')->with('probe')->once();

        $this->assertCount(1, $meldungen, 'die Meldung ist ausgelöst worden');
        foreach (['channel', 'stack', 'debug', 'info', 'notice', 'error', 'critical', 'alert', 'emergency', 'log'] as $methode) {
            $log->shouldNotHaveReceived($methode);
        }
        $log->shouldHaveReceived('warning')->once();
        $this->assertSame($kanalVorher, config('logging.deprecations'));
        $this->assertSame($envVorher, getenv('LOG_DEPRECATIONS_CHANNEL'));
    }
}
