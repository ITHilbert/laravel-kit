<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Tests\Unit;

use Illuminate\Http\Request;
use ITHilbert\LaravelKit\Helpers\Browser;
use ITHilbert\LaravelKit\Tests\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * Dashboard #735: Safari ist kein Robot, URL-Helper ohne fest verdrahtete Domains.
 */
final class BrowserAltlastenTest extends TestCase
{
    private const SAFARI = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 13_0) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.0 Safari/605.1.15';

    private const GOOGLEBOT = 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)';

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
        parent::tearDown();
    }

    private function mitUserAgent(string $userAgent): void
    {
        $_SERVER['HTTP_USER_AGENT'] = $userAgent;
        $request = Request::create('/', 'GET', [], [], [], ['HTTP_USER_AGENT' => $userAgent]);
        $this->app->instance('request', $request);
    }

    private function mitAnfrage(string $url, ?string $vorherige = null): void
    {
        $server = $vorherige === null ? [] : ['HTTP_REFERER' => $vorherige];
        $this->app->instance('request', Request::create($url, 'GET', [], [], [], $server));
    }

    // AK5
    public function test_safari_user_agent_wird_als_safari_erkannt_und_ist_kein_robot(): void
    {
        $this->mitUserAgent(self::SAFARI);

        // Gegenprobe: der User-Agent ist als Safari erkannt
        $this->assertSame('Safari', Browser::getBrowser());

        $this->assertFalse(Browser::isRobot());
    }

    // AK5
    public function test_bot_user_agent_bleibt_robot(): void
    {
        $this->mitUserAgent(self::GOOGLEBOT);

        // Gegenprobe: der User-Agent ist als Bot erkannt
        $this->assertSame('Googlebot', Browser::getBrowser());

        $this->assertTrue(Browser::isRobot());
    }

    // AK5
    public function test_uebrige_whitelist_eintraege_bleiben_kein_robot(): void
    {
        $this->mitUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) Gecko/20100101 Firefox/100.0');
        $this->assertSame('Firefox', Browser::getBrowser());
        $this->assertFalse(Browser::isRobot());
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function anfragen(): array
    {
        return [
            'andere Domain' => ['https://beispiel.test/kontakt', '/kontakt'],
            'andere Domain mit Port' => ['http://beispiel.test:8080/a/b', '/a/b'],
            'bisher unterstützte Domain (Wächter)' => ['https://www.it-hilbert.com/kontakt', '/kontakt'],
            'Startseite' => ['https://beispiel.test/', 'index'],
            'Startseite bisher unterstützte Domain (Wächter)' => ['https://www.it-hilbert.com/', 'index'],
        ];
    }

    // AK6
    #[DataProvider('anfragen')]
    public function test_get_url_liefert_pfad_oder_index_unabhaengig_von_der_domain(string $url, string $erwartet): void
    {
        $this->mitAnfrage($url);

        // Gegenprobe: die Anfrage ist aufgebaut
        $this->assertStringStartsWith('http', url()->current());

        $this->assertSame($erwartet, Browser::getURL());
    }

    /**
     * @return array<string, array{string, string}>
     */
    public static function vorherigeUrls(): array
    {
        return [
            'Pfad mit Query' => ['https://beispiel.test/a/b?x=1', '/a/b?x=1'],
            'Pfad ohne Query' => ['https://beispiel.test/kontakt', '/kontakt'],
            'Startseite' => ['https://beispiel.test/', 'index'],
            'bisher unterstützte Domain (Wächter)' => ['https://www.it-hilbert.com/kontakt', '/kontakt'],
        ];
    }

    // AK6
    #[DataProvider('vorherigeUrls')]
    public function test_get_url_bevor_liefert_pfad_mit_query_oder_index(string $vorherige, string $erwartet): void
    {
        $this->mitAnfrage('https://beispiel.test/aktuell', $vorherige);

        // Gegenprobe: die vorherige URL ist in der Anfrage angekommen
        $this->assertSame($vorherige, url()->previous());

        $this->assertSame($erwartet, Browser::getURLBevor());
    }

    // AK6
    public function test_browser_quelltext_enthaelt_keine_domains(): void
    {
        $quelle = (string) file_get_contents(dirname(__DIR__, 2).'/src/Helpers/Browser.php');

        // Gegenprobe: Quelltext gelesen
        $this->assertStringContainsString('class Browser', $quelle);

        $this->assertStringNotContainsString('localhost:8000', $quelle);
        $this->assertStringNotContainsString('it-hilbert.com', $quelle);
    }
}
