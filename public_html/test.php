<?php

$data = L_holiday("2023", "01");

print_r2($data);

exit;
$json->response->body->items->item[$i]->dateName
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
echo "	<tr><td colspan='2'>이남재</td></tr>".br;
echo "	<tr><td class='wd150'>당월 상환금액</td><td>".L_nf(L_sm("1", "g5_write_profit")["A"]["sm"], ",")."</td></tr>";
echo "	<tr><td class='wd150'>투자 잔액</td><td>".L_nf(L_sm("1", "g5_write_profit")["B"]["sm"], ",")."</td></tr>";

echo "	<tr><td colspan='2' class='border_n'><br /><br /></td></tr>";

echo "	<tr><td colspan='2' class='border_n'>이재준</td></tr>".br;
echo "	<tr><td class='wd150'>당월 상환금액</td><td>".L_nf(L_sm("1", "g5_write_profit2")["A"]["sm"], ",")."</td></tr>";
echo "	<tr><td class='wd150'>투자 잔액</td><td>".L_nf(L_sm("1", "g5_write_profit2")["B"]["sm"], ",")."</td></tr>";

/*
	$json = '{"foo-bar": "12345", "test2":"1232"}';

	//	$json[2][1] = "test2-1";
	//	$json[2][2] = "test2-2";

	$obj = json_decode($json);

	//	print $obj->{'foo-bar'}; // 12345
	print_r2($obj);

	echo br."〓〓〓〓〓〓〓〓〓〓〓〓〓〓〓〓〓〓〓〓〓〓〓〓〓〓〓〓〓〓〓〓〓〓〓〓〓〓〓〓〓〓〓〓〓〓〓〓〓〓".br;
	print_r2($json);
*/
?>