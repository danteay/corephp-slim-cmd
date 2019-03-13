<?php

namespace CorePHP\Slim\Console\Helpers\Dependencies\Database\Eloquent;

class Model
{
    private $className;

    private $tableName;

    public function __construct($className, $tableName)
    {
        $this->className = $className;
        $this->tableName = $tableName;
    }

    public function build()
    {
        $filePath = $this->validate();

        $prototype = file_get_contents(__DIR__ . '/prototypes/eloquent-model.php.txt');
        $prototype = str_replace('{{className}}', $this->className, $prototype);
        $prototype = str_replace('{{tableName}}', $this->tableName, $prototype);

        file_put_contents($filePath, $prototype);
    }

    private function validate()
    {
        $pathFile = getcwd() . '/src/app/models';

        $fileName = $pathFile . '/' . $this->className . '.php';

        if (!is_dir($pathFile)) {
            throw new \RuntimeException("Cant find models folder at src/app/models");
        }

        if (is_file($fileName)) {
            throw new \RuntimeException("Model {$this->className} allready exists...");
        }

        return $fileName;
    }
}