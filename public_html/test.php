<?php
include_once('./_common.php');

//	$array = array(
//		array(1, 2, 3, 'a'),
//		array(4, 5, 6, 'b')
//	);

//	$array = array(
//		array(1, 2, 3, 'a'),
//		array(4, 5, 6, 'b')
//	);

//	print_r2($array);

	function L_holiday($api_key, $yyyy, $mm="", $numOfRows="100", $pageNo="1") {	// https://www.data.go.kr/index.do 휴일확인.
		$ch = curl_init();
//		$url = 'http://apis.data.go.kr/B090041/openapi/service/SpcdeInfoService/getHoliDeInfo';	// 국경일
//		$url = 'http://apis.data.go.kr/B090041/openapi/service/SpcdeInfoService/getAnniversaryInfo';	//	국가 기념일
//		$url = 'http://apis.data.go.kr/B090041/openapi/service/SpcdeInfoService/getRestDeInfo';	//	공휴일
//		$url = 'http://apis.data.go.kr/B090041/openapi/service/SpcdeInfoService/get24DivisionsInfo';	//	24절기
//		$url = 'http://apis.data.go.kr/B090041/openapi/service/SpcdeInfoService/getSundryDayInfo';	//	잡절
		$url = 'http://apis.data.go.kr/B090041/openapi/service/SpcdeInfoService/'.$api_key;

		$queryParams = '?' . urlencode('serviceKey') . '=WeisyjaAFrTRPsIwbrX79%2BXpU2dnSLOgZ779T%2FYrkxeN3Kl8%2Fd6PI%2BNlJjbfLL2e4Wip81Zn8xbwHjQom38W8w%3D%3D';
		$queryParams .= '&' . urlencode('_type') . '=' . urlencode('json');
		$queryParams .= '&' . urlencode('pageNo') . '=' . urlencode($pageNo);
		$queryParams .= '&' . urlencode('numOfRows') . '=' . urlencode($numOfRows);
		$queryParams .= '&' . urlencode('solYear') . '=' . urlencode($yyyy);
		if($mm) {
			$queryParams .= '&' . urlencode('solMonth') . '=' . urlencode($mm);
		}

		curl_setopt($ch, CURLOPT_URL, $url . $queryParams);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
		$response = curl_exec($ch);
		$json = json_decode($response);
		curl_close($ch);

//		print_r2($json);

		for ($i=0;$i<$json->response->body->totalCount;$i++) {
				$holi[$json->response->body->items->item[$i]->locdate] = $json->response->body->items->item[$i]->dateName;
		}
		$holi[holi_ct] = $json->response->body->totalCount;
		return $holi;
	}


	static $dtod = array();
	$dtod[] = L_holiday("getAnniversaryInfo", "2023");	// 국가 기념일
	$dtod[] = L_holiday("getRestDeInfo", "2023");			// 공휴일
	$dtod[] = L_holiday("getHoliDeInfo", "2023");			// 국경일
	$dtod[] = L_holiday("get24DivisionsInfo", "2023");	// 24절기
	$dtod[] = L_holiday("getSundryDayInfo", "2023");		// 잡절

	print_r2($dtod);

//	print_r2(L_holiday("2023"));
//	$holi = L_holiday("2023");
//	echo "test1 : ".$holi[date("Ymd", strtotime("2023-03-01"))].br;
//	echo "test2 : ".$holi["20231225"].br;
//	echo "test3 : ".$holi[date("Ymd")].br;
//	echo "test4 : ".date('w', strtotime(date('Ymd')));
?>

<style>
	#container { font-size: 1.3em; }
	table { width:100%; border-collapse:collapse; border-spacing:0 5px; background:#fff;}
	td{ border:1px solid #ececec; }

	.border_n { border:0; }
	.wd150 { width:150px; }
</style>
<?

	function L_sm ($type="1", $bo_table="") {
		global $g5;
		$date = date("Y-m", strtotime(G5_TIME_YMD));
		$str["A"]	= sql_fetch(" select sum(wr_content)as sm from {$bo_table} where substr(wr_2,6,2) = '".substr($date,5,2)."' ");
		$str["B"]	= sql_fetch(" select sum(wr_content)as sm from {$bo_table} where wr_2 >= '".G5_TIME_YMD."' or wr_2 = '' ");

		return $str;
	}

	echo "<table>";
	echo "	<tr><td class='border_n'>".date("Y")."년 ".date("m")."월</td></tr>";

	echo "	<tr><td colspan='2' class='border_n'></td></tr>";
	echo "	<tr><td colspan='2'>LNJ</td></tr>".br;
	echo "	<tr><td class='wd150'>당월 상환금액</td><td>".L_nf(L_sm("1", "g5_write_profit")["A"]["sm"], ",")."</td></tr>";
	echo "	<tr><td class='wd150'>투자 잔액</td><td>".L_nf(L_sm("1", "g5_write_profit")["B"]["sm"], ",")."</td></tr>";

	echo "	<tr><td colspan='2' class='border_n'><br /><br /></td></tr>";

	echo "	<tr><td colspan='2' class='border_n'>LJJ</td></tr>".br;
	echo "	<tr><td class='wd150'>당월 상환금액</td><td>".L_nf(L_sm("1", "g5_write_profit2")["A"]["sm"], ",")."</td></tr>";
	echo "	<tr><td class='wd150'>투자 잔액</td><td>".L_nf(L_sm("1", "g5_write_profit2")["B"]["sm"], ",")."</td></tr>";

?>

<form name="test" action="./test.php">
	<input type="text" name="test1" value="<?=$_REQUEST["test1"]?>"><br /><br/>
	<textarea name="test2" rows="" cols=""><?=$_REQUEST["test2"]?></textarea><br /><br/>
	<input type="range" name="test3" value="<?=$_REQUEST["test3"]?>"><br /><br/>
	<input type="color" name="test4" value="<?=$_REQUEST["test4"]?>"><br /><br/>
	<input type="submit">
</form>

<br/><br/>
<?=$_REQUEST["test1"]?>
<br/><br/>
<?=$_REQUEST["test2"]?>
<br/><br/>
<?=$_REQUEST["test3"]?>
<br/><br/>
<?=$_REQUEST["test4"]?>
