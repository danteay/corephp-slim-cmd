<?php
/**
 * New Model command for corephp slim scafold
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

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * NewModelCommand
 *
 * @category  Command
 * @package   CorePHP_Slim_Cmd
 * @author    Eduardo Aguilar <dante.aguilar41@gmail.com>
 * @copyright 2019 Eduardo Aguilar
 * @license   https://github.com/danteay/corephp-slim-cmd/LICENSE Apache-2.0
 * @link      https://github.com/danteay/corephp-slim-cmd
 */
class NewModelCommand extends Command
{
    /**
     * Command name
     *
     * @var string
     */
    protected static $defaultName = 'new:model';

    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setDescription('Create a new project Model')
            ->addArgument(
                'className',
                InputArgument::REQUIRED,
                'Name of the model'
            )
            ->addArgument(
                'tableName',
                InputArgument::REQUIRED,
                'Name of the table linked to the model'
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
        $className = ucfirst($input->getArgument('className'));
        $tableName = $input->getArgument('tableName');

        $depends = file_get_contents(getcwd() . '/composer.json');

        if (preg_match('/corephp\/slim-eloquent/', $depends)) {
            $this->eloquentModel($className, $tableName);
        } else if (preg_match('/corephp\/slim-doctrine/', $depends)) {
            throw new \RuntimeException(
                "Module not implemented. It'll be available soon.."
            );
        } else {
            throw new \RuntimeException(
                "No ORM dependency detected. \n" .
                "For Eloquent ORM you should install corephp/slim-eloquent",
                1
            );

        }

        $output->writeln("<info>Model $className created...</info>");
    }

    /**
     * Generate an eloquent Model
     *
     * @param string $className Class name of the file
     * @param string $tableName Related table name
     *
     * @return void
     */
    private function eloquentModel($className, $tableName)
    {
        $pathFile = getcwd() . '/src/app/models';

        $fileName = $pathFile . '/' . $className . '.php';

        if (!is_dir($pathFile)) {
            throw new \RuntimeException("Cant find models folder at src/app/models");
        }

        if (is_file($fileName)) {
            throw new \RuntimeException("Model $className allready exists...");
        }

        $prototype = file_get_contents(__DIR__ . '/prototypes/eloquent-model.php.txt');
        $prototype = str_replace('{{className}}', $className, $prototype);
        $prototype = str_replace('{{tableName}}', $tableName, $prototype);

        file_put_contents($pathFile . '/' . $className . '.php', $prototype);
    }
}