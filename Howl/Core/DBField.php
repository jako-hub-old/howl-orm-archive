<?php
/**
 * This class represents a database table field.
 * @author Jorge Alejandro Quiroz Serna (Jako) <alejo.jko@gmail.com>
 * @license MIT License
 * @version 1.0.0
 */

namespace Howl\Core;

class DBField {
    const STRING = "string";
    const DATE = "date";
    const NUMBER = "number";
    public $name;
    public $type;
    public $null = false;
    public $default = null;
    public $primary = false;
    public $extra;
}