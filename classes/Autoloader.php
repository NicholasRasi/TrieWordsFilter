<?php

namespace TrieWordsFilter;


/**
 *
 * Autoloader for classes
 *
 */
class Autoloader
{
    /**
     * Register the Autoloader with SPL
     *
     */
    public static function Register() {
        if (function_exists('__autoload')) {
            //    Register any existing autoloader function with SPL, so we don't get any clashes
            spl_autoload_register('__autoload');
        }
        //    Register ourselves with SPL
        return spl_autoload_register(['TrieWordsFilter\Autoloader', 'Load']);
    }


    /**
     * Autoload a class identified by name
     *
     * @param    string    $pClassName    Name of the object to load
     */
    public static function Load($pClassName) {
        if ((class_exists($pClassName, FALSE)) || (strpos($pClassName, 'TrieWordsFilter\\') !== 0)) {
            // Either already loaded, or not a Trie class request
            return FALSE;
        }

        $pClassFilePath = __DIR__ . DIRECTORY_SEPARATOR .
                          'src' . DIRECTORY_SEPARATOR .
                          str_replace('TrieWordsFilter\\', '', $pClassName) .
                          '.php';

        if ((file_exists($pClassFilePath) === FALSE) || (is_readable($pClassFilePath) === FALSE)) {
            // Can't load
            return FALSE;
        }
        require($pClassFilePath);
    }
}