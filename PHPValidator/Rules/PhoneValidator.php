<?php
class PhoneValidator extends PHPValidator{
    protected function validateAttribute($values,$attribute){
        $value = $values[$attribute];
        if($value == '18810541532')
            return true;
        else
            return 'PHONE MUST 18810541532';
    }
}
