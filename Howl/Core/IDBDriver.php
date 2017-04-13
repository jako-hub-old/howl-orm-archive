<?php
/**
 * This interface describes the behaviour of any Database driver.
 * @author Jorge Alejandro Quiroz Serna (Jako) <alejo.jko@gmail.com>
 * @license MIT License
 */

namespace Howl\Core;


interface IDBDriver {
    /**
     * This function must be implemented to return a full description of the table.
     * @param string $tableName
     * @return array
     */
    public function describe(string $tableName) : array;

    /**
     * This function must be implemented to add conditions to the query.
     * @param string $field
     * @param string $compare
     * @param string $operator
     * @param string $connector
     */
    public function condition(string $field, string $compare, string $operator = "=", string $connector = "AND");

    /**
     * This function must be implemented to add LIKE conditions to the query.
     * @param string $field
     * @param string $likeCond
     * @param string $connector
     * @param bool $not
     */
    public function likeCond(string $field, string $likeCond, string $connector = "=", bool $not = false);

    /**
     * This function must be implemented to add a IN condition to the query.
     * @param string $field
     * @param array $values
     * @param string $connector
     * @param bool $not
     */
    public function inCond(string $field, array $values, string $connector = "AND", bool $not = false);

    /**
     * This function must be implemented to add a BETWEEN condition to the query.
     * @param string $field
     * @param string $from
     * @param string $to
     * @param string $connector
     * @param bool $not
     */
    public function betweenCond(string $field, string $from, string $to, $connector = "AND", bool $not = false);

    /**
     * This function must be implemented to add a IS NULL condition to the query.
     * @param string $field
     * @param string $connector
     * @param bool $not
     */
    public function isNullCond(string $field, string $connector = "AND", bool $not = false);

    /**
     * This function must be implemented to add a JOIN to the query.
     * @param string $join
     */
    public function join(string $join);

    /**
     * This function must be implemented to add a grouping condition to the query.
     * @param string $group
     */
    public function group(string $group);

    /**
     * This function must be implemented to add a HAVING condition to the query.
     * @param string $having
     */
    public function having(string $having);

    /**
     * This function must be implemented to execute a count query.
     * @param string $expression
     * @return array
     */
    public function count(string $expression) : array;

    /**
     * This function must be implemented to build and return the COUNT expression
     * used by the database engine.
     * @param string $expression
     * @return string
     */
    public function getCount(string $expression) : string;

    /**
     * This function must be implemented to add ORDER BY clause to the query.
     * @param string $order
     * @param bool $asc
     */
    public function order(string $order, bool $asc);

    /**
     * This function must be implemented to add the LIMIT clause used by the
     * database engine.
     * @param int $limit
     * @param int $offset
     */
    public function limit(int $limit, int $offset);

    /**
     * This function must be implemented to execute a select query and return
     * the results of the query.
     * @return array
     */
    public function select() : array;

    /**
     * This function must be implemented to execute an insert query and
     * return the result.
     * @return bool
     */
    public function insert() : bool;

    /**
     * This function must be implemented to execute an update query and return
     * the result.
     * @return bool
     */
    public function update() : bool;

    /**
     * This function must be implemented to execute a delete query and return
     * the result.
     * @return bool
     */
    public function delete() : bool;

    /**
     * This function must be implemented to return the affected rows by the last query.
     * @return int
     */
    public function affectedRows() : int;

    /**
     * This function must be implemented to return the last inserted id in the database.
     * @return string
     */
    public function insertedId() : string;
}