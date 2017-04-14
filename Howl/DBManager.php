<?php
/**
 * This class is the Db Manager, it handles all DB Drivers
 * Supported: MySql
 * @author Jorge Alejandro Quiroz Serna (Jako) <alejo.jko@gmail.com>
 * @license MIT License
 * @version 1.0.0
 */

namespace Howl;

use Howl\Core\DBField;

class DBManager implements \Howl\Core\IDBDriver {
    const MYSQL_DRIVER = 'MySql';
    /**
     * The current driver.
     * @var \Howl\Core\DBDriver
     */
    private $driver = null;

    private function __construct(){}

    /**
     * This function loads a DBDriver
     * @param string $driverName
     * @param array $config
     */
    public function loadDriver(string $driverName, array $config){
        $namespace = "\Howl\Drivers\\{$driverName}\\{$driverName}Driver";
        $this->driver = new $namespace($config);
    }

    /**
     * This function returns the oly instance of the DBManager
     * @return DBManager|null
     */
    public static function getInstance(){
        static $instance = null;
        if($instance === null) $instance = new DBManager();
        return $instance;
    }

    /**
     * This function returns a full description of the table.
     * @param string $tableName
     * @return array
     */
    public function describe(string $tableName): array {
        return $this->driver->describe($tableName);
    }

    /**
     * This function allows to set the table to use in the query.
     * @param string $table
     */
    public function table(string $table){
        $this->driver->setTable($table);
    }

    /**
     * This function allows to set the alias for the table in the query.
     * @param string $alias
     */
    public function alias(string $alias){
        $this->driver->setAlias($alias);
    }

    /**
     * This function allows to set the columns to be used for the query.
     * @param array $columns
     */
    public function columns(array $columns){
        $this->driver->setColumns($columns);
    }

    /**
     * This function allows to set the values to be used for the query.
     * @param array $values
     */
    public function values(array $values){
        $this->driver->setValues($values);
    }

    /**
     * This function adds conditions to the query.
     * @param string $field
     * @param string $compare
     * @param string $operator
     * @param string $connector
     */
    public function condition(string $field, string $compare, string $operator = "=", string $connector = "AND") {
        $this->driver->condition($field, $compare, $operator, $connector);
    }

    /**
     * This function adds LIKE conditions to the query.
     * @param string $field
     * @param string $likeCond
     * @param string $connector
     * @param bool $not
     */
    public function likeCond(string $field, string $likeCond, string $connector = "=", bool $not = false) {
        $this->driver->likeCond($field, $likeCond, $connector, $not);
    }

    /**
     * This function adds an IN condition to the query.
     * @param string $field
     * @param array $values
     * @param string $connector
     * @param bool $not
     */
    public function inCond(string $field, array $values, string $connector = "AND", bool $not = false) {
        $this->driver->inCond($field, $values, $connector, $not);
    }

    /**
     * This function adds a BETWEEN condition to the query.
     * @param string $field
     * @param string $from
     * @param string $to
     * @param string $connector
     * @param bool $not
     */
    public function betweenCond(string $field, string $from, string $to, $connector = "AND", bool $not = false) {
        $this->driver->betweenCond($field, $from, $to, $connector, $not);
    }

    /**
     * This function adds a IS NULL condition to the query.
     * @param string $field
     * @param string $connector
     * @param bool $not
     */
    public function isNullCond(string $field, string $connector = "AND", bool $not = false) {
        $this->driver->isNullCond($field, $connector, $not);
    }

    /**
     * This function adds a JOIN to the query.
     * @param string $join
     */
    public function join(string $join) {
        $this->driver->join($join);
    }

    /**
     * This function adds a grouping condition to the query.
     * @param string $group
     */
    public function group(string $group) {
        $this->driver->group($group);
    }

    /**
     * This function adds a HAVING condition to the query.
     * @param string $having
     */
    public function having(string $having) {
        $this->driver->having($having);
    }

    /**
     * This function executes a count query.
     * @param string $expression
     * @return array
     */
    public function count(string $expression): array {
        return $this->driver->count($expression);
    }

    /**
     * This function  builds and return the COUNT expression
     * used by the database engine.
     * @param string $expression
     * @return string
     */
    public function getCount(string $expression): string {
        return $this->driver->getCount($expression);
    }

    /**
     * This function adds ORDER BY clause to the query.
     * @param string $order
     * @param bool $asc
     */
    public function order(string $order, bool $asc) {
        $this->driver->order($order, $asc);
    }

    /**
     * This function adds the LIMIT clause used by the
     * database engine.
     * @param int $limit
     * @param int $offset
     */
    public function limit(int $limit, int $offset) {
        $this->driver->limit($limit, $offset);
    }

    /**
     * This function executes a select query and return
     * the results of the query.
     * @param bool $all
     * @return array
     */
    public function select(bool $all = true): array {
        return $this->driver->select($all);
    }

    /**
     * This function executes an insert query and
     * return the result.
     * @return bool
     */
    public function insert(): bool {
        return $this->driver->insert();
    }

    /**
     * This function executes an update query and return
     * the result.
     * @return bool
     */
    public function update(): bool {
        return $this->driver->update();
    }

    /**
     * This function executes a delete query and return
     * the result.
     * @return bool
     */
    public function delete(): bool {
        return $this->driver->delete();
    }

    /**
     * This function returns the affected rows by the last query.
     * @return int
     */
    public function affectedRows(): int {
        return $this->driver->affectedRows();
    }

    /**
     * This function returns the last inserted id in the database.
     * @return string
     */
    public function insertedId(): string {
        return $this->driver->insertedId();
    }

    /**
     * This function executes a query and return it's results as an array.
     * @param string $query
     * @param bool $all
     * @return array
     */
    public function query(string $query, bool $all = true): array {
        return $this->driver->query($query, $all);
    }
}