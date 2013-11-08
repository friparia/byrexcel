<?php
class ICardValidator extends PHPValidator{
    public $areaCode;
    public $birthCode;
    public $idCode;
    public $verifyCode;
    public $weight = array(7,9,10,5,8,4,2,1,6,3,7,9,10,5,8,4,2,1);
    protected function validateAttribute($values, $attribute){
        $value = $values[$attribute];
        if(strlen($value) == 15){
            $this->areaCode = substr($value,0,6);
            $this->birthCode = "19".substr($value,6,6);
            $this->idCode = substr($value,12,3);
            return true;
        }
        elseif(strlen($value) == 18){
            $this->areaCode = substr($value,0,6);
            $this->birthCode = substr($value,6,8);
            $this->idCode = substr($value,14,3);
            $this->verifyCode = substr($value,17,1);
            if($this->verifyCode == 'X' || $this->verifyCode == 'x'){
                $this->verifyCode = 10;
            }
            if($this->verifyICard()){
                return true;
            }
            else{
                $this->addErrorTips("INVALID ICARD");
                return false;
            }
        }
        else{
            $this->addErrorTips("INVALID LENGTH OF ICARD NUMBER");
            return false;
        }
    }

    public function verifyICard(){
        $unverfiedStr = $this->areaCode.$this->birthCode.$this->idCode();
        $sum = 0;
        foreach($unverfiedStr as $k=>$v){
            $sum += (int)$v*$weight[$k];
        }
        if(($sum % 11) == (int)$verifyCode){
            return true;
        }
        else{
            return false;
        }
    }


}
