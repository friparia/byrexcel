<?php
class StringValidator extends PHPValidator{
	public $max;
	public $min;
	public $is;
	protected function validateAttribute($values,$attribute)
	{
        $value=$values[$attribute];
        $length=strlen($value);

		if($this->min!==null && $length<$this->min)
		{
            $this->addErrorTips("TOO SHORT!");
            return false;
		}
		if($this->max!==null && $length>$this->max)
		{
            $this->addErrorTips("TOO LONG!");
            return false;
		}
		if($this->is!==null && $length!==$this->is)
		{
            $this->addErrorTips("NOT MATCH!");
            return false;
		}
        return true;
	}


}
