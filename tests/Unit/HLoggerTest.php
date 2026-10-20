<?php

namespace ITHilbert\LaravelKit\Tests\Unit;

use ITHilbert\LaravelKit\Helpers\hLogger;
use Tests\TestCase;

class HLoggerTest extends TestCase
{
    private string $tempStorage;

    private string $originalStorage;

    protected function setUp(): void
    {
        parent::setUp();

        $this->originalStorage = $this->app->storagePath();
        $this->tempStorage = sys_get_temp_dir().'/hlogger-test-'.uniqid('', true);
        mkdir($this->tempStorage, 0777, true);
        $this->app->useStoragePath($this->tempStorage);
    }

    protected function tearDown(): void
    {
        $this->app->useStoragePath($this->originalStorage);
        $this->deleteDirectory($this->tempStorage);

        parent::tearDown();
    }

    // AK1
    public function test_schreibt_in_tagesdatei_mit_vierstelligem_jahr(): void
    {
        mkdir($this->tempStorage.'/logs/hLogger', 0777, true);

        hLogger::Log('x');

        $erwartet = $this->tempStorage.'/logs/hLogger/'.date('Y-m-d').'.txt';
        $this->assertFileExists($erwartet);
        $this->assertSame("x\n", file_get_contents($erwartet));
    }

    // AK1
    public function test_legt_keine_datei_mit_falschem_jahresformat_an(): void
    {
        mkdir($this->tempStorage.'/logs/hLogger', 0777, true);

        hLogger::Log('x');

        $this->assertSame(
            [date('Y-m-d').'.txt'],
            array_map('basename', glob($this->tempStorage.'/logs/hLogger/*'))
        );
    }

    // AK2
    public function test_legt_fehlendes_verzeichnis_an_und_wirft_keine_exception(): void
    {
        $this->assertDirectoryDoesNotExist($this->tempStorage.'/logs/hLogger');

        hLogger::Log('x');

        $datei = $this->tempStorage.'/logs/hLogger/'.date('Y-m-d').'.txt';
        $this->assertFileExists($datei);
        $this->assertSame("x\n", file_get_contents($datei));
    }

    // AK3
    public function test_haengt_an_vorhandene_tagesdatei_an(): void
    {
        mkdir($this->tempStorage.'/logs/hLogger', 0777, true);

        hLogger::Log('erste');
        hLogger::Log('zweite');

        $datei = $this->tempStorage.'/logs/hLogger/'.date('Y-m-d').'.txt';
        $this->assertSame("erste\nzweite\n", file_get_contents($datei));
    }

    private function deleteDirectory(string $dir): void
    {
        if (! is_dir($dir)) {
            return;
        }
        foreach (array_diff(scandir($dir), ['.', '..']) as $item) {
            $pfad = $dir.'/'.$item;
            is_dir($pfad) ? $this->deleteDirectory($pfad) : unlink($pfad);
        }
        rmdir($dir);
    }
}
