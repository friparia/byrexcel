<?php
require_once "./Excel.class.php";
//test import
$rules = array(
    array('电话', 'phone'),
    array('姓名', 'checkName'),
    array('布尔值', 'boolean'),
    array('数字', 'numberical'),
    array('字符串', 'length'),
    array('Url', 'url'),
    array('邮箱', 'email'),
    array('范围', 'in', 'range'=>array(100,5)),
    array('身份证', 'icard'),
    array('正则表达式', 'match', 'pattern'=>'/te/')
    );
$excel = new BYRExcel('test.xlsx');
$result = $excel->validate($rules);
if(!$result)
{
    $ret = $excel->getJSONData();
}
die($ret);


