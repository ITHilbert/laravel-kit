<?php
namespace ITHilbert\LaravelKit\App\Classes;

use Exception;
use Symfony\Component\Process\Process;

class ShellCommand
{
    public static function execute($cmd): string
    {
        $process = Process::fromShellCommandline($cmd);

        $processOutput = '';

        $captureOutput = function ($type, $line) use (&$processOutput) {
            $processOutput .= $line;
        };

        $process->setTimeout(null)
            ->run($captureOutput);

        if ($process->getExitCode()) {
            $exception = new Exception($cmd . " - " . $processOutput);

            throw $exception;
        }
        return $processOutput;
    }

}
