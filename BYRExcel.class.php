<?php
/***
 ** BYR Excel 
 ** Excel高级导入操作
 ** Version 0.1
 ** Auther frip
 ***/


class BYRExcel{

	private $_contents = NULL;
	private $_headers = array();
	private $_rows = 0;
	private $_cols = 0;
	private $_defaultcid = 0;
	private $_colarr = array();
	private $_leastrows = -1;

	public function __construct($file, $sheetid=0)
	{
		session_start();
		if(!file_exists($file))
		{
			die("FILE NOT EXISTS");
		}
		require_once "./PHPExcel/Classes/PHPExcel.php";
		$excel = PHPExcel_IOFactory::load($file);
		$sheet = $excel->getSheet($sheetid);
		$this->_rows = $sheet->getHighestRow();
		$highestColumnnum = $sheet->getHighestColumn();
		$this->_cols = PHPExcel_Cell::columnIndexFromString($highestColumnnum);
		for($row = 1; $row <= $this->_rows; $row++){
			for($col = 0; $col != $this->_cols; $col++){
				$this->_contents[$row][$col]["val"] = $sheet->getCellByColumnAndRow($col, $row)->getValue();
				$this->_contents[$row][$col]["check"] = false;
			}
		}
		unset($excel);
		unset($sheet);
		$this->_headers = array();
	}

	/***
	 ** 设置列头部以及匹配规则
	 ** 匹配规则为正则表达式
	 ***/
	public function setHeader($headerName, $rule, $cid=-1)
	{
		if($cid == -1 && !in_array($cid, $this->_colarr))
			$cid = $this->_defaultcid++;
		$this->_headers[] = array('NAME' => $headerName, 'RULE' => $rule, 'CID' => $cid);
		array_push($this->_colarr, $cid);
	}

	/***
	 ** 设置最多显示行数
	 ***/

	public function setLeastRows($val)
	{
		$this->_leastrows = 0 + $val;
	}

	/***
	 ** 单元格查错
	 ***/

	public function check($element, $col)
	{
		foreach($this->_headers as $header)
		{
			if($header['CID'] == $col)
				$rule = $header['RULE'];
		}
		if(!isset($rule))
			return true;
		if (preg_match($rule, $element))
			return true;
		return false;
	}

	/***
	 ** 初始化所有数据
	 ***/

	public function init()
	{
		for($row = 1; $row <= $this->_rows; $row++){
			for($col = 0; $col != $this->_cols; $col++){
				$this->_contents[$row][$col]["check"] = $this->check($this->_contents[$row][$col]["val"],$col);
			}
		}
		$data = array('LEAST_ROW'=>$this->_leastrows, 'HEAD' => $this->_headers, 'CONTENT' => $this->_contents, 'ROWS' => $this->_rows);
		$_SESSION['excel'] = $data;
	}


	/***
	 ** 以下数据需要SESSION以及数据已经init
	 ***/

	/***
	 ** 返回js渲染时需要的数据
	 ***/

	public function json_data()
	{
		if($_SESSION['excel']['LEAST_ROW'] != -1){
			$content = array_slice($_SESSION['excel']['CONTENT'],0,$_SESSION['excel']['LEAST_ROW']);
			$ret = json_encode(array('LEAST_ROW'=>$_SESSION['excel']['LEAST_ROW'], 'HEAD' =>$_SESSION['excel']['HEAD'], 'CONTENT' => $content, 'ROWS' => $_SESSION['excel']['ROWS']));
		}
		else
			$ret = json_encode($_SESSION['excel']);
		return $ret;
	}

	/***
	 ** 返回列顺序改变后的数据
	 ***/

	public function change($data)
	{
		$temp = $_SESSION['excel']['CONTENT'];
		$rows = sizeof($temp);
		$cols = sizeof($temp[1]);
		for($row = 1; $row <= $rows; $row++){
			for($col = 0 ; $col < $cols; $col++){
				$_SESSION['excel']['CONTENT'][$row][$col] = $temp[$row][(int)$data[$col]];
				foreach($_SESSION['excel']['HEAD'] as $header)
				{
					if($header['CID'] == $col)
						$rule = $header['RULE'];
				}
				if(!isset($rule))
					$_SESSION['excel']['CONTENT'][$row][$col]["check"] = true;
				else if (preg_match($rule, $_SESSION['excel']['CONTENT'][$row][$col]['val']))
					$_SESSION['excel']['CONTENT'][$row][$col]["check"] = true;
				
				else
					$_SESSION['excel']['CONTENT'][$row][$col]["check"] = false;
			}
		}
	}

	/***
	 ** 返回列单元格元素改变后的数据
	 ***/

	public function edit($col, $row, $content)
	{
		$_SESSION['excel']['CONTENT'][$row][$col]['val'] = $content;
		foreach($_SESSION['excel']['HEAD'] as $header)
		{
			if($header['CID'] == $col)
				$rule = $header['RULE'];
		}
		if(!isset($rule))
			$_SESSION['excel']['CONTENT'][$row][$col]["check"] = true;
		else if (preg_match($rule, $_SESSION['excel']['CONTENT'][$row][$col]['val']))
			$_SESSION['excel']['CONTENT'][$row][$col]["check"] = true;

		else
			$_SESSION['excel']['CONTENT'][$row][$col]["check"] = false;
	}
	
	/***
	 ** 导入时所需要的数据
	 ***/ 
	public function data()
	{
		return $_SESSION['excel'];
	}
}
