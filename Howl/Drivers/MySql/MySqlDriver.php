<?php
/**
 * This is the specific Driver to the Mysql Engine, is in charge of building the
 * queries using mysql syntax and functions.
 * @author Jorge Alejandro Quiroz Serna (Jako) <alejo.jko@gmail.com>
 * @license MIT License
 * @version 1.0.0
 */

namespace Howl\Drivers\MySql;

use \PDOException;
use Howl\Core\DBDriver;
use Howl\Core\DBField;

class MySqlDriver extends DBDriver {

    public function __construct(array $config) {
        $this->connector = new MySqlConnector($config);
        $this->connector->connect();
    }

    /**
     * This function must be implemented to return a full description of the table.
     * @param string $tableName
     * @return array
     */
    public function describe(string $tableName): array {
        $query = "DESCRIBE {$this->table}";
        $results = $this->query($query);
        $fields = [];
        foreach($results AS $fieldInfo){
            $field = $this->createField($fieldInfo);
            # The position 'fields-info' will help to keep only information about the field
            $fields['fields-info'][$field->name] = $field;
            # The position 'fields' will store the field name and it's value
            $fields['fields'][$field->name] = $field->default;
            # Here we catch the primary of the table.
            if($field->primary) $fields['primary'] = $field->name;
        }
        return $fields;
    }

    /**
     * This function allows to create field objects which contains info about the table field.
     * @param array $fieldInfo
     * @return DBField
     */
    public function createField(array $fieldInfo) : DBField{
        $field = new DBField();
        $field->name = $fieldInfo['Field'];
        $field->type = $fieldInfo['Type'];
        $field->null = $fieldInfo['Null'] == 'YES';
        $field->primary = $fieldInfo['Key'] == 'PRI';
        $field->default = $fieldInfo['Default'];
        $field->extra = $fieldInfo['Extra'];
        return $field;
    }

    /**
     * This function allows to execute a query and return it's results as an array.
     * @param string $query
     * @param bool $many
     * @return array
     */
    public function query(string $query, bool $many = true) : array{
        $this->query = $query;
        $result = [];
        if($this->exeQuery()) $result = $this->connector->fetch($many);
        return $result;
    }

    /**
     * This function allows to execute any query.
     * @return bool
     */
    public function exeQuery(): bool {
        $result = $this->connector->exeQuery($this->query);
        $this->clear();
        return $result;
    }

    /**
     * This function allows to add a condition to the current query.
     * @param string $field
     * @param string $compare
     * @param string $operator
     * @param string $connector
     */
    public function condition(string $field, string $compare, string $operator = "=", string $connector = "AND") {
        $this->conditions .= $this->conditions == ""? " WHERE " : " {$connector} ";
        $this->conditions .= "{$field} {$operator} '{$compare}'";
    }

    /**
     * This function allows to add a Like condition to the current query.
     * @param string $field
     * @param string $likeCond
     * @param string $connector
     * @param bool $not
     */
    public function likeCond(string $field, string $likeCond, string $connector = "=", bool $not = false) {
        $this->conditions .= $this->conditions == ""? " WHERE " : " {$connector} ";
        $this->conditions .= "{$field}" . ($not? " NOT " : " ") . "LIKE '{$likeCond}'";
    }

    /**
     * This function allows to add a in condition to the current query.
     * @param string $field
     * @param array $values
     * @param string $connector
     * @param bool $not
     */
    public function inCond(string $field, array $values, string $connector = "AND", bool $not = false) {
        $this->conditions .= $this->conditions == ""? " WHERE " : " {$connector} ";
        $implodedValues = implode(", ", array_map(function($value){return "'{$value}'";}, $values));
        $this->conditions .= "{$field}" . ($not? " NOT " : " ") . "IN ({$implodedValues})";
    }

    /**
     * This function allows to add a between condition to the current query.
     * @param string $field
     * @param string $from
     * @param string $to
     * @param string $connector
     * @param bool $not
     */
    public function betweenCond(string $field, string $from, string $to, $connector = "AND", bool $not = false) {
        $this->conditions .= $this->conditions == ""? " WHERE " : " {$connector} ";
        $this->conditions .= "{$field}" . ($not? " NOT " : " ") . "BETWEEN '{$from}' AND {$to})";
    }

    /**
     * This function allows to add a is null condition to the current query.
     * @param string $field
     * @param string $connector
     * @param bool $not
     */
    public function isNullCond(string $field, string $connector = "AND", bool $not = false) {
        $this->conditions .= $this->conditions == ""? " WHERE " : " {$connector} ";
        $this->conditions .= "{$field} IS" . ($not? " NOT " : " ") . "NULL";
    }

    /**
     * This function allows to add joins to the current query.
     * @param string $join
     */
    public function join(string $join) {
        $this->joins .= " {$join} ";
    }

    /**
     * This function allows to add grouping condition to the current query.
     * @param string $group
     */
    public function group(string $group) {
        if($this->group == "") $this->group = "GROUP BY {$group} ";
        else $this->group .= ", {$group}";
        $this->isGrouped = true;
    }

    /**
     * This function allows to add a having condition to the current query.
     * @param string $having
     */
    public function having(string $having) { $this->having = " HAVING {$having}"; }

    /**
     * This function executes a count query.
     * @param string $expression
     * @return array
     */
    public function count(string $expression): array {
        $this->query = "SELECT COUNT({$expression}) AS counted FROM {$this->table} {$this->tableAlias}";
        $this->query .= $this->joins;
        $this->query .= $this->conditions;
        $this->query .= $this->group;
        $this->query .= $this->having;
        $result = [];
        try {
            $this->exeQuery();
            $result = $this->connector->fetch(false);
        } catch (PDOException $e){
            throw $e;
        }
        return $result;
    }

    /**
     * This function returns the count expression used by the database engine.
     * used by the database engine.
     * @param string $expression
     * @return string
     */
    public function getCount(string $expression): string { return "COUNT({$expression})"; }

    /**
     * This function allows to add order clause to the current query.
     * @param string $order
     * @param bool $asc
     */
    public function order(string $order, bool $asc) {
        if($this->order == "") $this->order = " ORDER BY {$order}";
        else $this->order .= ", {$order}";
        $this->order .= " " . ($asc? "ASC" : "DESC");
    }

    /**
     * This function allows to limit the records returned by mysql.
     * database engine.
     * @param int $limit
     * @param int $offset
     */
    public function limit(int $limit, int $offset) {
        $this->limit = " LIMIT {$limit} OFFSET {$offset}";
    }

    /**
     * This function allows to execute a select query.
     * @return array
     */
    public function select(bool $all = true): array {
        $cols = $this->buildColumns();
        if($this->isGrouped) $cols .= ", COUNT({$this->tableAlias}.*) as counted";
        $this->query = "SELECT {$cols} FROM $this->table AS $this->tableAlias";
        $this->query .= $this->joins;
        $this->query .= $this->conditions;
        $this->query .= $this->group;
        $this->query .= $this->having;
        $this->query .= $this->order;
        $this->query .= $this->limit;
        $results = [];
        try {
            $this->exeQuery();
            $results = $this->connector->fetch($all);
        } catch (PDOException $e){ throw $e; }
        return $results;
    }

    /**
     * This function allows to execute a insert query.
     * @return bool
     */
    public function insert(): bool {
        $this->query = "INSERT INTO {$this->table} ";
        $this->query .= "(" . $this->buildColumns(false) . ") VALUES (" . $this->buildValues() . ")";
        return $this->exeQuery();
    }

    /**
     * This function allows to execute an update query.
     * @return bool
     */
    public function update(): bool {
        $values = $this->buildValuesUpdate();
        $this->query = "UPDATE {$this->table} SET {$values}";
        $this->query .= $this->conditions;
        return $this->exeQuery();
    }

    /**
     * This function allows to execute a delete query.
     * the result.
     * @return bool
     */
    public function delete(): bool {
        $this->query = "DELETE FROM {$this->table} ";
        $this->query .= $this->conditions;
        return $this->exeQuery();
    }

    /**
     * This function allows to get the affected rows by the current query.
     * @return int
     */
    public function affectedRows(): int { return $this->connector->affectedRows(); }

    /**
     * This function allows to get the last inserted id in the database.
     * @return string
     */
    public function insertedId(): string { return $this->connector->lastInsertId(); }
}