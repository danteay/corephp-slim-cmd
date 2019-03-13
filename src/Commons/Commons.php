<?php

namespace CorePHP\Slim\Console\Commons;

trait Commons
{
    /**
     * Get the composer command for the environment.
     *
     * @return string
     */
    public function findComposer()
    {
        if (file_exists(getcwd() . '/composer.phar')) {
            return '"' . PHP_BINARY . '" composer.phar';
        }

        return 'composer';
    }
}