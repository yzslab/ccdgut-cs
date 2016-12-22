<?php

/**
 * Created by PhpStorm.
 * User: zhensheng
 * Date: 12/21/16
 * Time: 6:36 PM
 */
namespace CCDGUT_ClassSelector;

class ThreadHolder extends \Thread {
    private $object;
    private $method_name;
    private $argv;

    public function __construct($object, $method_name, ...$argv) {
        $this->object = $object;
        $this->method_name = $method_name;
        $this->argv = $argv;
    }

    public function run() {
        $reflection = new \ReflectionClass(get_class($this->object));
        $method = $reflection->getMethod($this->method_name);
        $method->invoke($this->object, ...$this->argv);
    }
}