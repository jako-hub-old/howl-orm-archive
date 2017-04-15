<?php
/**
 * This class allows to build the criteria of the query, it sends
 * the built criteria to the DBManager
 * Supported: MySql
 * @author Jorge Alejandro Quiroz Serna (Jako) <alejo.jko@gmail.com>
 * @license MIT License
 * @version 1.0.0
 */

namespace Howl;

class DBCriteria {
    const EQUALS = "=";
    const NOT_EQUALS = "<>";
    const G_THEN = ">";
    const L_THEN = "<";
    const GE_THEN = ">=";
    const LE_THEN = "<=";
    const OR = "OR";
    const AND = "AND";
    const JOIN = "JOIN";
    const L_JOIN = "LEFT JOIN";
    const R_JOIN = "RIGHT JOIN";
    /**
     * Count of aliases to be used in the joins.
     * @var int
     */
    protected $aliasCount = 1;
    /**
     * Join Clauses to the query.
     * @var string
     */
    protected $join = "";
    /**
     * Join conditions.
     * @var string
     */
    protected $onCondition = "";
    /**
     * Conditions to the query.
     * @var array
     */
    protected $conditions = [];
    /**
     * Grouping conditions to the query.
     * @var
     */
    protected $group;
    /**
     * Having conditions to the query.
     * @var
     */
    protected $having;
    /**
     * The next condition connector.
     * @var string
     */
    protected $nextConcat = "AND";

    /**
     * Allows to add Join conditions.
     * @param string $type
     * @param string $tableName
     * @param string $alias
     */
    public function join(string $type, string $tableName, string $alias = ""){
        $this->join .= ($this->onCondition != ""? " {$this->onCondition} " : "");
        $this->join .= "{$type} {$tableName} " . ($alias != ""? $alias : "t" . $this->aliasCount ++);
        $this->onCondition = "";
    }

    /**
     * Allows to add multiple conditions to the Join.
     * @param string $field
     * @param string $operator
     * @param string $field2
     */
    public function on(string $field, string $operator, string $field2){
        $this->onCondition .= ($this->onCondition == ""? " ON " : " {$this->nextConcat} ");
        $this->onCondition .= "{$field} {$operator} {$field2} ";
    }

    /**
     * Allows to add a condition.
     * @param string $compare
     * @param $to
     * @param string $operator
     */
    public function condition(string $compare, $to, string $operator){
        $this->conditions[] = [$compare, $to, $operator];
    }

    /**
     * Allows to adda a LIKE condition.
     * @param string $field
     * @param string $content
     * @param bool $not
     */
    public function likeCond(string $field, string $content, bool $not = false){
        $this->conditions[] = ['type' => 'like', 'cond' => [$field, $content, $not]];
    }

    /**
     * Allows to add an in condition.
     * @param string $field
     * @param array $values
     * @param bool $not
     */
    public function inCond(string $field, array $values, bool $not = false){
        $this->conditions[] = ['type' => 'in', 'cond' => [$field, $values, $not]];
    }

    /**
     * Allows to add a between condition.
     * @param string $field
     * @param $from
     * @param $to
     * @param bool $not
     */
    public function betweenCond(string $field, $from, $to, bool $not = false){
        $this->conditions[] = ['type' => 'between', 'cond' => [$field, $from, $to, $not]];
    }

    /**
     * Allows to add an IS NULL condition
     * @param string $field
     * @param bool $not
     */
    public function isNullCond(string $field, bool $not = false){
        $this->conditions[] = ['type' => 'null', 'cond' => [$field, $not]];
    }

    /**
     * Allows to set which will be the next concat (connector) operator.
     * @param string $concat
     */
    public function setNextConcat(string $concat){
        $this->nextConcat = $concat;
    }

    /**
     * Allows to add having condition.
     * @param string $field
     */
    public function having(string $field){
        $this->having .= $this->having != ""? " {$this->nextConcat} " : "";
        $this->having .= $field;
    }

    /**
     * Allows to add a having count condition.
     * @param string $field
     */
    public function havingCount(string $field){
        $count = DBManager::getInstance()->getCount($field);
        $this->having($count);
    }

    /**
     * Allows to add conditions to the having count.
     * @param string $directive
     */
    public function is(string $directive){
        $operator = $this->getOperator($directive);
        $this->having .= " {$operator} ";
    }

    /**
     * Allows to compare the having count condition.
     * @param $compare
     */
    public function then($compare){
        $value = is_numeric($compare)? $compare : "'{$compare}'";
        $this->having .= "{$value}";
    }

    /**
     * Allows to get the operator for the having condition
     * @param string $directive
     * @return string
     */
    protected function getOperator(string $directive){
        switch ($directive){
            case 'greater' : return self::G_THEN;
            case 'greater-equals' : return self::GE_THEN;
            case 'less' : return self::L_THEN;
            case 'less-equals' : return self::LE_THEN;
            case 'different' : return self::NOT_EQUALS;
            case 'equals' : return self::EQUALS;
            default : return self::EQUALS;
        }
    }

    /**
     * Allows to establish an order to the query.
     * @param string $field
     * @param bool $asc
     */
    public function order(string $field, bool $asc = true){
        DBManager::getInstance()->order($field, $asc);
    }

    /**
     * Allows to establish a group condition to the query.
     * @param string $field
     */
    public function group(string $field){
        DBManager::getInstance()->group($field);
    }

    /**
     * Allows to establish a limit to the query.
     * @param int $limit
     * @param int $offset
     */
    public function limit(int $limit = 1, int $offset = 0){
        DBManager::getInstance()->limit($limit, $offset);
    }

    /**
     * Dispatch all the conditions and sentences to the DBManager.
     */
    public function dispatch(){
        $this->dispatchConditions();
        $this->dispatchHaving();
        $this->dispatchJoins();
    }

    /**
     * Dispatch only the conditions to the DBManager.
     */
    private function dispatchConditions(){
        foreach($this->conditions AS $condition){
            $this->evalCond($condition);
        }
    }

    /**
     * Eval each condition and sends it to the DBManager.
     * @param array $info
     */
    private function evalCond(array $info){
        $type = $info['type']?? false;
        $cond = $info['cond']?? $info;
        if($type == 'like'){
            DBManager::getInstance()->likeCond($cond[0], $cond[1], $this->nextConcat, $cond[2]);
        } else if($type == 'in'){
            DBManager::getInstance()->inCond($cond[0], $cond[1], $this->nextConcat, $cond[2]);
        } else if($type == 'between'){
            DBManager::getInstance()->betweenCond($cond[0], $cond[1], $cond[2], $this->nextConcat, $cond[3]);
        } else if($type == 'null'){
            DBManager::getInstance()->isNullCond($cond[0], $this->nextConcat, $cond[1]);
        } else {
            DBManager::getInstance()->condition($cond[0], $cond[1], $cond[2], $this->nextConcat);
        }
    }

    /**
     * Dispatch the having conditions to the DBManager.
     */
    private function dispatchHaving(){
        if($this->having == "") return;
        DBManager::getInstance()->having($this->having);
    }

    /**
     * Dispatch the joins to the DBManager.
     */
    private function dispatchJoins(){
        $join = $this->join . $this->onCondition;
        DBManager::getInstance()->join($join);
    }
}