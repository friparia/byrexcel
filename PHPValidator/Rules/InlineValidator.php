<?php
class InlineValidator extends PHPValidator{
    public $function;
    public $params;
	protected function validateAttribute($values,$attribute)
	{
        if(($ret = call_user_func($this->function, $values[$attribute])) === true){
            return true;
        }
        else{
            $this->addErrorTips($ret);
            return false;
        }
	}
}
