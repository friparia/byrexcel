<?php
require_once "./Excel.class.php";
//test import
$rules = array(
    array('电话', 'phone'),
    array('姓名', 'checkname'),
    );
$excel = new BYRExcel('test.xlsx');
$result = $excel->validate($rules);
if(!$result)
{
    $ret = $excel->getJSONData();
}
die($ret);


