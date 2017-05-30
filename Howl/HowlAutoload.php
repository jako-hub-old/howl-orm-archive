<?php
/**
 * This class initializes the autoload functionality of Howl-ORM
 * @author Jorge Alejandro Quiroz Serna (Jako) <alejo.jko@gmail.com>
 * @license MIT License
 */

namespace Howl;

final class HowlAutoload{
    /**
     * Init the autoload function
     */
    public static function initAutoload(){
        self::defineConstants();
        spl_autoload_register('\Howl\HowlAutoload::autoload');
    }

    /**
     * Define constants used by Howl-ORM
     */
    private static function defineConstants(){
        # Define a root directory from Howl-ORM.
        define("HOWL_ROOT", realpath(__DIR__ . '/..'));
        # Define a directory separator only if it's not defined.
        if(!defined('DS')){ define("DS", DIRECTORY_SEPARATOR); }
    }

    /**
     * Loads the Howl-ORM classes.
     * @param string $className
     */
    public static function autoload(string $className){
        $path = HOWL_ROOT . DS;
        $path .= str_replace('\\', DS, $className) . ".php";
        if(file_exists($path)){ require_once $path; }
    }
}