<?php
/**
 * This class is the base for any Database Connector
 * @author Jorge Alejandro Quiroz Serna (Jako) <alejo.jko@gmail.com>
 * @license MIT License
 */

namespace Howl\Core;

abstract class DBConnector {
    /**
     * Database server name.
     * @var string
     */
    protected $_host;
    /**
     * Database user name.
     * @var string
     */
    protected $_user;
    /**
     * Database password.
     * @var string
     */
    protected $_password;
    /**
     * Database name
     * @var string
     */
    protected $_database;
    /**
     * Database port
     * @var string
     */
    protected $_port;
    /**
     * Connection instance
     * @var \PDO
     */
    protected $_connection;

    public function __construct(array $configs){
        $this->_host = $configs['host']?? null;
        $this->_user = $configs['user']?? null;
        $this->_password = $configs['password']?? null;
        $this->_database = $configs['database']?? null;
        $this->_port = $configs['port']?? null;
    }

    /**
     * This function must be implemented to connect to database.
     * @return mixed
     */
    public abstract function connect();

    /**
     * This function must be implemented to execute any query.
     * @param string $query
     * @return mixed
     */
    public abstract function exeQuery(string $query);

    /**
     * This function must be implemented to close connection to database.
     * @return mixed
     */
    public abstract function disconnect();

    /**
     * This function must be implemented to return database errors.
     * @return mixed
     */
    public abstract function getError();

    /**
     * This function hides information about connector components.
     * @return array
     */
    public function __debugInfo() {
        return [
            'Component type' => 'Howl-ORM Database Connector'
        ];
    }
}