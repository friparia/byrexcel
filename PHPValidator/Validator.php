<?php
abstract class PHPValidator{

    public $attributes;
    public $on;
    public $except;
    private $_errorAttributes;
    private $_errorTips;
	public static $builtInValidators = array(
        'phone' => 'PhoneValidator',
		'email'=>'EmailValidator',
        'icard' => 'ICardValidator',
		'required'=>'RequiredValidator',
		'match'=>'RegularExpressionValidator',
		'url'=>'UrlValidator',
		'length'=>'StringValidator',
		'in'=>'RangeValidator',
		'numerical'=>'NumberValidator',
		'boolean'=>'BooleanValidator',
		// 'date'=>'CDateValidator',
		// 'compare'=>'CompareValidator',
        );
    abstract protected function validateAttribute($values, $attribute);

    public function validate($values, $attributes=null){
        if(!is_array($values)){
            ;
        }
        else{
            $attributes=$this->attributes;
            foreach($attributes as $attribute){
                if(!isset($values[$attribute]))
                    return;
                $ret = $this->validateAttribute($values,$attribute);
                if($ret !== true){ 
                    $this->_errorAttributes = $attribute;
                }
            }
        }
        if(empty($this->_errorAttributes))
            return true;
        else
            return false;

    }

	public static function createValidator($name,$attributes,$params=array())
	{
		if(is_callable($name))
		{
			$validator=new InlineValidator;
			$validator->attributes=$attributes;
			$validator->function=$name;
		}
		else
		{
			$params['attributes']=$attributes;
			if(isset(self::$builtInValidators[$name]))
				$className=self::$builtInValidators[$name];
			$validator=new $className;
			foreach($params as $name=>$value)
				$validator->$name=$value;
		}

		return $validator;
	}

    public function addErrorTips($errorTips){
        $this->_errorTips = $errorTips;
    }

    public function getErrorAttribute(){
        return $this->_errorAttributes;
    }

    public function getTips(){
        return $this->_errorTips;
    }
}

