<?php
class RequiredValidator extends PHPValidator{
	protected function validateAttribute($values,$attribute)
	{
		$value=$values[$attribute];
		if(empty($value)){
            $this->addErrorTips("VALUE CANNOT BE BLANK");
            return false;
		}
        else{
            return true;
        }

	}

}
