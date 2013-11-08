<?php
class EmailValidator extends PHPValidator{
	public $pattern='/^[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+(?:\.[a-zA-Z0-9!#$%&\'*+\\/=?^_`{|}~-]+)*@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?\.)+[a-zA-Z0-9](?:[a-zA-Z0-9-]*[a-zA-Z0-9])?$/';
	protected function validateAttribute($values,$attribute){
		$value = $values[$attribute];
		if(is_string($value) && strlen($value)<=254 && (preg_match($this->pattern,$value))){
            return true;
        }
        else{
            $this->addErrorTips("INVALID EMAIL ADDRESS");
            return false;
        }
	}

}
