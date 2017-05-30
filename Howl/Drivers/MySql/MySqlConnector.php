<?php
/**
 * This is the specific Connector to the Mysql Engine, to establish connection
 * to the MySql db engine.
 * @author Jorge Alejandro Quiroz Serna (Jako) <alejo.jko@gmail.com>
 * @license MIT License
 * @version 1.0.0
 */

namespace Howl\Drivers\MySql;

use \PDO;
use \Exception;
use \Howl\Core\DBConnector;

class MySqlConnector extends DBConnector {
    /**
     * @var \PDOStatement
     */
    private $stm;
    private $rowCount = 0;
    /**
     * This function allows to connect to a mysql server.
     */
    public function connect() {
        try {
            $conString = "mysql:host={$this->_host};dbname={$this->_database}";
            $this->_connection = new PDO($conString, $this->_user, $this->_password);
            $this->_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (Exception $e){
            throw $e;
        }
    }

    /**
     * This function allows to execute any query
     * @param string $query
     * @return mixed
     */
    public function exeQuery(string $query) {
        $this->stm = $this->_connection->prepare($query);
        $result = $this->stm->execute();
        $this->rowCount = $this->stm->rowCount();
        return $result;
    }

    /**
     * This function returns the results from the executed query.
     * @param bool $all
     * @return array
     */
    public  function fetch(bool $all = true): array {
        $type = PDO::FETCH_ASSOC;
        return $all? $this->stm->fetchAll($type) : $this->stm->fetch($type);
    }

    /**
     * This function allows to close the database connection.
     */
    public function disconnect() {
        $this->_connection = null;
    }

    /**
     * This function returns the database error.
     * @return mixed
     */
    public function getError() {
        return $this->_connection->errorInfo();
    }

    /**
     * This function is used to get the last insert id on database.
     * @return string
     */
    public function lastInsertId() : string{
        return $this->_connection->lastInsertId();
    }

    /**
     * This function is used to get the affected rows by the last query.
     * @return int
     */
    public function affectedRows() : int{
        return $this->rowCount;
    }
}