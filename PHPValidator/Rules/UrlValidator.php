<?php
class UrlValidator extends PHPValidator{
	public $pattern='/^{schemes}:\/\/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)/i';
	public $defaultScheme;
	public $validSchemes=array('http','https');
	protected function validateAttribute($values,$attribute)
	{
		$value=$values[$attribute];
		if(is_string($value) && strlen($value)<2000){
			if(strpos($value,'://')===false)
				$value=$this->defaultScheme.'://'.$value;

			if(strpos($this->pattern,'{schemes}')!==false)
				$pattern=str_replace('{schemes}','('.implode('|',$this->validSchemes).')',$this->pattern);
			else
				$pattern=$this->pattern;

			if(preg_match($pattern,$value)){
                return true;
            }
		}
        $this->addErrorTips("INVALID URL");
		return false;
	}

}
