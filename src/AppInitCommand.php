<?php
/**
 * New project command for Slim
 *
 * PHP Version 7.1
 *
 * @category  Command
 * @package   CorePHP_Slim_Cmd
 * @author    Eduardo Aguilar <dante.aguilar41@gmail.com>
 * @copyright 2019 Eduardo Aguilar
 * @license   https://github.com/danteay/corephp-slim-cmd/LICENSE Apache-2.0
 * @link      https://github.com/danteay/corephp-slim-cmd
 */

namespace CorePHP\Slim\Console;

use Symfony\Component\Process\Process;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * NewProjectCommand
 *
 * @category  Command
 * @package   CorePHP_Slim_Cmd
 * @author    Eduardo Aguilar <dante.aguilar41@gmail.com>
 * @copyright 2019 Eduardo Aguilar
 * @license   https://github.com/danteay/corephp-slim-cmd/LICENSE Apache-2.0
 * @link      https://github.com/danteay/corephp-slim-cmd
 */
class AppInitCommand extends Command
{
    /**
     * Command name
     *
     * @var string
     */
    protected static $defaultName = 'app:init';

    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setDescription('Create a new Slim project')
            ->addArgument(
                'projectdir',
                InputArgument::REQUIRED,
                'The username of the user.'
            );
    }

    /**
     * Execute the command.
     *
     * @param InputInterface  $input  Input of the command
     * @param OutputInterface $output Output for the command line
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $directory = getcwd() . '/' . $input->getArgument('projectdir');
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
        $process->run(
            function ($type, $line) use ($output) {
                $output->write($line);
            }
        );

        $output->writeln('<comment>Project is allready installed!!</comment>');
    }

    /**
     * Get the composer command for the environment.
     *
     * @return string
     */
    protected function findComposer()
    {
        if (file_exists(getcwd() . '/composer.phar')) {
            return '"' . PHP_BINARY . '" composer.phar';
        }

        return 'composer';
    }

    /**
     * Verify that the application does not already exist.
     *
     * @param string $directory Directory path to evaluate
     *
     * @return void
     */
    protected function verifyApplicationDoesntExist($directory)
    {
        if (is_dir($directory)) {
            throw new \RuntimeException('Application already exists: '.$directory);
        }
    }
}