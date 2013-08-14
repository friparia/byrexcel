<?php
require_once "BYRExcel.class.php";
$data = $_POST['data'];
session_start();
BYRExcel::change($data);
die(BYRExcel::json_data());
