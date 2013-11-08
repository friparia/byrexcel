<?php
class PhoneValidator extends PHPValidator{
    protected function validateAttribute($values,$attribute){
        $value = $values[$attribute];
        $pattern="/^((\(\d{2,3}\))|(\d{3}\-))?13\d{9}$/";
        if(preg_match($pattern, $value))
            return true;
        else{
            $this->addErrorTips("INVALID PHONE");
            return false;
        }
    }
}
