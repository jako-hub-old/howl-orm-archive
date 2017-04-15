<?php
/**
 * This class is the base of all Howl Models.
 * the built criteria to the DBManager
 * Supported: MySql
 * @author Jorge Alejandro Quiroz Serna (Jako) <alejo.jko@gmail.com>
 * @license MIT License
 * @version 1.0.0
 */

namespace Howl\Core;

use Howl\DBCriteria;
use Howl\DBManager;

abstract class Bean {
    /**
     * If the model represents a new record or an existent one.
     * @var bool
     */
    protected $isNew = true;
    /**
     * Table name represented by the model.
     * @var string
     */
    protected $table;
    /**
     * Primary key name.
     * @var string
     */
    protected $pk;
    /**
     * Primary key value.
     * @var string
     */
    protected $pkValue;
    /**
     * Criteria used by the model.
     * @var DBCriteria
     */
    protected $criteria;
    /**
     * Fields information.
     * @var DBField[];
     */
    protected $propertiesInfo = [];
    /**
     * Attributes of the model.
     * @var array
     */
    protected $attributes = [];
    /**
     * Column names of the table.
     * @var array
     */
    protected $tableColumns = [];
    /**
     * Total of records returned when a grouped query.
     * @var int
     */
    protected $counted = 0;

    public function __construct(array $properties = []) {
        if(!empty($properties)) $this->propertiesInfo = $properties;
        $this->buildProperties();
        $this->init();
    }

    /**
     * Build the properties info and column names of the table.
     */
    protected function buildProperties(){
        if(empty($this->propertiesInfo)){
            $properties = DBManager::getInstance()->describe($this->table);
            $this->propertiesInfo = $properties['fields-info']?? [];
            $this->attributes = $properties['fields']?? [];
            $this->pk = $properties['primary']?? null;
        } else {
            $this->fetchProperties();
        }
        $this->tableColumns = array_keys($this->attributes);
    }

    /**
     * Fetch the properties info if it is a new Model.
     */
    protected function fetchProperties(){
        foreach($this->propertiesInfo as $propName => $prop){
            $this->attributes[$propName] = $prop->default;
            if($prop->primary) $this->pk = $propName;
        }
    }

    /**
     * Initializes properties of the model.
     */
    protected function init(){
        $this->criteria = new DBCriteria();
    }

    public function __set(string $property, $value){
        if($property == "attributes") $this->setAttributes($value);
        else if(array_key_exists($property, $this->attributes)) $this->attributes[$property] = $value;
    }

    public function __get(string $property){
        if(array_key_exists($property, $this->attributes)) return $this->attributes[$property];
        else return null;
    }

    /**
     * Allows to set the model attributes at once.
     * @param array $attributes
     */
    protected function setAttributes(array $attributes){
        foreach($attributes AS $name => $value){
            if(array_key_exists($name, $this->attributes)) $this->attributes[$name] = $value;
        }
    }

    /**
     * Returns the attributes of the model.
     * @return array
     */
    public function getAttributes() : array{
        return $this->attributes;
    }

    /**
     * return the model attributes info.
     * @return array|DBField[]
     */
    public function getPropertiesInfo() : array{
        return $this->propertiesInfo;
    }

    /**
     * This function allows to select all records in table.
     * @return array|Bean[]
     */
    protected function selectAll() : array{
        DBManager::getInstance()->table($this->table);
        DBManager::getInstance()->columns($this->getColumns());
        $results = DBManager::getInstance()->select();
        $invokedClass = get_called_class();
        $objects = [];
        foreach($results AS $attributes){
            $obj = new $invokedClass($this->propertiesInfo);
            if(!$obj instanceof Bean) break;
            $obj->setAttributes($attributes);
            $obj->counted = $attributes['counted']?? 0;
            $obj->isNew = false;
            $objects[] = $obj;
        }
        return $objects;
    }

    /**
     * This function returns the count of rows in the table.
     * @return int
     */
    protected function selectCount() : int{
        DBManager::getInstance()->table($this->table);
        $results = DBManager::getInstance()->count($this->pk);
        return $results['counted']?? 0;
    }

    /**
     * This function returns a lists using key value.
     * @param string|null $key
     * @param string|null $value
     * @return array
     */
    protected function getList(string $key = null, string $value = null) : array{
        $results = $this->selectAll();
        $list = [];
        if(empty($key) || $key == null) $key = $this->pk;
        if(!empty($value) && $value != null){
            foreach($results as $object) $list[$object->{$key}] = $object->{$value};
        } else {
            foreach($results as $object) $list[] = $object->{$key};
        }
        return $list;
    }

    /**
     * This function returns the table columns.
     * @return array
     */
    public function getColumns() : array{
        return $this->tableColumns;
    }

    /**
     * This function returns the current value of the pk attribute.
     * @return mixed
     */
    public function getPkVal(){
        return $this->attributes[$this->pk];
    }

    /**
     * This function returns the first record of the table.
     * @return null|\Howl\Model
     */
    protected function selectOne() : \Howl\Model{
        DBManager::getInstance()->table($this->table);
        DBManager::getInstance()->columns($this->getColumns());
        $results = DBManager::getInstance()->select();
        if(empty($results)) return null;
        $invokedClass = get_called_class();
        $obj = new $invokedClass($this->propertiesInfo);
        if(!$obj instanceof \Howl\Model) return null;
        $obj->setAttributes($results[0]);
        $obj->isNew = false;
        return $obj;
    }

    /**
     * This function allows to perform an insert.
     * @return bool
     */
    protected function insert() : bool{
        $inserted = $this->insertRecord();
        if($inserted){
            $this->attributes[$this->pk] = DBManager::getInstance()->insertedId();
            $this->isNew = false;
        }
        return $inserted;
    }

    /**
     * This function sends the insert instruction to the DBManager.
     * @return bool
     */
    private function insertRecord() : bool{
        $attributes = $this->attributes;
        unset($attributes[$this->pk]);
        $cols = array_keys($attributes);
        DBManager::getInstance()->table($this->table);
        DBManager::getInstance()->columns($cols);
        DBManager::getInstance()->values($attributes);
        return DBManager::getInstance()->insert();
    }

    /**
     * This function sends an update instruction to the DBManager.
     * @return bool
     */
    protected function update() : bool{
        $attributes = $this->attributes;
        unset($attributes[$this->pk]);
        $cols = array_keys($attributes);
        DBManager::getInstance()->table($this->table);
        DBManager::getInstance()->columns($cols);
        DBManager::getInstance()->values($attributes);
        DBManager::getInstance()->condition($this->pk, $this->getPkVal());
        return DBManager::getInstance()->update();
    }

    /**
     * This function sends a delete instruction to the DBManager.
     * @return bool
     */
    protected function deleteRecord() : bool{
        DBManager::getInstance()->table($this->table);
        DBManager::getInstance()->condition($this->pk, $this->getPkVal());
        return DBManager::getInstance()->delete();
    }

    /**
     * This function hides the behaviour of the bean.
     * @return array
     */
    public function __debugInfo() {
        return [
            'isNew' => $this->isNew,
            'attributes' => $this->attributes,
            'counted' => $this->counted
        ];
    }
}