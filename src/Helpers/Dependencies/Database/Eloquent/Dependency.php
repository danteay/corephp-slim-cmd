<?php

namespace CorePHP\Slim\Console\Helpers\Dependencies\Database\Eloquent;

use CorePHP\Slim\Console\Commons\Commons;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Output\OutputInterface;


class Dependency
{
    use Commons;

    /**
     * Console output interface
     *
     * @var OutputInterface
     */
    private $out;

    /**
     * Dependency constructor
     *
     * @param [type] $databaseUrl
     * @param OutputInterface $out
     */
    public function __construct(OutputInterface $out)
    {
        $this->out = $out;
    }

    /**
     * setup all the needed configuration for eloquent dependency
     *
     * @return void
     */
    public function setUp()
    {
        $this->install();
        $this->addSettings();
        $this->addDepednency();
    }

    /**
     * Require with composer the corephp/slim-eloquent module
     *
     * @return void
     */
    private function install()
    {
        $this->out->writeln('<info>Installing from composer...</info>');

        $composer = $this->findComposer();
        $output = $this->out;

        $commands = [
            $composer,
            'require',
            'corephp/slim-eloquent'
        ];

        $process = new Process($commands, null, null, null, null);
        $process->run(
            function ($type, $line) use ($output) {
                $output->write($line);
            }
        );
    }

    /**
     * Add eloquent settings to the main settigns file in the project
     *
     * @return void
     */
    private function addSettings()
    {
        $this->out->writeln('<info>Adding settings...</info>');

        $settings = file_get_contents(__DIR__ . '/prototypes/settings.txt');

        $file = getcwd() . '/src/settings.php';

        if (!is_file($file)) {
            throw new \RuntimeException("File settings.php not found");
        }

        $content = file_get_contents($file);
        $content = str_replace('#NEW_SETTINGS', $settings, $content);
        file_put_contents($file, $content);
    }

    /**
     * Add dependency file to the project to be loaded
     *
     * @return void
     */
    private function addDepednency()
    {
        $this->out->writeln('<info>Adding dependency file...</info>');

        $path = getcwd() . '/src/config/dependencies/';

        if (!is_dir($path)) {
            throw new \RuntimeException("Dependencies dir was nort found at $path");
        }

        file_put_contents(
            $path . '/eloquent.php',
            file_get_contents(__DIR__ . '/prototypes/dependency.php.txt')
        );
    }
}