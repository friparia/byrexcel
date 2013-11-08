<?php
class CompareValidator extends PHPValidator{
	public $strict=false;
	public $operator='=';

	protected function validateAttribute($values,$attribute)
	{
        $value=$values[$attribute];
        $compareAttribute=$attribute.'_repeat';
        $compareValue=$object->$compareAttribute;
        $compareTo=$object->getAttributeLabel($compareAttribute);

		switch($this->operator)
		{
			case '=':
			case '==':
				if(($this->strict && $value!==$compareValue) || (!$this->strict && $value!=$compareValue))
					$message=$this->message!==null?$this->message:Yii::t('yii','{attribute} must be repeated exactly.');
				break;
			case '!=':
				if(($this->strict && $value===$compareValue) || (!$this->strict && $value==$compareValue))
					$message=$this->message!==null?$this->message:Yii::t('yii','{attribute} must not be equal to "{compareValue}".');
				break;
			case '>':
				if($value<=$compareValue)
					$message=$this->message!==null?$this->message:Yii::t('yii','{attribute} must be greater than "{compareValue}".');
				break;
			case '>=':
				if($value<$compareValue)
					$message=$this->message!==null?$this->message:Yii::t('yii','{attribute} must be greater than or equal to "{compareValue}".');
				break;
			case '<':
				if($value>=$compareValue)
					$message=$this->message!==null?$this->message:Yii::t('yii','{attribute} must be less than "{compareValue}".');
				break;
			case '<=':
				if($value>$compareValue)
					$message=$this->message!==null?$this->message:Yii::t('yii','{attribute} must be less than or equal to "{compareValue}".');
				break;
			default:
				throw new CException(Yii::t('yii','Invalid operator "{operator}".',array('{operator}'=>$this->operator)));
		}
		if(!empty($message))
			$this->addError($object,$attribute,$message,array('{compareAttribute}'=>$compareTo,'{compareValue}'=>$compareValue));
	}

}
