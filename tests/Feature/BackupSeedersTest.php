<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Tests\Feature;

use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use ITHilbert\LaravelKit\Tests\TestCase;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Ersatz für den Befehl `iseed` (orangehill/iseed ist im Paket nicht installiert):
 * schreibt jeden Aufruf mit Argument und Optionen mit.
 */
class FakeIseedBefehl extends Command
{
    protected $signature = 'iseed {tables} {--force} {--orderby=} {--direction=} {--skip-fk-checks}';

    protected $description = 'Fake für iseed (Tests)';

    /** @var array<int, array{tables: string, options: array<string, mixed>}> */
    public static array $aufrufe = [];

    /** @var array<int, string> Tabellen, bei denen der Fake mit Exit-Code 1 endet */
    public static array $scheitern = [];

    public function handle(): int
    {
        $tabellen = (string) $this->argument('tables');
        self::$aufrufe[] = ['tables' => $tabellen, 'options' => $this->options()];

        return in_array($tabellen, self::$scheitern, true) ? 1 : 0;
    }
}

/** Markiert, dass ein Seeder aus DatabaseSeeder::run() wirklich lief. */
class BackupSeedersMarkerSeeder extends Seeder
{
    public static int $gelaufen = 0;

    public function run(): void
    {
        self::$gelaufen++;
    }
}

/**
 * Schnittstelle für install:iSeed (vom Coder umzusetzen): Der Befehl führt die Kommandozeile
 * über die Container-Bindung `laravelkit.exec` aus — ein Callable `fn (string $kommandozeile): int`
 * (Rückgabe = Exit-Code), das beim Ausführen (in handle()) per `app('laravelkit.exec')` aufgelöst wird.
 * Ohne Bindung führt der Befehl die Kommandozeile selbst aus (exec/passthru).
 */
class BackupSeedersTest extends TestCase
{
    private string $dbPfad;

    protected function setUp(): void
    {
        parent::setUp();

        FakeIseedBefehl::$aufrufe = [];
        FakeIseedBefehl::$scheitern = [];
        BackupSeedersMarkerSeeder::$gelaufen = 0;

        Artisan::registerCommand(new FakeIseedBefehl);

        $this->dbPfad = sys_get_temp_dir().'/kit-backup-seeders-'.bin2hex(random_bytes(4));
        File::ensureDirectoryExists($this->dbPfad.'/seeders');
        $this->app->useDatabasePath($this->dbPfad);

        Schema::create('users', function (Blueprint $t) {
            $t->id();
            $t->string('name');
        });
        Schema::create('migrations', function (Blueprint $t) {
            $t->id();
            $t->string('migration');
        });
        Schema::create('sessions', function (Blueprint $t) {
            $t->string('id')->primary();
            $t->text('payload');
        });
        Schema::create('posts', function (Blueprint $t) {
            $t->id();
            $t->string('title');
        });
        Schema::create('items', function (Blueprint $t) {
            $t->string('code')->primary();
            $t->string('name');
        });
        Schema::create('post_user', function (Blueprint $t) {
            $t->unsignedInteger('a_id');
            $t->unsignedInteger('b_id');
            $t->primary(['a_id', 'b_id']);
        });
        Schema::create('nopk', function (Blueprint $t) {
            $t->string('label');
            $t->integer('x');
        });
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->dbPfad);

        parent::tearDown();
    }

    /** @return array{0: int, 1: string} */
    private function lauf(): array
    {
        $puffer = new BufferedOutput;
        $code = Artisan::call('db:backup-seeders', [], $puffer);

        return [$code, $puffer->fetch()];
    }

    /** @return array<int, string> */
    private function exportierteTabellen(): array
    {
        $tabellen = [];
        foreach (FakeIseedBefehl::$aufrufe as $aufruf) {
            foreach (explode(',', $aufruf['tables']) as $t) {
                $tabellen[] = $t;
            }
        }

        return $tabellen;
    }

    /** @return array<string, mixed> */
    private function optionenFuer(string $tabelle): array
    {
        foreach (FakeIseedBefehl::$aufrufe as $aufruf) {
            if (explode(',', $aufruf['tables']) === [$tabelle]) {
                return $aufruf['options'];
            }
        }
        $this->fail("Kein iseed-Aufruf für Tabelle {$tabelle}");
    }

    private function seederDatei(string $namespace): string
    {
        $inhalt = <<<PHP
<?php

namespace {$namespace};

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        \$this->call(\\ITHilbert\\LaravelKit\\Tests\\Feature\\BackupSeedersMarkerSeeder::class);
    }
}

PHP;
        $pfad = $this->dbPfad.'/seeders/DatabaseSeeder.php';
        file_put_contents($pfad, $inhalt);

        return $pfad;
    }

    // AK1
    public function test_ignore_tables_der_config_enthalten_migrations_und_sessions(): void
    {
        $ignore = config('laravelkit.backup_seeders.ignore_tables');

        $this->assertContains('sessions', $ignore);
        $this->assertContains('migrations', $ignore);
    }

    // AK1
    public function test_export_enthaelt_weder_migrations_noch_sessions_aber_users(): void
    {
        [$code] = $this->lauf();

        $tabellen = $this->exportierteTabellen();
        $this->assertContains('users', $tabellen); // Gegenprobe: der Aufbau greift
        $this->assertNotContains('migrations', $tabellen);
        $this->assertNotContains('sessions', $tabellen);
        $this->assertSame(0, $code);
    }

    // AK2
    public function test_tabelle_mit_einfachem_primaerschluessel_wird_nach_diesem_sortiert(): void
    {
        $this->lauf();

        $this->assertSame('id', $this->optionenFuer('users')['orderby']);
        $this->assertSame('code', $this->optionenFuer('items')['orderby']); // PK ist nicht die erste Spalte-Namen-Konvention "id"
        $direction = $this->optionenFuer('users')['direction'] ?? 'asc';
        $this->assertSame('asc', strtolower((string) ($direction === '' ? 'asc' : $direction)));
    }

    // AK2
    public function test_tabelle_ohne_primaerschluessel_wird_nach_erster_spalte_sortiert_mit_meldung(): void
    {
        [, $ausgabe] = $this->lauf();

        $this->assertSame('label', $this->optionenFuer('nopk')['orderby']);
        $this->assertMatchesRegularExpression('/^.*nopk.*label.*$|^.*label.*nopk.*$/m', $ausgabe);
        // Gegenprobe: für Tabellen mit PK keine solche Meldung zu "users"
        $this->assertDoesNotMatchRegularExpression('/^.*users.*Primärschlüssel.*$/mu', $ausgabe);
    }

    // AK2
    public function test_tabelle_mit_zusammengesetztem_primaerschluessel_wird_nach_erster_spalte_sortiert_mit_meldung(): void
    {
        [, $ausgabe] = $this->lauf();

        $this->assertSame('a_id', $this->optionenFuer('post_user')['orderby']);
        $this->assertMatchesRegularExpression('/^.*post_user.*a_id.*$|^.*a_id.*post_user.*$/m', $ausgabe);
    }

    // AK2
    public function test_zwei_laeufe_ohne_datenaenderung_rufen_iseed_identisch_auf(): void
    {
        $this->lauf();
        $erster = FakeIseedBefehl::$aufrufe;
        $this->assertNotEmpty($erster);

        FakeIseedBefehl::$aufrufe = [];
        $this->lauf();

        $this->assertSame($erster, FakeIseedBefehl::$aufrufe);
    }

    // AK3
    public function test_jeder_iseed_aufruf_bekommt_skip_fk_checks(): void
    {
        $this->lauf();

        $this->assertNotEmpty(FakeIseedBefehl::$aufrufe); // Gegenprobe: es gab Aufrufe
        foreach (FakeIseedBefehl::$aufrufe as $aufruf) {
            $this->assertTrue($aufruf['options']['skip-fk-checks'], 'skip-fk-checks fehlt bei '.$aufruf['tables']);
        }
    }

    // AK4
    public function test_database_seeder_bekommt_sperre_als_erste_anweisung_mit_markierung(): void
    {
        $pfad = $this->seederDatei('Tmp\\S'.bin2hex(random_bytes(4)));
        $vorher = file_get_contents($pfad);

        $this->lauf();

        $nachher = file_get_contents($pfad);
        $this->assertNotSame($vorher, $nachher);
        $this->assertSame(1, substr_count($nachher, '// laravelkit:seed-sperre'));
        $markierung = strpos($nachher, '// laravelkit:seed-sperre');
        $this->assertGreaterThan(strpos($nachher, 'function run'), $markierung);
        $this->assertLessThan(strpos($nachher, '$this->call('), $markierung);
    }

    // AK4
    public function test_sperre_bricht_ausserhalb_local_und_testing_mit_meldung_ab_und_laesst_local_testing_durch(): void
    {
        $namespace = 'Tmp\\S'.bin2hex(random_bytes(4));
        $pfad = $this->seederDatei($namespace);
        $this->lauf();
        require $pfad;
        $klasse = $namespace.'\\DatabaseSeeder';

        // Gegenprobe: in local und testing läuft der Seeder
        foreach (['local', 'testing'] as $umgebung) {
            $this->app['env'] = $umgebung;
            BackupSeedersMarkerSeeder::$gelaufen = 0;
            (new $klasse)->setContainer($this->app)->__invoke();
            $this->assertSame(1, BackupSeedersMarkerSeeder::$gelaufen, "Seeder läuft in {$umgebung}");
        }

        foreach (['production', 'staging'] as $umgebung) {
            $this->app['env'] = $umgebung;
            BackupSeedersMarkerSeeder::$gelaufen = 0;
            $meldung = null;
            try {
                (new $klasse)->setContainer($this->app)->__invoke();
            } catch (\Throwable $e) {
                $meldung = $e->getMessage();
            }
            $this->assertNotNull($meldung, "Sperre greift in {$umgebung}");
            $this->assertStringContainsString('db:seed', $meldung);
            $this->assertStringContainsString($umgebung, $meldung);
            $this->assertSame(0, BackupSeedersMarkerSeeder::$gelaufen, 'Kein Seeder darf laufen');
        }
    }

    // AK4
    public function test_sperre_wird_nicht_doppelt_eingefuegt(): void
    {
        $pfad = $this->seederDatei('Tmp\\S'.bin2hex(random_bytes(4)));
        $original = file_get_contents($pfad);

        $this->lauf();
        $erster = file_get_contents($pfad);
        $this->assertNotSame($original, $erster); // Gegenprobe: der erste Lauf fügt ein

        $this->lauf();

        $this->assertSame($erster, file_get_contents($pfad));
        $this->assertSame(1, substr_count(file_get_contents($pfad), '// laravelkit:seed-sperre'));
    }

    // AK4
    public function test_fehlender_database_seeder_wird_nicht_angelegt_mit_hinweis(): void
    {
        $pfad = $this->dbPfad.'/seeders/DatabaseSeeder.php';
        $this->assertFileDoesNotExist($pfad);

        [$code, $ausgabe] = $this->lauf();

        $this->assertFileDoesNotExist($pfad);
        $this->assertStringContainsString('DatabaseSeeder', $ausgabe);
        $this->assertSame(0, $code); // Gegenprobe: Export lief trotzdem
        $this->assertContains('users', $this->exportierteTabellen());
    }

    // AK5
    public function test_install_iseed_fuehrt_composer_require_dev_aus(): void
    {
        // Schutz: Ohne die Schnittstelle würde der Befehl wirklich `composer require` im Paketordner ausführen.
        $quelle = (string) file_get_contents(dirname(__DIR__, 2).'/src/Commands/LaravelKitInstalliSeed.php');
        $this->assertStringContainsString('laravelkit.exec', $quelle, 'install:iSeed nutzt die Container-Bindung laravelkit.exec noch nicht');

        $zeilen = [];
        $this->app->instance('laravelkit.exec', function (string $kommando) use (&$zeilen): int {
            $zeilen[] = $kommando;

            return 0;
        });

        $code = Artisan::call('install:iSeed');

        $this->assertSame(0, $code);
        $this->assertCount(1, $zeilen);
        $this->assertSame('composer require --dev orangehill/iseed', preg_replace('/\s+/', ' ', trim($zeilen[0])));
    }

    // Randfall 1
    public function test_ohne_exportierbare_tabellen_warnung_und_kein_iseed_aufruf(): void
    {
        // Gegenprobe: mit Standard-Config gibt es Aufrufe
        $this->lauf();
        $this->assertNotEmpty(FakeIseedBefehl::$aufrufe);
        FakeIseedBefehl::$aufrufe = [];

        config(['laravelkit.backup_seeders.ignore_tables' => [
            'users', 'migrations', 'sessions', 'posts', 'items', 'post_user', 'nopk',
        ]]);

        [, $ausgabe] = $this->lauf();

        $this->assertStringContainsString('Keine Tabellen zum Exportieren gefunden.', $ausgabe);
        $this->assertSame([], FakeIseedBefehl::$aufrufe);
    }

    // Randfall 2
    public function test_fehler_von_iseed_meldet_tabelle_macht_weiter_und_endet_mit_exit_code_1(): void
    {
        [$codeOhneFehler] = $this->lauf();
        $this->assertSame(0, $codeOhneFehler); // Gegenprobe: ohne Fehler Exit-Code 0
        FakeIseedBefehl::$aufrufe = [];
        FakeIseedBefehl::$scheitern = ['posts'];

        [$code, $ausgabe] = $this->lauf();

        $this->assertSame(1, $code);
        $this->assertMatchesRegularExpression('/^.*posts.*$/m', $ausgabe);
        $tabellen = $this->exportierteTabellen();
        $this->assertContains('posts', $tabellen);
        $this->assertContains('users', $tabellen); // kommt alphabetisch nach posts: es ging weiter
    }
}
