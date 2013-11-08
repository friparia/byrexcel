<?php
class PhoneValidator extends PHPValidator{
    protected function validateAttribute($values,$attribute){
        $value = $values[$attribute];
        $pattern="/^1[3|4|5|8]\d{9}$/";
        if(preg_match($pattern, (string)$value))
            return true;
        else{
            $this->addErrorTips("INVALID PHONE");
            return false;
        }
    }
}
