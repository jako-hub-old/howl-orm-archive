<?php
/**
 * This class is the base of all Howl Models.
 * the built criteria to the DBManager
 * Supported: MySql
 * @author Jorge Alejandro Quiroz Serna (Jako) <alejo.jko@gmail.com>
 * @license MIT License
 * @version 1.0.0
 */

namespace Howl;

class Model extends \Howl\Core\Bean {

    /**
     * Sets the columns to the query.
     * @param \string[] ...$columns
     * @return Model
     */
    public function columns (string ...$columns) : Model {
        $this->tableColumns = $columns;
        return $this;
    }

    /**
     * Defines a JOIN.
     * @param string $tableName
     * @param string $alias
     * @return Model
     */
    public function join(string $tableName, string $alias = "") : Model{
        $this->criteria->join(DBCriteria::JOIN, $tableName, $alias);
        return $this;
    }

    /**
     * Defines a LEFT JOIN
     * @param string $tableName
     * @param string $alias
     * @return Model
     */
    public function leftJoin(string $tableName, string $alias = "") : Model{
        $this->criteria->join(DBCriteria::L_JOIN, $tableName, $alias);
        return $this;
    }

    /**
     * Defines a RIGHT JOIN
     * @param string $tableName
     * @param string $alias
     * @return Model
     */
    public function rightJoin(string $tableName, string $alias = "") : Model{
        $this->criteria->join(DBCriteria::R_JOIN, $tableName, $alias);
        return $this;
    }

    /**
     * Defines the conditions to the JOIN
     * @param string $field1
     * @param string $field2
     * @return Model
     */
    public function onEquals(string $field1, string $field2) : Model{
        $this->criteria->on($field1, DBCriteria::EQUALS, $field2);
        return $this;
    }

    /**********************************************************************************/
    /*                                  SELECTION CRITERIA                            */
    /**********************************************************************************/
    /**
     * Adds an equals condition.
     * @param string $field
     * @param $compare
     * @return Model
     */
    public function equals(string $field, $compare) : Model{
        $this->criteria->condition($field, $compare, DBCriteria::EQUALS);
        return $this;
    }

    /**
     * Adds a not equals condition.
     * @param string $field
     * @param $compare
     * @return Model
     */
    public function notEquals(string $field, $compare) : Model{
        $this->criteria->condition($field, $compare, DBCriteria::NOT_EQUALS);
        return $this;
    }

    /**
     * Adds a greater or greater equals condition.
     * @param string $field
     * @param $then
     * @param bool $equals
     * @return Model
     */
    public function greater(string $field, $then, bool $equals = false) : Model{
        $operator = $equals? DBCriteria::GE_THEN : DBCriteria::G_THEN;
        $this->criteria->condition($field, $then, $operator);
        return $this;
    }

    /**
     * Adds a less or less equals condition.
     * @param string $field
     * @param $then
     * @param bool $equals
     * @return Model
     */
    public function less(string $field, $then, bool $equals = false) : Model{
        $operator = $equals? DBCriteria::LE_THEN : DBCriteria::L_THEN;
        $this->criteria->condition($field, $then, $operator);
        return $this;
    }

    /**
     * Adds a like condition where the string should starts with the content.
     * @param string $field
     * @param string $content
     * @return Model
     */
    public function startsWith(string $field, string $content) : Model{
        $this->criteria->likeCond($field, "{$content}%");
        return $this;
    }

    /**
     * Adds a like condition where the string should ends with the content.
     * @param string $field
     * @param string $content
     * @return Model
     */
    public function endsWith(string $field, string $content) : Model{
        $this->criteria->likeCond($field, "%{$content}");
        return $this;
    }

    /**
     * Adds a like condition where the string should contains the content.
     * @param string $field
     * @param string $content
     * @return Model
     */
    public function contains(string $field, string $content) : Model{
        $this->criteria->likeCond($field, "%{$content}%");
        return $this;
    }

    /**
     * Adds a like condition where the string should not contains the content.
     * @param string $field
     * @param string $content
     * @return Model
     */
    public function notContains(string $field, string $content) : Model{
        $this->criteria->likeCond($field, "%{$content}%", true);
        return $this;
    }

    /**
     * Adds an in condition.
     * @param string $field
     * @param array $values
     * @return Model
     */
    public function in(string $field, array $values = []) : Model {
        $this->criteria->inCond($field,  $values);
        return $this;
    }

    /**
     * Adds a not in condition.
     * @param string $field
     * @param array $values
     * @return Model
     */
    public function notIn(string $field, array $values = []) : Model {
        $this->criteria->inCond($field,  $values, true);
        return $this;
    }

    /**
     * Adds a between condition.
     * @param string $field
     * @param $from
     * @param $to
     * @return Model
     */
    public function between(string $field, $from, $to) : Model{
        $this->criteria->betweenCond($field, $from, $to);
        return $this;
    }
    /**
     * Adds a not between condition.
     * @param string $field
     * @param $from
     * @param $to
     * @return Model
     */
    public function notBetween(string $field, $from, $to) : Model{
        $this->criteria->betweenCond($field, $from, $to, true);
        return $this;
    }

    /**
     * Adds an is null condition.
     * @param string $field
     * @return Model
     */
    public function empty(string $field) : Model{
        $this->criteria->isNullCond($field);
        return $this;
    }

    /**
     * Adds an is null condition.
     * @param string $field
     * @return Model
     */
    public function notEmpty(string $field) : Model{
        $this->criteria->isNullCond($field, true);
        return $this;
    }

    /**
     * Adds order clause.
     * @param string $field
     * @param bool $asc
     * @return Model
     */
    public function order(string $field, bool $asc = true) : Model{
        $this->criteria->order($field, $asc);
        return $this;
    }

    /**
     * Adds group clause.
     * @param string $field
     * @return Model
     */
    public function group(string $field) : Model{
        $this->criteria->group($field);
        return $this;
    }

    /**
     * Establish a having condition.
     * @param string $field
     * @return Model
     */
    public function havingCount(string $field) : Model{
        $this->criteria->havingCount($field);
        return $this;
    }

    /**
     * First part of having condition.
     * @param string $directive
     * @return Model
     */
    public function is(string $directive) : Model{
        $this->criteria->is($directive);
        return $this;
    }

    /**
     * Last part of a having condition.
     * @param $compare
     * @return Model
     */
    public function then($compare) : Model{
        $this->criteria->then($compare);
        return $this;
    }

    /**
     * Alias of then.
     * @param $compare
     * @return Model
     */
    public function to($compare) : Model{
        return $this->then($compare);
    }

    /**
     * Adds limit clause.
     * @param int $limit
     * @param int $offset
     * @return Model
     */
    public function limit(int $limit = 1, int $offset = 0) : Model{
        $this->criteria->limit($limit, $offset);
        return $this;
    }

    /**
     * Adds the OR operator.
     * @return Model
     */
    public function or() : Model{
        $this->criteria->setNextConcat(DBCriteria::OR);
        return $this;
    }

    /**
     * Adds the AND operator.
     * @return Model
     */
    public function and() : Model{
        $this->criteria->setNextConcat(DBCriteria::AND);
        return $this;
    }

    /**
     * Returns all records.
     * @return array|Model
     */
    public function all() : array {
        return $this->selectAll();
    }

    /**
     * Returns all records filtered by the conditions established.
     * @return array|Model[]
     */
    public function get() : array {
        $this->criteria->dispatch();
        return $this->selectAll();
    }

    /**
     * Returns all the records as an key:value array.
     * @param string|null $key
     * @param string|null $value
     * @return array|Model[]
     */
    public function list(string $key = null, string $value = null) : array {
        $this->criteria->dispatch();
        return $this->getList($key, $value);
    }

    /**
     * Returns the count of all records.
     * @return int
     */
    public function getCount() : int{
        $this->criteria->dispatch();
        return $this->selectCount();
    }

    /**
     * Returns the first record.
     * @return Model
     */
    public function first() : Model{
        $this->criteria->limit();
        $this->criteria->dispatch();
        return $this->selectOne();
    }

    /**
     * Returns the record that corresponds to the primary key passed as an argument.
     * @param string $pk
     * @return Model|null
     */
    public function byPk(string $pk) : Model{
        $this->criteria->limit();
        $this->criteria->condition($this->pk, $pk, DBCriteria::EQUALS);
        $this->criteria->dispatch();
        return $this->selectOne();
    }

    /**
     * Alias of getCount.
     * @return int
     */
    public function total() : int{
        return $this->getCount();
    }

    /**
     * Saves the current Model, if it is new, its inserted to the database, if not, it will be updated.
     * @return bool
     */
    public function save() : bool{
        if($this->isNew)
            return $this->insert();
        else
            return $this->update();
    }

    /**
     * Deletes a Model.
     * @return bool
     */
    public function delete(){
        return $this->deleteRecord();
    }

    /**
     * This function allows to invoke the functions of the model from static context.
     * @return Model
     */
    public static function search() : Model{
        $className = get_called_class();
        return new $className();
    }
}