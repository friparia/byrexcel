<?php
class RangeValidator extends PHPValidator{
	public $range;
	public $strict=false;
 	public $not=false;

	protected function validateAttribute($values,$attribute)
	{
		$value=$values[$attribute];
		if(!is_array($this->range))
        {
            $this->addErrorTips("RANGE NOT DEFINED");
            return false;
        }
		if(!$this->not && !in_array($value,$this->range,$this->strict))
		{
            $this->addErrorTips("NOT IN THE LIST");
            return false;
		}
		elseif($this->not && in_array($value,$this->range,$this->strict))
		{
            $this->addErrorTips("IN THE LIST");
            return false;
		}
        return true;
	}

}
