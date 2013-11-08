<?php
class RegularExpressionValidator extends PHPValidator{
    public $pattern;
 	public $not=false;
	protected function validateAttribute($values,$attribute)
	{
		$value=$values[$attribute];
		if($this->pattern===null){
            $this->addErrorTips("EMPTY PATTERN");
            return false;
        }
		if(!preg_match($this->pattern,$value))
		{
            $this->addErrorTips("PATTERN NOT MATCH");
            return false;
		}
        return true;
	}

}
