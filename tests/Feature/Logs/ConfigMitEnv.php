<?php

declare(strict_types=1);

namespace ITHilbert\LaravelKit\Tests\Feature\Logs;

/**
 * Wertet src/Config/laravelkit.php mit gesetzter bzw. entfernter Umgebungsvariable neu aus.
 */
trait ConfigMitEnv
{
    /**
     * @return array<string, mixed>
     */
    private function configMitEnv(string $name, ?string $wert): array
    {
        $alt = [$_ENV[$name] ?? null, $_SERVER[$name] ?? null, getenv($name)];

        try {
            if ($wert === null) {
                unset($_ENV[$name], $_SERVER[$name]);
                putenv($name);
            } else {
                $_ENV[$name] = $_SERVER[$name] = $wert;
                putenv("{$name}={$wert}");
            }

            return require dirname(__DIR__, 3).'/src/Config/laravelkit.php';
        } finally {
            foreach (['_ENV', '_SERVER'] as $i => $global) {
                if ($alt[$i] === null) {
                    unset($GLOBALS[$global][$name]);
                } else {
                    $GLOBALS[$global][$name] = $alt[$i];
                }
            }
            $alt[2] === false ? putenv($name) : putenv("{$name}={$alt[2]}");
        }
    }
}
