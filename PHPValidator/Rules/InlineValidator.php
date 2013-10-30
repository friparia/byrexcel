<?php
class InlineValidator extends PHPValidator{
    public $function;
    public $params;
	protected function validateAttribute($values,$attribute)
	{
        return call_user_func($this->function, $values[$attribute]);
	}
}
