# Howl-ORM

## What is?
Howl-ORM (Object Relational Model) was built to make the data layer of your applications easy and fast. Based on the active record pattern ** Howl-ORM ** makes easy:
* **Connect** to database
* **List** and find records.
* **Store** new records.
* **Update** existing records.
* **Delete** existing records.


>Also ** Howl-ORM ** allows you to filter records using clauses such as JOINS and GROUPS, the result can be ORDERED, LIMITED and COUNTED.


## How to install and initialize
1. Download Howl-ORM as a zip and extract the files into your project libraries folder.
1. Require the **HowlAutoload.php** file from the Howl-ORM package.
1. Initialize the Howl Autoload.
1. Instance Howl Database manager.
1. Load the Database driver.

```php
<?php 
# This is your proyect root.

# Requiring Howl.
require_once 'Libs/Howl/HowlAutoload.php';

# Initializing the autoload.
\Howl\HowlAutoload::initAutoload();

# Getting the Manager instance.
$db = \Howl\DBManager::getInstance();

$config = [
    'host' => 'localhost',
    'user' => 'root',
    'database' => 'your database',
    'password' => 'your password',
];

# Loading the desired driver.
$db->loadDriver(\Howl\DBManager::MYSQL_DRIVER, $config);

```


>Done, now you are connected to the database! An optional way to do exactly the same as above is:


```php
<?php 
require_once 'Libs/Howl/HowlAutoload.php';
\Howl\HowlAutoload::initAutoload();
$config = [
    'host' => 'localhost',
    'user' => 'root',
    'database' => 'your database',
    'password' => 'your password',
];
\Howl\DBManager::getInstance()->loadDriver(\Howl\DBManager::MYSQL_DRIVER, $config);
```


> Howl use the singleton patter to keep only one instance of the database connection, using the **getInstance** funciton you can get the unique instance of the Dadatabase manager en execute custom queries if you want.





## Mapping your tables (Models)
To map your database tables is very easy. 
1. Create a file with name of the table into a directory named **models** (I recommend you to use PascalCase to name your models).
1. The file must contains a **namespace** Model.
1. The file must have a class with the same name as the file.
1. The class must extends from Howl\Model.
1. The class must contains a property named **table** and it's value must be the name of the table that it represents in database.

> Model/Items.php
```php
<?php
namespace Model;
use Howl\Model;

class Items extends Model{
  protected $table = "tbl_items";
}

```
> app/index.php

```php
<?php 
# this is how we create a new record using Howl-ORM
$item = new \Model\Items();
$item->name = "This is the name";
$item->description = "Lorem ipsum dolor sit...";
$item->save();
```

### List
```php
$records = \Model\Items::search()->all();
```
### Update
```php
$item = \Model\Items::search()->byPk(1);
$item->name = "Name updated...";
$item->save();
```

### Delete
```php
$item = \Model\Items::search()->byPk(1);
$item->delete();
# or
\Model\Items::search()->byPk(2)->delete();
```

### Compare
```php
$records = \Model\Items::search()
                        ->equals("field1", "item1")
                        ->and()
                        ->equals("field2", 2)
                        ->get();
```
