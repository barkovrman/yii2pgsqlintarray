<?php

namespace Barkov\Pgsqlint;

use Codeception\Exception\ConfigurationException;
use Yii;
use yii\base\Behavior;
use yii\validators\Validator;
use yii\db\ActiveRecord;

/**
 * Class PgIntegerArrayBehavior add functionality for use PostgreSQL int[] field in ActiveRecord.
 * - convert to PHP array when read data from table;
 * - convert to PostgreSQL array when save data in table;
 * - validate input data from ActiveRecord owner class.
 *
 * Owner class must extends ActiveRecord, and have field contains in configuration.
 *
 * This class use as Yii2 behavior.
 *
 * Example:
 * ```php
 * public function behaviors()
 * {
 *  return
 *  [
 *      'PgIntegerArrayBehavior' => [
 *          'class' => \backend\classes\PgIntegerArrayBehavior::className(),
 *          'field' => 'pgarray', //field name
 *      ]
 *  };
 * }
 *
 *
 *
 * ```
 * @author Barkov Roman <barkov.rman@gmail.com>
 * @version Version 1.0
 */
class PgIntegerArrayBehavior extends Behavior{

    /**
     * @var string the attribute that containt field name in table with type int[]
     */
    public $field = 'pgarray';


    /**
     * Validate field value. It will be array with only integer values.
     *
     * @param $attribute
     * @param $params
     */
    public function intArray($attribute, $params){

        if (!is_array($this->owner->{$attribute})) {
            $this->owner->addError($attribute, Yii::t('app','Must be array of integer.'));
            return;
        }

        foreach ($this->owner->{$attribute} as $val)
            if (!is_integer($val)) {
                $this->owner->addError($attribute, Yii::t('app','Value {0} not integer.', $val));
                return;
            }
    }

    /**
     * @inheritdoc
     *
     * Add rule for int[] field. It will be array with only integer values or empty field
     * @var $owner ActiveRecord
     */
    public function attach($owner)
    {
        parent::attach($owner);

        // Check if owner of ActiveRecord
        if (!is_a($owner,ActiveRecord::className()))
            throw new ConfigurationException(Yii::t('app', 'Object {0} must be ActiveRecord class.', get_class($owner)));

        // Check if owner has configure field
        if (!$owner->hasProperty($this->field))
            throw new ConfigurationException(Yii::t('app', "Object {0} don't have '{$this->field}' field.", get_class($owner)));

        // attach validator for configure field
        $validators = $owner->getValidators();
        $validators->append(Validator::createValidator('intArray', $this, (array) $this->field, ['skipOnEmpty' => true]));
    }

    /**
     * Convert postrgeSQL array (string format  - example: {5,4,79}) to php array
     *
     * @param $stringArray
     * @return array
     */
    function StringToArray($stringArray)
    {
        $ar = explode(',', trim($stringArray, '{}'));
        foreach ($ar as $key=>$val) $ar[$key] = (integer) $val;
        return $ar;
    }

    /**
     * Convert php array to postrgeSQL array (string format - example: {5,4,79}).
     * Return false if input value not array.
     *
     * @param $val
     * @return bool|string
     */
    function ArrayToString($val)
    {
        if (is_array($val)) { return '{'.implode(',',$val).'}';}
        else return false;
    }

    /**
     * Transform field value to PHP array
     *
     * @param $event
     */
    public function transformToPhpArray($event) {
        $this->owner->{$this->field} = $this->StringToArray($this->owner->{$this->field});
    }

    /**
     * Transform field value to postgreSQL array
     *
     * @param $event
     */
    public function transformToPostgreArray($event) {

        $new_val = $this->ArrayToString($this->owner->{$this->field});
        if ($new_val) $this->owner->{$this->field} = $new_val;
    }

    /**
     * @inheritdoc
     *
     * Attach events for change field value.
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_FIND => 'transformToPhpArray',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'transformToPostgreArray',
            ActiveRecord::EVENT_AFTER_UPDATE => 'transformToPhpArray',
        ];
    }
}

