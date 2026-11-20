<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Tests\Feature;

use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use ITHilbert\LaravelKit\Tests\TestCase;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Ersatz für `iseed`: schreibt Aufrufe mit und trägt wie der echte iseed
 * `$this->call(<Tabelle>TableSeeder::class);` in DatabaseSeeder::run() ein,
 * sofern dort nicht schon genau `$this->call(<Klasse>::class)` steht
 * (ein Aufruf innerhalb von `call([...])` zählt nicht — der echte iseed hängt dann doppelt an).
 */
class OnlyTablesIseedFake extends Command
{
    protected $signature = 'iseed {tables} {--force} {--orderby=} {--direction=} {--skip-fk-checks}';

    protected $description = 'Fake für iseed (Tests only_tables)';

    /** @var array<int, string> */
    public static array $tabellen = [];

    /** @var array<int, string> Tabellen, bei denen der Fake eine Zeile angehängt hat */
    public static array $angehaengt = [];

    public function handle(): int
    {
        $tabelle = (string) $this->argument('tables');
        self::$tabellen[] = $tabelle;

        $pfad = database_path('seeders/DatabaseSeeder.php');
        if (! is_file($pfad)) {
            return 0;
        }

        $klasse = ucfirst($tabelle).'TableSeeder';
        $inhalt = (string) file_get_contents($pfad);
        if (strpos($inhalt, "\$this->call({$klasse}::class)") === false) {
            $inhalt = preg_replace('/(run\(\).+?)}/us', "$1    \$this->call({$klasse}::class);\n    }", $inhalt);
            file_put_contents($pfad, $inhalt);
            self::$angehaengt[] = $tabelle;
        }

        return 0;
    }
}

class BackupSeedersOnlyTablesTest extends TestCase
{
    private string $dbPfad;

    protected function setUp(): void
    {
        parent::setUp();

        OnlyTablesIseedFake::$tabellen = [];
        OnlyTablesIseedFake::$angehaengt = [];
        Artisan::registerCommand(new OnlyTablesIseedFake);

        $this->dbPfad = sys_get_temp_dir().'/kit-only-tables-'.bin2hex(random_bytes(4));
        File::ensureDirectoryExists($this->dbPfad.'/seeders');
        $this->app->useDatabasePath($this->dbPfad);

        foreach (['vorlagen', 'kunden'] as $name) {
            Schema::create($name, function (Blueprint $t) {
                $t->id();
                $t->string('name');
            });
        }
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

    private function seederPfad(): string
    {
        return $this->dbPfad.'/seeders/DatabaseSeeder.php';
    }

    private function seederSchreiben(string $runKoerper): string
    {
        $inhalt = <<<PHP
<?php

namespace Tmp;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
{$runKoerper}
    }
}

PHP;
        file_put_contents($this->seederPfad(), $inhalt);

        return $this->seederPfad();
    }

    private function vorkommen(string $klasse): int
    {
        return substr_count((string) file_get_contents($this->seederPfad()), $klasse.'::class');
    }

    // AK1
    public function test_only_tables_exportiert_nur_die_genannte_tabelle(): void
    {
        $this->lauf();
        $this->assertContains('kunden', OnlyTablesIseedFake::$tabellen); // Gegenprobe: ohne only_tables beide
        $this->assertContains('vorlagen', OnlyTablesIseedFake::$tabellen);
        OnlyTablesIseedFake::$tabellen = [];

        config(['laravelkit.backup_seeders.only_tables' => ['vorlagen']]);
        $this->lauf();

        $this->assertSame(['vorlagen'], OnlyTablesIseedFake::$tabellen);
    }

    // AK2
    public function test_only_tables_geht_vor_ignore_tables(): void
    {
        config([
            'laravelkit.backup_seeders.only_tables' => ['vorlagen'],
            'laravelkit.backup_seeders.ignore_tables' => ['vorlagen'],
        ]);

        $this->lauf();

        $this->assertSame(['vorlagen'], OnlyTablesIseedFake::$tabellen);
    }

    // AK3
    public function test_ohne_only_tables_gilt_ignore_tables(): void
    {
        $this->assertSame([], config('laravelkit.backup_seeders.only_tables')); // Standard

        config(['laravelkit.backup_seeders.ignore_tables' => ['kunden']]);
        $this->lauf();

        $this->assertContains('vorlagen', OnlyTablesIseedFake::$tabellen);
        $this->assertNotContains('kunden', OnlyTablesIseedFake::$tabellen);
    }

    // AK4
    public function test_unbekannte_tabelle_in_only_tables_warnt_und_endet_mit_exit_code_0(): void
    {
        config(['laravelkit.backup_seeders.only_tables' => ['vorlagen', 'gibt_es_nicht']]);

        [$code, $ausgabe] = $this->lauf();

        $this->assertSame(0, $code);
        $this->assertSame(['vorlagen'], OnlyTablesIseedFake::$tabellen);
        $this->assertMatchesRegularExpression('/gibt_es_nicht.*existiert nicht|existiert nicht.*gibt_es_nicht/u', $ausgabe);
    }

    // AK5
    public function test_von_iseed_doppelt_angehaengte_zeile_wird_entfernt(): void
    {
        config(['laravelkit.backup_seeders.ignore_tables' => ['kunden']]); // nur vorlagen wird exportiert, unabhängig von only_tables
        $this->seederSchreiben("        \$this->call([\n            VorlagenTableSeeder::class,\n        ]);");

        $this->lauf();

        $this->assertSame(['vorlagen'], OnlyTablesIseedFake::$angehaengt); // Gegenprobe: iseed hat angehängt
        $this->assertSame(1, $this->vorkommen('VorlagenTableSeeder'));
        $this->assertStringNotContainsString('$this->call(VorlagenTableSeeder::class);', (string) file_get_contents($this->seederPfad()));
    }

    // AK6
    public function test_zweiter_lauf_laesst_database_seeder_byte_gleich(): void
    {
        config(['laravelkit.backup_seeders.ignore_tables' => ['kunden']]); // nur vorlagen wird exportiert, unabhängig von only_tables
        $pfad = $this->seederSchreiben("        \$this->call([\n            VorlagenTableSeeder::class,\n        ]);");

        $this->lauf();
        $nachErstem = (string) file_get_contents($pfad);
        $this->lauf();

        $this->assertSame(['vorlagen', 'vorlagen'], OnlyTablesIseedFake::$angehaengt); // Gegenprobe: iseed hängt in beiden Läufen an
        $this->assertSame(1, $this->vorkommen('VorlagenTableSeeder'));
        $this->assertSame($nachErstem, (string) file_get_contents($pfad));
    }

    // AK7
    public function test_neu_eingetragener_aufruf_bleibt_genau_einmal_stehen(): void
    {
        config(['laravelkit.backup_seeders.ignore_tables' => ['kunden']]); // nur vorlagen wird exportiert, unabhängig von only_tables
        $this->seederSchreiben('        $this->call(KundenTableSeeder::class);');

        $this->lauf();

        $this->assertSame(['vorlagen'], OnlyTablesIseedFake::$angehaengt);
        $this->assertSame(1, $this->vorkommen('VorlagenTableSeeder'));
        $this->assertSame(1, $this->vorkommen('KundenTableSeeder'));
    }

    // Randfall 1
    public function test_vorhandene_einzelzeile_bleibt_unveraendert_waehrend_angehaengtes_entfernt_wird(): void
    {
        $this->seederSchreiben("        \$this->call(VorlagenTableSeeder::class);\n        \$this->call([\n            KundenTableSeeder::class,\n        ]);");

        $this->lauf();

        $inhalt = (string) file_get_contents($this->seederPfad());
        $this->assertSame(['kunden'], OnlyTablesIseedFake::$angehaengt); // Gegenprobe: iseed hat für kunden angehängt
        $this->assertSame(1, $this->vorkommen('KundenTableSeeder'));
        $this->assertSame(1, substr_count($inhalt, '$this->call(VorlagenTableSeeder::class);'));
    }

    // Randfall 2
    public function test_fehlender_database_seeder_laeuft_ohne_fehler_durch_die_bereinigung(): void
    {
        config(['laravelkit.backup_seeders.only_tables' => ['vorlagen']]);
        $this->assertFileDoesNotExist($this->seederPfad());

        [$code] = $this->lauf();

        $this->assertSame(0, $code);
        $this->assertSame(['vorlagen'], OnlyTablesIseedFake::$tabellen);
        $this->assertFileDoesNotExist($this->seederPfad());
    }
}
