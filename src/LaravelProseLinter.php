<?php

namespace Beyondcode\LaravelProseLinter;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class LaravelProseLinter
{
    public function lint($namespace = "auth")
    {
        $valePath = base_path("vendor/beyondcode/laravel-prose-linter/src/vale-ai/bin");
        $packagePath = base_path("vendor/beyondcode/laravel-prose-linter/src/");

        // initial tries for invoker plugin via exec
//        exec("./bin/vale " . $packagePath . "README.md", $hm);
//        dump($hm);
//        exec("curl -sfL https://install.goreleaser.com/github.com/ValeLint/vale.sh | sh -s latest", $outputCurl);
//        exec('export PATH="./bin:$PATH"', $outputExec);
//        exec("./bin/vale -v", $output);
//        $command = "./bin/vale --output=line --config=" . $valePath . ".vale.ini " . $packagePath . "README.md";
//        exec($command, $outputTest);
//
//
//        dump("~ " . "curl -sfL https://install.goreleaser.com/github.com/ValeLint/vale.sh | sh -s latest");
//        dump("~ " . 'export PATH="./bin:$PATH"');
//        dump("~ " . "./bin/vale -v");
//
//        dump($output[0]);
//        dump("~ " . $command);
//        dump("------ RESULT ------");
//        foreach ($outputTest as $outputLine) {
//            dump($outputLine);
//        }

        $process = new Process([
            './vale',
            "--ext='.md'",
            "'A, B and very C'"
        ]);
        $process->setWorkingDirectory($valePath);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        echo $process->getOutput();
    }


}
