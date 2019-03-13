<?php
/**
 * Add new dependency for slim
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
use CorePHP\Slim\Console\Commons\Commons;
use CorePHP\Slim\Console\Helpers\Dependencies\Database\Eloquent\Dependency as EloquentDependency;

/**
 * AddDependencyCommand
 *
 * @category  class
 * @package   CorePHP_Slim_Cmd
 * @author    Eduardo Aguilar <dante.aguilar41@gmail.com>
 * @copyright 2019 Eduardo Aguilar
 * @license   https://github.com/danteay/corephp-slim-cmd/LICENSE Apache-2.0
 * @link      https://github.com/danteay/corephp-slim-cmd
 */
class AddDependencyCommand extends Command
{
    use Commons;

    /**
     * Command name
     *
     * @var string
     */
    protected static $defaultName = 'dependency:add';

    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setDescription('Add a new project dependency')
            ->addArgument(
                'dependency',
                InputArgument::REQUIRED,
                'Dependency name'
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
        $output->writeln('<info>Installing dependency... </info>');

        switch ($input->getArgument('dependency')) {
            case 'eloquent':
                (new EloquentDependency($output))->setUp();
                break;

            default:
                throw new \RuntimeException(
                    "Invalid dependency. Chose one of the next available dependencies \n" .
                    "[-] eloquent"
                );
        }

        $output->writeln('<info>Dependency added successfuly...</info>');
    }
}