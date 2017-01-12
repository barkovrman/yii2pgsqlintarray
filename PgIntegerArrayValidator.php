<?php

namespace barkov\pgsqlint;

use yii;
use yii\validators\Validator;

/**
 * Class PgIntegerArrayValidator provide validation array with integers.
 *
 * Use as usual yii2 validator in rule section.
 *
 * Example:  ['field_name', 'intArray', 'skipOnEmpty' => true]
 *
 * @author Barkov Roman <barkov.rman@gmail.com>
 * @version Version 1.0
 */
class PgIntegerArrayValidator extends Validator
{
    
    /**
     * @inheritdoc
     *
     * Validate field value. It will be array with only integer values.
     */
    public function validateAttribute($model, $attribute)
    {
        if (!is_array($model->$attribute)) {
            $this->addError($model,$attribute, Yii::t('app','Must be array of integer.'));
            return;
        }

        foreach ($model->$attribute as $val)
            if (!is_integer($val)) {
                $this->addError($model,$attribute, Yii::t('app','Value {0} not integer.', $val));
                return;
            }
    }
}