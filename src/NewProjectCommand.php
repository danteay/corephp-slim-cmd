<?php

namespace CorePHP\Slim\Console;

use Symfony\Component\Process\Process;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class NewProjectCommand extends Command
{
    protected static $defaultName = 'app:init';

    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setDescription('Create a new Slim project')
            ->addArgument('projectdir', InputArgument::REQUIRED, 'The username of the user.');
    }

    /**
     * Execute the command.
     *
     * @param  InputInterface  $input
     * @param  OutputInterface  $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $directory = getcwd().'/'.$input->getArgument('projectdir');
        $output->writeln('<info>Creating project: '. $directory . '</info>');

        $this->verifyApplicationDoesntExist($directory);

        $composer = $this->findComposer();

        $commands = [
            $composer,
            'create-project',
            'corephp/slim-scafold',
            $directory
        ];

        $process = new Process($commands, null, null, null, null);
        $process->run(function ($type, $line) use ($output) {
            $output->write($line);
        });

        $output->writeln('<comment>Project is allready installed!!</comment>');
    }

    /**
     * Get the composer command for the environment.
     *
     * @return string
     */
    protected function findComposer()
    {
        if (file_exists(getcwd().'/composer.phar')) {

            return '"'.PHP_BINARY.'" composer.phar';
        }
        return 'composer';
    }

    /**
     * Verify that the application does not already exist.
     *
     * @param  string $directory
     *
     * @return void
     */
    protected function verifyApplicationDoesntExist($directory)
    {
        if (is_dir($directory)) {
            throw new \RuntimeException('Application already exists: ' . $directory);
        }
    }
}