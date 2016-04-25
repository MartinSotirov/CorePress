<?php
namespace MartinSotirov\CorePress\Utils;

class ClassParser
{
    private $sourceCode = '';
    public $className = '';
    public $namespace = '';

    /**
     * @param string $sourceCode The contents of a PHP file
     */
    private function __construct($sourceCode) {

        // extract namespace
        if (preg_match('/namespace\s(?P<namespace>.+);/', $sourceCode, $matches)) {
            $this->namespace = '\\' . trim($matches['namespace'], '\\') . '\\';
        }

        // extract the class name
        if (preg_match('/\s+class\s(?P<class>\w+)/', $sourceCode, $matches)) {
            $this->className = $matches['class'];
        }
    }

    /**
     * Parses the contents of a PHP file
     * @param  string      $sourceCode The contents of the PHP file
     * @return ClassParser             An object containing the class name
     */
    public static function parse($sourceCode)
    {
        return new self($sourceCode);
    }

    /**
     * Returns the namespaced class name
     * @return string
     */
    public function getNamespacedClassName()
    {
        return $this->namespace . $this->className;
    }
}

