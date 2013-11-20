<?php
class PhoneValidator extends PHPValidator{
    public $pattern = "/^1[3|4|5|8]\d{9}$/";
    protected function validateAttribute($values,$attribute){
        $value = $values[$attribute];
        if($this->validateValue($value)){
            return true;
        }
        else{
            $this->addErrorTips("INVALID PHONE");
            return false;
        }
    }

    public function validateValue($value){
        if(preg_match($this->pattern, $value)){
            return true;
        }
        else{
            return false;
        }
    }
}
