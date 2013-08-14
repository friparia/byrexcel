<?php
require_once "BYRExcel.class.php";

$byrexcel = new BYRExcel("test.xlsx"); 
$byrexcel->setHeader("姓名","/f/");
$byrexcel->setHeader("性别","/\d/");
$byrexcel->setHeader("NUM","/\d/");
//$byrexcel->setLeastRows(2);
$byrexcel->init();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
		<link rel="stylesheet" type="text/css" href="./css/bootstrap.min.css" />
		<link rel="stylesheet" type="text/css" href="./css/common.css" />
		<link rel="stylesheet" type="text/css" href="./css/jquery-ui.css" />
		<link rel="stylesheet" type="text/css" href="./css/dragtable-default.css" />
		<title>北邮人EXCEL DEMO</title>
		<script type="text/javascript" src="./js/jquery-1.8.2.min.js"></script>
		<script type="text/javascript" src="./js/jquery-ui.js"></script>
		<script type="text/javascript" src="./js/bootstrap.min.js"></script>
		<script type="text/javascript" src="./js/jquery.dragtable.js"></script>
		<script type="text/javascript" src="./js/byrexcel.js"></script>
	</head>
	<style>
	.error{
		background-color: #f2dede;
	}
	</style>
	<body>
		<div class="alert alert-info">
			<h4>如果和第一行不对应，可以手动拖动表头使其对应</h4>
		</div>
		<div id="excel-head"></div>
		<div id="excel-content"></div>
		<button id="submitButton" class="btn">导入</button>
	</body>
</html>
