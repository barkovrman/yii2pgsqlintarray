Enable work with PostgreSQL array of integer, in yii2 ActiveRecord.

This extension contains validator and behavior.

Installation
-----------

The preferred way to install this extension is through composer.

```
php composer.phar require --prefer-dist barkov/pgsqlint "dev-master"
```

or add

```
"barkov/pgsqlint": "dev-master"
```
to the require section of your composer.json file.



Usage
-----

**Validator**

use barkov\pgsqlint\PgIntegerArrayValidator;
...

```php
    public function rules(){
        return [['field_name', 'intArray', 'skipOnEmpty' => true]];
    }   
 
```

**Behavior**

In your ActiveRecord model.

```php
 public function behaviors(){
        return
        [
            'PgIntegerArrayBehavior' => [
                'class' =>  \barkov\pgsqlint\PgIntegerArrayBehavior::className(),
                'field' => 'field_name',
            ]
         ]
    } 
```

_Note: If use as behavior - validator included. You don't need adding validation._


