<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Tests\Unit;

use Illuminate\Http\Request;
use ITHilbert\LaravelKit\Helpers\Browser;
use ITHilbert\LaravelKit\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

/**
 * Dashboard #729: Browser-Helper ohne Warnung bei fehlenden Server-Variablen, IP über request()->ip().
 */
final class BrowserOhneServerTest extends TestCase
{
    /** @var array<string, mixed> */
    private array $serverVorher = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->serverVorher = $_SERVER;
    }

    protected function tearDown(): void
    {
        $_SERVER = $this->serverVorher;
        SymfonyRequest::setTrustedProxies([], SymfonyRequest::HEADER_X_FORWARDED_FOR);
        parent::tearDown();
    }

    /**
     * Baut Anfrage und $_SERVER gleich auf; fehlende Schlüssel fehlen wirklich.
     *
     * @param  array<string, string>  $server
     */
    private function anfrage(array $server): void
    {
        foreach (['REMOTE_ADDR', 'HTTP_USER_AGENT', 'HTTP_X_FORWARDED_FOR', 'HTTP_CLIENT_IP', 'HTTP_SEC_CH_UA_PLATFORM', 'HTTP_ACCEPT_LANGUAGE'] as $schluessel) {
            unset($_SERVER[$schluessel]);
        }
        $request = Request::create('/', 'GET');
        foreach (['REMOTE_ADDR', 'HTTP_USER_AGENT'] as $schluessel) {
            $request->server->remove($schluessel);
        }
        foreach ($server as $schluessel => $wert) {
            $_SERVER[$schluessel] = $wert;
            $request->server->set($schluessel, $wert);
            if (str_starts_with($schluessel, 'HTTP_')) {
                $request->headers->set(str_replace('_', '-', substr($schluessel, 5)), $wert);
            }
        }
        $this->app->instance('request', $request);
    }

    /**
     * @return array{0: mixed, 1: list<string>}
     */
    private function ueberwacht(callable $aufruf): array
    {
        $meldungen = [];
        set_error_handler(function (int $nr, string $text) use (&$meldungen): bool {
            $meldungen[] = $text;

            return true;
        });

        try {
            $leer = [];
            $unbenutzt = $leer['gibt-es-nicht'];
            $this->assertCount(1, $meldungen, 'Der Handler muss eine Warnung erkennen.');
            $meldungen = [];

            try {
                $ergebnis = $aufruf();
            } catch (\Throwable $e) {
                $meldungen[] = get_class($e).': '.$e->getMessage();
                $ergebnis = null;
            }
        } finally {
            restore_error_handler();
        }

        return [$ergebnis, $meldungen];
    }

    /**
     * @return array<string, array{string}>
     */
    public static function proxyKopfzeilen(): array
    {
        return [
            'X-Forwarded-For' => ['HTTP_X_FORWARDED_FOR'],
            'Client-IP' => ['HTTP_CLIENT_IP'],
        ];
    }

    // AK1
    public function test_get_ip_liefert_request_ip(): void
    {
        $this->anfrage(['REMOTE_ADDR' => '10.0.0.5']);
        // $_SERVER weicht ab, Quelle ist die Anfrage von Laravel
        unset($_SERVER['REMOTE_ADDR']);

        $this->assertSame('10.0.0.5', request()->ip());
        $this->assertSame('10.0.0.5', Browser::getIP());
    }

    // AK1
    #[DataProvider('proxyKopfzeilen')]
    public function test_proxy_kopfzeile_von_nicht_vertrauenswuerdigem_absender_wird_nicht_uebernommen(string $kopf): void
    {
        $this->anfrage(['REMOTE_ADDR' => '10.0.0.5', $kopf => '1.2.3.4']);

        // Gegenprobe: die Kopfzeile ist in der Anfrage angekommen
        $this->assertSame('1.2.3.4', request()->server($kopf));

        $this->assertSame('10.0.0.5', Browser::getIP());
    }

    // AK1
    public function test_x_forwarded_for_von_vertrauenswuerdigem_proxy_wird_uebernommen(): void
    {
        $this->anfrage(['REMOTE_ADDR' => '10.0.0.5', 'HTTP_X_FORWARDED_FOR' => '1.2.3.4']);
        SymfonyRequest::setTrustedProxies(['10.0.0.5'], SymfonyRequest::HEADER_X_FORWARDED_FOR);

        // Gegenprobe: Laravel selbst übernimmt die Kopfzeile bei vertrautem Proxy
        $this->assertSame('1.2.3.4', request()->ip());

        $this->assertSame('1.2.3.4', Browser::getIP());
    }

    // Randfall 1
    public function test_ohne_proxy_kopfzeilen_ergibt_get_ip_die_remote_addr(): void
    {
        $this->anfrage(['REMOTE_ADDR' => '10.0.0.5']);

        $this->assertSame('10.0.0.5', $_SERVER['REMOTE_ADDR']);
        $this->assertSame('10.0.0.5', Browser::getIP());
    }

    // AK2
    public function test_get_ip_ohne_remote_addr_liefert_na_ohne_warnung(): void
    {
        $this->anfrage(['HTTP_USER_AGENT' => 'Mozilla/5.0 Firefox/100.0']);

        // Gegenprobe: es gibt keine IP in der Anfrage
        $this->assertNull(request()->ip());
        $this->assertArrayNotHasKey('REMOTE_ADDR', $_SERVER);

        [$ip, $meldungen] = $this->ueberwacht(fn () => Browser::getIP());

        $this->assertSame([], $meldungen);
        $this->assertSame('N/A', $ip);
    }

    // AK2
    public function test_get_id_ohne_remote_addr_erzeugt_keine_warnung(): void
    {
        $this->anfrage(['HTTP_USER_AGENT' => 'Mozilla/5.0 Firefox/100.0']);

        // Gegenprobe: es gibt keine IP in der Anfrage
        $this->assertNull(request()->ip());

        [$id, $meldungen] = $this->ueberwacht(fn () => Browser::getID());

        $this->assertSame([], $meldungen);
        $this->assertIsString($id);
        $this->assertStringStartsWith('N/A', $id);
    }

    // AK3
    public function test_get_browser_ohne_user_agent_liefert_na_ohne_warnung(): void
    {
        $this->anfrage(['REMOTE_ADDR' => '10.0.0.5']);

        // Gegenprobe: kein User-Agent gesetzt
        $this->assertArrayNotHasKey('HTTP_USER_AGENT', $_SERVER);

        [$browser, $meldungen] = $this->ueberwacht(fn () => Browser::getBrowser());

        $this->assertSame([], $meldungen);
        $this->assertSame('N/A', $browser);
    }

    // AK3
    public function test_get_platform_ohne_user_agent_liefert_na_ohne_warnung(): void
    {
        $this->anfrage(['REMOTE_ADDR' => '10.0.0.5']);

        $this->assertArrayNotHasKey('HTTP_USER_AGENT', $_SERVER);

        [$plattform, $meldungen] = $this->ueberwacht(fn () => Browser::getPlatform());

        $this->assertSame([], $meldungen);
        $this->assertSame('N/A', $plattform);
    }

    // AK3
    public function test_is_robot_ohne_user_agent_erzeugt_keine_warnung(): void
    {
        $this->anfrage(['REMOTE_ADDR' => '10.0.0.5']);

        $this->assertArrayNotHasKey('HTTP_USER_AGENT', $_SERVER);

        [$robot, $meldungen] = $this->ueberwacht(fn () => Browser::isRobot());

        $this->assertSame([], $meldungen);
        $this->assertIsBool($robot);
    }

    // AK3
    public function test_is_mobil_ohne_user_agent_erzeugt_keine_warnung(): void
    {
        $this->anfrage(['REMOTE_ADDR' => '10.0.0.5']);

        $this->assertArrayNotHasKey('HTTP_USER_AGENT', $_SERVER);

        [$mobil, $meldungen] = $this->ueberwacht(fn () => Browser::isMobil());

        $this->assertSame([], $meldungen);
        $this->assertFalse($mobil);
    }

    // AK3
    public function test_get_id_ohne_user_agent_erzeugt_keine_warnung(): void
    {
        $this->anfrage(['REMOTE_ADDR' => '10.0.0.5']);

        $this->assertArrayNotHasKey('HTTP_USER_AGENT', $_SERVER);

        [$id, $meldungen] = $this->ueberwacht(fn () => Browser::getID());

        $this->assertSame([], $meldungen);
        $this->assertIsString($id);
        $this->assertStringStartsWith('10.0.0.5', $id);
    }

    // AK2 und AK3
    public function test_get_id_ganz_ohne_server_variablen_erzeugt_keine_warnung(): void
    {
        $this->anfrage([]);

        $this->assertArrayNotHasKey('HTTP_USER_AGENT', $_SERVER);
        $this->assertNull(request()->ip());

        [$id, $meldungen] = $this->ueberwacht(fn () => Browser::getID());

        $this->assertSame([], $meldungen);
        $this->assertIsString($id);
    }

    // Randfall 2
    public function test_vorhandener_user_agent_ergibt_browser_und_plattform_wie_vorher(): void
    {
        $this->anfrage([
            'REMOTE_ADDR' => '10.0.0.5',
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Gecko/20100101 Firefox/100.0',
        ]);

        [$browser, $meldungen] = $this->ueberwacht(fn () => Browser::getBrowser());
        $this->assertSame([], $meldungen);
        $this->assertSame('Firefox', $browser);

        [$plattform, $meldungen] = $this->ueberwacht(fn () => Browser::getPlatform());
        $this->assertSame([], $meldungen);
        $this->assertSame('Windows', $plattform);
    }

    // AK4
    public function test_readme_nennt_request_ip_und_trusted_proxies_im_browser_abschnitt(): void
    {
        $readme = (string) file_get_contents(dirname(__DIR__, 2).'/README.md');

        // Gegenprobe: die README ist gelesen
        $this->assertStringContainsString('Browser', $readme);

        $this->assertMatchesRegularExpression('/Browser.*request\(\)->ip\(\)/su', $readme);
        $this->assertMatchesRegularExpression('/Trusted ?Proxies/iu', $readme);
        $this->assertMatchesRegularExpression('/X-Forwarded-For/u', $readme);
    }
}
