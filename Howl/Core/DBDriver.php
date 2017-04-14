<?php
/**
 * This class is the base for any Database Driver
 * @author Jorge Alejandro Quiroz Serna (Jako) <alejo.jko@gmail.com>
 * @license MIT License
 * @version 1.0.0
 */

namespace Howl\Core;

abstract class DBDriver implements IDBDriver {
    /**
     * Current query (A database driver must run one query at once).
     * @var string
     */
    protected $query;
    /**
     * Instance of the Database Connector.
     * @var \Howl\Core\DBConnector
     */
    protected $connector;
    /**
     * Table to be used in the current query.
     * @var string
     */
    protected $table;
    /**
     * Column names to be used in a the current query.
     * @var array
     */
    protected $columnNames = [];
    /**
     * Values to be used in the current query.
     * @var array
     */
    protected $values = [];
    /**
     * Joins to be used in the current query (Only in SELECT ones).
     * @var string
     */
    protected $joins = "";
    /**
     * Conditions to be used in the current query.
     * @var string
     */
    protected $conditions = "";
    /**
     * Group sentences to be used in the current query.
     * @var string
     */
    protected $group = "";
    /**
     * Flag to check if the current query is grouped.
     * @var bool
     */
    protected $isGrouped = false;
    /**
     * Current having condition.
     * @var string
     */
    protected $having = "";
    /**
     * Order to be used in the current query.
     * @var string
     */
    protected $order = "";

    /**
     * Table alias to be used in the current query
     * @var string
     */
    protected $tableAlias = "t";

    /* ================= MYSQL Exclusive ================= */
    /**
     * Limit to the current query.
     * @var int
     */
    protected $limit;
    /**
     * Offset to limit the current query.
     * @var int
     */
    protected $offset;

    public abstract function __construct(array $config);
    /**
     * This function allows to create field objects which contains info about the table field.
     * @param array $fieldInfo
     * @return DBField
     */
    public abstract  function createField(array $fieldInfo) : \Howl\Core\DBField;
    /**
     * This function must be implemented to execute any query.
     * @return bool
     */
    public abstract function exeQuery() : bool;

    /**
     * Sets the table name to be use in the current query.
     * @param string $table
     */
    public function setTable(string $table){ $this->table = $table; }

    /**
     * Sets the columns to be used in the current query.
     * @param array $columns
     */
    public function setColumns(array $columns){ $this->columnNames = $columns; }

    /**
     * Sets the values to be used in the current query.
     * @param array $values
     */
    public function setValues(array $values){ $this->values = $values; }

    /**
     * Set the alias to be used for the table in the current query.
     * @param string $alias
     */
    public function setAlias(string $alias){ $this->tableAlias = $alias; }

    /**
     * Returns the current table's alias
     * @return string
     */
    public function getAlias(){ return $this->tableAlias; }

    /**
     * Helps to build the column names for a SELECT | insert query.
     * @return string
     */
    public function buildColumns() : string{
        $columns = implode(', ', array_map(function($column){
            return "{$column}";
        }, $this->columnNames));
        return $columns;
    }

    /**
     * Helps to build the values for an INSERT query.
     * @return string
     */
    public function buildValues() : string{
        $values = implode(', ', array_map(function($value){
            return "'{$value}'";
        }, $this->values));
        return $values;
    }

    /**
     * Helps to build the values for an UPDATE query.
     * @return string
     */
    public function buildValuesUpdate(){
        $values = implode(', ', array_map(function($column, $value){
            return "{$column} = '$value'";
        }, $this->columnNames, $this->values));
        return $values;
    }

    /**
     * Clear Driver fields to be used in the next query.
     */
    public function clear(){
        $this->table = "";
        $this->query = "";
        $this->joins = "";
        $this->conditions = "";
        $this->group = "";
        $this->having = "";
        $this->order = "";
        $this->limit = null;
        $this->offset = null;
        $this->columnNames = [];
        $this->values = [];
    }
}