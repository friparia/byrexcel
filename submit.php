<?php
require_once "./Excel.class.php";
$data = $_POST['excel_data'];
$id = $data['id'];
$excel2 = new BYRExcel($id);
$result = $excel2->modify($data['items']);
if(!$result){
    $ret = $excel2->getJSONData();
}
else{
    $ret = json_encode(true);
}
die($ret);

