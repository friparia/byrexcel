<?php
class BYRExcel{

    private $_error = array();
    private $_content = array();
    private $_header = array();
    private $_rule;
    private $_id;
    private $_loaded = false;
    public $withHeader = false;

    public static $excelType = array('xlsx', 'xls', 'csv');

    const EXCEL_TMP_DIR = "./temp/";

    public function __construct($file, $sheetid=0){
        if(!$this->_loaded){
            include "./PHPExcel/Classes/PHPExcel.php";
            include "./PHPValidator/Validator.php";
            include "./PHPValidator/Rule.php";
            $this->_loaded = true;
        }
        
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
            $this->_content = get_object_vars($data['content']);
            $this->_header = $data['header'];
            $this->_rule = $data['rules'];
            $this->_id = $file;
        }
    }

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
            $data['content'] = $this->_content;
            $data['header'] = $this->_header;
            $data['rules'] = $rules;
            $data['id'] = $this->_id;
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

    public function modify($items){
        foreach($items as $i){
            $this->_content[$i['row']][$i['col']] = $i['value'];
        }
        $this->_error = array();
        $data = array();
        $data['content'] = $this->_content;
        $data['header'] = $this->_header;
        $data['rules'] = $this->_rule;
        $data['id'] = $this->_id;
        $data = json_encode($data);
        $filename = $this->_id;
        file_put_contents(self::EXCEL_TMP_DIR.$filename, $data);
        return $this->validate($this->_rule);
    }

    public function getJSONData(){
        $data = array();
        $data['content'] = $this->_content;
        $data['header'] = $this->_header;
        $data['error'] = $this->_error;
        $data['id'] = $this->_id;
        return json_encode($data);
    }

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

function checkName($values, $attribute)
{
    $name = $values[$attribute];
    if($name == 'bob'){
        return true;
    }
    else
    return "NAME SHOULD BE bob";
}
