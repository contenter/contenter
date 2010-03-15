<?php

/**
 * Библиотека функций ядра.
 *
 */
abstract class FinalView 
{

    /**
     * Абстрактная фабрика. Создает объект указанного типа из указанного "пэкиджа".
     *
     * @param   string  $package
     * @param   string  $type
     * @param   mixed   $arg1[, mixed  $arg2, …]
     * @return  object
     * @throws  FinalView_Exception
     */
    static public function factory($package, $type) 
    {
        $type = self::_str_camelize($type);
        $class = false === strpos($package, '%')
            ? $package . '_' . $type : sprintf($package, $type);
        try {
            Zend_Loader::loadClass($class);
        } catch (Zend_Exception $e) {
            throw new FinalView_Exception($e->getMessage());
        }
        $args = array_slice(func_get_args(), 2);
        // ReflectionClass::newInstanceArgs() появился только в PHP 5.1.3, плюс
        // ко всему, он ругается при создании объекта класса, у которого явно не
        // объявлен конструктор. это — более универсальный вариант :|
        return call_user_func_array(array(new ReflectionClass($class), 'newInstance'), $args);
    }
    
    /**
     * Convert string from "foo-bar-baz" notation into "fooBarBaz" (Camel case?). 
     *
     * @param   string  $string
     * @return  string
     */
    static private function _str_camelize($string) 
    {
        return preg_replace('/(^|\-)([a-z])/e', "strtoupper('\\2')", $string);
    }

}
