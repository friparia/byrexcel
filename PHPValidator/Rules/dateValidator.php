<?php
class DateValidator extends PHPValidator{
    public $format = 'MM/dd/yyyy';

	protected function validateAttribute($values,$attribute)
	{
		$value=$values[$attribute];
		$formats=is_string($this->format) ? array($this->format) : $this->format;
		$valid=false;

		foreach($formats as $format)
		{
			$timestamp=CDateTimeParser::parse($value,$format,array('month'=>1,'day'=>1,'hour'=>0,'minute'=>0,'second'=>0));
			if($timestamp!==false)
			{
				$valid=true;
				if($this->timestampAttribute!==null)
					$object->{$this->timestampAttribute}=$timestamp;
				break;
			}
		}

		if(!$valid)
		{
			$message=$this->message!==null?$this->message : Yii::t('yii','The format of {attribute} is invalid.');
			$this->addError($object,$attribute,$message);
		}
	}

}
