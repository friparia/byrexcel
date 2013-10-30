<?php
require_once "./PHPExcel/Classes/PHPExcel.php";
require_once "./PHPValidator/Validator.php";
require_once "./PHPValidator/Rule.php";
require_once "./PHPValidator/Rules/PhoneValidator.php";
require_once "./PHPValidator/Rules/InlineValidator.php";
/***
 * BYR EXCEL 
 * EXCEL 导入导出
 * Version 0.2
 * Auther frip
 * Email friparia@gmail.com
 * TODO Add online mode to modify the current excel
 ***/

class BYRExcel{

    /*** 
     * _errors shows where the excel error is and
     * what it is
     * Example :
     * array(
         *    array('row' => 1, 'col' => 3, 'error' => 'data type wrong'),
         *    array('row' => 2, 'col' => 1, 'error' => 'invalid phone'),
         * );
     ***/
    private $_error = array();

    /***
     * the array stores the whole data
     * in the excel
     * row priori
     ***/
    private $_content = array();

    /***
     * the header of the excel
     ***/
    private $_header = array();

    private $_rule;

    private $_id;
    /**
     * error status whether with head
     ***/
    public $withHeader = false;

    public static $excelType = array('xlsx', 'xls', 'csv');

    const EXCEL_TMP_DIR = "./temp/";

        

    /***
     * Read the exact sheet of an excel file
     * Convert it into an array
     * TODO Update the speed of the reading excel 
     ***/
    public function __construct($file, $sheetid=0){
        if(file_exists($file)){
            $path_parts = pathinfo($file);
        }
        elseif(file_exists(self::EXCEL_TMP_DIR.$file)){
            $path_parts = pathinfo(self::EXCEL_TMP_DIR.$file);
        }
        else{
            die("FILE NOT EXISTS");
        }

        if(!isset($path_parts['extension']))
        {
            $path_parts['extension'] = '';
        }
        if(in_array($path_parts['extension'], self::$excelType)){
            $excel = PHPExcel_IOFactory::load($file);
            $sheet = $excel->getSheet($sheetid);
            $rows = $sheet->getHighestRow();
            $highestColumnnum = $sheet->getHighestColumn();
            $cols = PHPExcel_Cell::columnIndexFromString($highestColumnnum);
            for($col = 0; $col != $cols; $col++){
                $this->_header[] = $sheet->getCellByColumnAndRow($col, 1)->getValue();
                for($row = 2; $row <= $rows; $row++){
                    $this->_content[$row-1][$col] = $sheet->getCellByColumnAndRow($col, $row)->getValue();
                }
            }
        }
        else{
            $jsonData = file_get_contents(self::EXCEL_TMP_DIR.$file);
            $data = (array)json_decode($jsonData);
            $this->_content = get_object_vars($data['CONTENT']);
            $this->_header = $data['HEADER'];
            $this->_rule = $data['RULES'];
            $this->_id = $file;
        }
    }

    /***
     *  use  yii like rule array
     *  and validate the rule with data
     *  TODO Make rule more general (create a class like RuleParser or Rule and pass it to Validator)
     ***/
    public function validate($rules){
        if(!$this->validateHeader()){
            return false;
        }
        $rules_norepeat = $this->validateColumn($rules);
        foreach($this->_content as $row){
            $row = array_combine($this->_header, $row);
            foreach($rules_norepeat as $rule){
                $rule = new Rule($rule);
                $validator = PHPValidator::createValidator($rule->getName(), $rule->getAttributes(), $rule->getParams());
                $res = $validator->validate($row);
                if($res != true){
                    $attribute = $validator->getErrorAttribute();
                    $rowIndex = $this->getRowIndex(array_values($row));
                    if($this->withHeader)
                        $rowNum = $rowIndex+1;
                    else
                        $rowNum = $rowIndex;
                    $colIndex = $this->getColIndex($attribute);
                    $this->_error[] = array('type'=>'element', 'row' => $rowNum, 'col' => $colIndex, 'value' => $this->_content[$rowIndex][$colIndex], 'tips' => $validator->getTips());
                }
            }
        }
        if(empty($this->_error)){
            return $this->_content;
        }
        else
        {
            $this->_id = md5(time());//TODO need to be more yooo
            $data = array();
            $data['CONTENT'] = $this->_content;
            $data['HEADER'] = $this->_header;
            $data['RULES'] = $rules;
            $data['ID'] = $this->_id;
            $data = json_encode($data);
            $filename = $this->_id;
            if(!file_exists(self::EXCEL_TMP_DIR.$filename)){
                file_put_contents(self::EXCEL_TMP_DIR.$filename, $data);
            }
            return false;
        }
    }

    public function validateHeader(){
        $length = count($this->_header);
        for($i = 0 ;  $i != $length - 1; $i++){
            if($this->_header[$i] == $this->_header[$i+1]){
                $this->_error[] = array('type' => 'special', 'value' => 'COLUMN '.$this->_header[$i].' AND COLUMN '.$this->_header[$i+1].' DUMPLICATED');
                return false;
            }
        }
        return true;
    }
    public function validateColumn($rules){
        $retrules = array();
        foreach($rules as $rule){
            $rule = new Rule($rule);
            foreach($rule->getAttributes() as $head){
                if(!in_array($head, $this->_header))
                {
                    $rule->removeAttribute($head);
                    $this->_error[] = array('type' => 'column', 'value' => 'column '.$head.' not found');
                }
            }
            $retrules[] = $rule->getRule();
        }
        return $retrules;
    }

    /***
     * read the js return to modify the content
     ***/
    public function modify($items){
        foreach($items as $i){
            $this->_content[$i['row']][$i['col']] = $i['value'];
        }
        $this->_error = array();
        $data = array();
        $data['CONTENT'] = $this->_content;
        $data['HEADER'] = $this->_header;
        $data['RULES'] = $this->_rule;
        $data['ID'] = $this->_id;
        $data = json_encode($data);
        $filename = $this->_id;
        file_put_contents(self::EXCEL_TMP_DIR.$filename, $data);
        return $this->validate($this->_rule);
    }

    /***
     * return a json data for client use
     ***/
    public function getJSONData(){
        $data = array();
        $data['CONTENT'] = $this->_content;
        $data['HEADER'] = $this->_header;
        $data['ERROR'] = $this->_error;
        $data['ID'] = $this->_id;
        return json_encode($data);
    }

    /***
     * return the errors for backend use
     ***/
    public function getError(){
        return $this->_error;
    }

    private function getRowIndex($row){
        foreach($this->_content as $key=>$value)
        {
            if($row == $value)
            {
                return $key;
            }
        }
        return false;
    }

    private function getColIndex($attribute){
        return array_search($attribute,$this->_header);
    }


}

function checkName($name)
{
    if($name == 'bob'){
        return true;
    }
    else
    return "NAME SHOULD BE bob";
}
