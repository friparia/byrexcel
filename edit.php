<?php
require_once "BYRExcel.class.php";
$id = explode(",",$_POST['id']);
$col = $id[0];
$row = $id[1];
session_start();
$content = $_POST['content'];
BYRExcel::edit($col,$row,$content);
die(BYRExcel::json_data());
