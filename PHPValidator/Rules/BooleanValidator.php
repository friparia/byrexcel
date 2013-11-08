<?php
class BooleanValidator extends PHPValidator{
	public $trueValue='1';
	public $falseValue='0';
	public $strict=false;
	protected function validateAttribute($values,$attribute)
	{
		$value=$values[$attribute];
		if(!$this->strict && $value!=$this->trueValue && $value!=$this->falseValue
			|| $this->strict && $value!==$this->trueValue && $value!==$this->falseValue)
		{
            $this->addErrorTips("NOT A BOOLEAN");
            return false;
		}
        return true;
	}

}
