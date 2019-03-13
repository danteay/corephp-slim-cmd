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

use Symfony\Component\Process\Process;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * NewControllerCommand
 *
 * @category  Command
 * @package   CorePHP_Slim_Cmd
 * @author    Eduardo Aguilar <dante.aguilar41@gmail.com>
 * @copyright 2019 Eduardo Aguilar
 * @license   https://github.com/danteay/corephp-slim-cmd/LICENSE Apache-2.0
 * @link      https://github.com/danteay/corephp-slim-cmd
 */
class NewControllerCommand extends Command
{
    /**
     * Command name
     *
     * @var string
     */
    protected static $defaultName = 'controller:new';

    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setDescription('Create a new project Controller')
            ->addArgument(
                'className',
                InputArgument::REQUIRED,
                'Name of the Controller'
            )
            ->addArgument(
                'subfolder',
                InputArgument::OPTIONAL,
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
        $pathFile = getcwd() . '/src/app/controllers';

        if (!is_dir($pathFile)) {
            throw new \RuntimeException(
                "Can't find contollers folder at src/app/controllers"
            );
        }

        $className = $this->createClassName(
            $input->getArgument('className')
        );

        $subfolder = $this->parseSubfolders(
            $input->getArgument('subfolder')
        );

        $finalFolder = $pathFile . '/' . $subfolder;

        $fullPath = $this->makeFullPathFile($subfolder, $className);

        if (!is_dir($finalFolder)) {
            $this->makeFolders(
                explode('/' , $subfolder),
                $pathFile,
                $output
            );
        }

        if (is_file($fullPath)) {
            throw new \RuntimeException("Controller $className allready exists...");
        }

        $this->makeClass($subfolder, $fullPath, $className);

        $output->writeln("<info>Controller $className created...</info>");
    }

    /**
     * Make controller file
     *
     * @param string $subfolder
     * @param string $fullPath
     * @param string $className
     *
     * @return void
     */
    protected function makeClass($subfolder, $fullPath, $className)
    {
        $file = file_get_contents(__DIR__ . '/prototypes/controller.php.txt');

        $namespace = null;

        if (!empty($subfolder)) {
            $namespace = explode('/', $subfolder);

            $namespace = array_map(function ($item) {
                return ucfirst($item);
            }, $namespace);

            $namespace = '\\' . implode('\\', $namespace);
        }

        $file = str_replace('{{className}}', $className, $file);
        $file = str_replace('{{namespace}}', $namespace, $file);

        file_put_contents($fullPath, $file);
    }

    /**
     * Add Controller postfix to the class name
     *
     * @param string $className Class name argument of the command line
     *
     * @return string
     */
    protected function createClassName($className)
    {
        $className = ucfirst($className);

        if (!preg_match('/Controller/', $className)) {
            $className .= 'Controller';
        }

        return $className;
    }

    /**
     * Create full path to the controller file
     *
     * @param string $subfolder Sub folder in the controller folder
     * @param string $className Name of the controller to generate
     *
     * @return string
     */
    protected function makeFullPathFile($subfolder, $className)
    {
        $pathFile = getcwd() . '/src/app/controllers';
        $fullPath = $pathFile . '/' . $subfolder . '/' . $className . '.php';

        return $fullPath;
    }

    /**
     * Clean subfolders to make path
     *
     * @param string $subfolder
     *
     * @return string
     */
    protected function parseSubfolders($subfolder)
    {
        $subfolder = str_replace('\\', '/', $subfolder);
        $subfolder = explode('/', $subfolder);

        return implode('/', $subfolder);
    }

    /**
     * Create sub directories
     *
     * @param array  $folders Folders that has to be checked
     * @param string $base    Base folder
     *
     * @return boolean
     */
    protected function makeFolders($folders, $base, $out)
    {
        var_dump($folders);

        if (empty($folders)) return true;

        $folder = $base . '/' . $folders[0];

        if (!is_dir($folder)) {
            mkdir($folder);
        }

        $out->writeln($folder);

        return $this->makeFolders(array_splice($folders, 1), $folder, $out);
    }
}