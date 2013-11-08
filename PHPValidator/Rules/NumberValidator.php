<?php
class NumberValidator extends PHPValidator{

	public $integerOnly=false;
	public $max;
	public $min;
	public $tooBig;
	public $tooSmall;
	public $integerPattern='/^\s*[+-]?\d+\s*$/';
	public $numberPattern='/^\s*[-+]?[0-9]*\.?[0-9]+([eE][-+]?[0-9]+)?\s*$/';

	protected function validateAttribute($values,$attribute)
	{
		$value=$values[$attribute];
		if($this->integerOnly)
		{
			if(!preg_match($this->integerPattern,"$value"))
			{
                $this->addErrorTips("NOT AN INTEGER");
                return false;
			}
		}
		else
		{
			if(!preg_match($this->numberPattern,"$value"))
			{
                $this->addErrorTips("NOT A NUMBER");
                return false;
			}
		}
		if($this->min!==null && $value<$this->min)
		{
            $this->addErrorTips("TOO SMALL");
            return false;
		}
		if($this->max!==null && $value>$this->max)
		{
            $this->addErrorTips("TOO BIG");
            return false;
		}
        return true;
	}

}
