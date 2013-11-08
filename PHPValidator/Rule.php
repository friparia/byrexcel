<?php
class Rule{
    private $_attributes;
    private $_name;
    private $_params;

    public function __construct($rule){
        if($rule instanceof stdclass){
            $rule = get_object_vars($rule);
        }
        $this->_attributes = preg_split('/[\s,]+/',$rule[0],-1,PREG_SPLIT_NO_EMPTY);
        $this->_name = $rule[1];
        $this->_params = array_slice($rule, 2);
    }

    public function getRule(){
        return array(implode(',',$this->_attributes), $this->_name, $this->_params); //yii like
    }

    public function removeAttribute($attribute){
        foreach($this->_attributes as $key => $val){
            if($val == $attribute){
                unset($this->_attributes[$key]);
            }
        }
    }

    public function getAttributes(){
        return $this->_attributes;
    }

    public function getName(){
        return $this->_name;
    }

    public function getParams(){
        return $this->_params[0];
    }
}
