<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

//	cf_product 상품정보 테이블
//	cf_product_invest 투자정보 테이블
//	cf_product_invest_return 투자금반환 테이블
//	cf_product_give 이자지급 테이블
//	cf_product_part_return 투자금 일부상환 테이블
//	cf_product_part_success 투자금 일부상환 관련 테이블
//	cf_product_success 이자지급진행,완료 여부 관련 테이블

define('br', '<br />'); // 줄바꿈
define('user_ip', $_SERVER["REMOTE_ADDR"]); // 접속자아이피
define('adm_ip', '58.230.210.38'); // 접속자아이피
define('L_login_back_url', G5_BBS_URL."/login.php?url=".urlencode('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'])); // 로그인 후 이전 페이지로 이동

	if ( $member['mb_tel'] == 'lnj' ) {
		define('user_ip', adm_ip);
	}

	if ( user_ip == adm_ip ) {
		$Lvalue_1 = "동의함";
		$Lvalue_2 = " value='1' checked ";
	}

	function L_MMpay($mbs='', $mbe='', $pay='', $memo='') {
		if(L_balanceMoney($mbs['mb_id']) < $pay) {
			echo "잔액이 부족합니다.".br;
			exit;
		}

		$param = array();
		$param['apiGbn'] = 'm_transferPending';
		$param['mb_id'] =  $mbs['mb_id'];
		$param['dstMemGuid'] = $mbe['dstMemGuid'];
		$param['pendingAmt'] = $pay;
		$result = welcomeCurl($param);

		$rs = insert_point($mbs['mb_id'], '-'.$pay, $memo);
		$re = insert_point($mbe['mb_id'], $pay, $memo);
	}

	function L_birth_de($birth, $key="jumin"){
		$result = "";
		if (is_numeric($birth)){
			$ye = substr($birth,0,2);
			$ye_date = substr(date('Y'),0,2);
			$birth = $ye_date - $ye;
			if($birth<=0) {
				$birth = $birth + 100;
			}
			$result = $birth + 1;
		}else{
			$birth = base64_decode($birth);
			for($i=strlen($birth)-1; $i>=0; $i--){
				$val2 = substr($birth, $i, 1);
				$key2 = substr($key, ($i % strlen($key)), 1);
				$val2 = $val2 ^ $key2;
				$val2 = chr(ord($val2)-strlen($birth));
				$result = $val2.$result;

				$ye = substr($result,0,2);
				$ye_date = substr(date('Y'),0,2);
				$result = $ye_date - $ye;
				if($result<=0) {
					$result = $result + 100;
				}
				$result = $result + 1;
			}
		}

		return $result;
	}

	function L_calculate_payment() {
		$str		= 0;
		$sql		= " select * from g5_member where mb_level2 = 'L' and mb_password != '' ";
		$result	= sql_query($sql);
		for ($i=0; $row=sql_fetch_array($result); $i++) {
			$sql2 = " select * from g5_point where mb_id = '".$row['mb_id']."' and po_datetime like '".date('Y-m-d')."%' ";
			$result2	= sql_query($sql2);
			$str2 = 0;
			for ($i2=0; $row2=sql_fetch_array($result2); $i2++) {
				if ( $row2['po_rel_table'] == "@charge" ) {
					$str2 ++;
				} else if ( $row2['po_rel_table'] == "@separate" ) {
					$str2 --;
				}
			}
			if($str2 > 0) {
				$str = $str + $str2;
			}
		}
		return $str;
	}

	 function mb_birth_number($birth)
	 {
		 $birth = preg_replace("/[^0-9]/", "", $birth);
		 return preg_replace("/([0-9]{6})([0-9]{7})$/", "\\1-\\2", $birth);
	 }

	function L_date_se($date_s="", $date_e="") {

		$date_start	= new DateTime($date_s);
		$date_end	= new DateTime($date_e);
		$date_se	= $date_start->diff($date_end);
		$date_y		= (int)$date_se->y;
		$date_m		= (int)$date_se->m;
		$date_d		= (int)$date_se->d;
		if($date_y > 0) { $date_y = $date_y * 12; }
		if($date_d > 0) { $date_d = 1; }
		$str			= ($date_y + $date_m + $date_d);

		return $str;
	}

	function Lget_write($write_table, $where, $option="", $option2="")
	{
		if($option) {
			if($option == "ct"){
				$row = sql_fetch(" select count(*) as ct from $write_table where ".$where." ".$option2);
	//			echo " select count(*) as ct from $write_table where ".$where." ".$option2;
				return $row['ct'];
			}else if($option == "*"){
				$row = sql_fetch(" select * from $write_table where ".$where." ".$option2);
	//			echo " select * from $write_table where ".$where." ".$option2;
				return $row;
			}else{
				$row = sql_fetch(" select * from $write_table where ".$where." ".$option2);
	//			echo " select * from $write_table where ".$where." ".$option2;
				return $row[$option];
			}
		} else {
			return sql_fetch(" select * from $write_table where ".$where." ".$option2);
		}
	}

	function L_DN($value='', $option='')
	{
		$value = explode("|", $value);
		for ($i=0; $i < count($value); $i++) {
			if ($option == "&&" && $value[$i] != "") {
				return $str = "";
			} else if ($value[$i] == "") {
				$str = ' style="display:none;" ';
			}
		}
		return $str;
	}

	// 메인페이지에서 언론기사 출력하는 함수
	function Lget_main_board()
	{

		$sql		= " select * from funding_news_list order by idx desc limit 0,3 ";
		$result	= sql_query($sql);
		$str		= '';

		for ($i=0; $row=sql_fetch_array($result); $i++) {

			$str	.=	'			<div class="newsbox nbox'.($i+1).'">'.PHP_EOL;
			$str	.=	'				<div class="newsimg" style="background-image:url('.$row['thumbnail'].');" onclick="document.location.href=\"/news/funding_news.php\";"></div>'.PHP_EOL;
			$str	.=	'				<div class="newstxt">'.PHP_EOL;
			$str	.=	'					<img src="'.$row['news_logo'].'" alt="뉴스로고이미지" />'.PHP_EOL;
			$str	.=	'					<a href="'.$row['news_link'].'" target="_blank">자세히보기&gt;</a>'.PHP_EOL;
			$str	.=	'					<div class="mline"></div>'.PHP_EOL;
			$str	.=	'					<p>'.conv_subject($row['subject'], 35, '...').'</p>'.PHP_EOL;
			$str	.=	'				</div>'.PHP_EOL;
			$str	.=	'			</div>'.PHP_EOL;

		}

		return $str;
	}

	// 관리자 회원리스트에서 가입경로 출력에 사용하는 함수
	function Lget_join_url($id)
	{
		if($id=='01'){
			$str = "페이스북";
		} else if($id=='02'){
			$str = "인스타 그램";
		} else if($id=='03'){
			$str = "네이버 블로그";
		} else if($id=='04'){
			$str = "네이버 검색";
		} else if($id=='05'){
			$str = "구글검색";
		} else if($id=='06'){
			$str = "신문기사";
		} else if($id=='07'){
			$str = "친구추천";
		} else if($id=='08'){
			$str = "흥부야 재테크하자";
		} else if($id=='09'){
			$str = "P2P금융 클럽";
		} else if($id=='10'){
			$str = "투모다";
		} else if($id=='11'){
			$str = "카카오 플러스 친구";
		} else if($id=='12'){
			$str = "한국P2P금융협회";
		} else if($id=='13'){
			$str = "피자모";
		} else if($id=='14'){
			$str = "크사모";
		} else if($id=='15'){
			$str = "기타";
		} else if($id=='16'){
			$str = "펀사모";
		} else if($id=='17'){
			$str = "P2P연구소";
		} else if($id=='AT'){
			$str = "알통";
		}else{
			$str = $id;
		}


		return $str;
	}

	// 투자하기 이자 상환상태 확인
	function Lget_payments($Lid)
	{

		$sql	= " select * from cf_product where idx = '".$Lid."' ";
		$row	= sql_fetch($sql);

		if($row['state'] == '1') {
			$str = '이자상환중';
		} else if($row['state'] == '2') {
			$str = '상환완료';
	//	} else if($row['state'] == '4') {
	//		$str = '부실';
	//	} else if($row['state'] == '5') {
	//		$str = '중도일시상환';
		}

		return $str;
	}

	// 메인, 투자하기 이미지 위에 아이콘 출력
	function Lget_img_icon($Lid)
	{

		$str = "";
		$sql = " select * from cf_product where idx = '".$Lid."' ";
		$result	= sql_query($sql);

		for ($i=0; $row=sql_fetch_array($result); $i++) {
			if($row['img_left1']) { $str .= '<div class="invest_list_img1 invest_list_img_color_'.$row['img_left_color_1'].'">'.$row['img_left1'].'</div>'.PHP_EOL; }
			if($row['img_right1']) { $str .= '<div class="invest_list_img2 invest_list_img_color_'.$row['img_right_color_1'].'">'.$row['img_right1'].'</div>'.PHP_EOL; }
			if($row['img_left2']) { $str .= '<div class="invest_list_img3 invest_list_img_color_'.$row['img_left_color_2'].'">'.$row['img_left2'].'</div>'.PHP_EOL; }
			if($row['img_right2']) { $str .= '<div class="invest_list_img4 invest_list_img_color_'.$row['img_right_color_2'].'">'.$row['img_right2'].'</div>'.PHP_EOL; }
			if($row['img_left3']) { $str .= '<div class="invest_list_img5 invest_list_img_color_'.$row['img_left_color_3'].'">'.$row['img_left3'].'</div>'.PHP_EOL; }
			if($row['img_right3']) { $str .= '<div class="invest_list_img6 invest_list_img_color_'.$row['img_right_color_3'].'">'.$row['img_right3'].'</div>'.PHP_EOL; }
		}
		return $str;
	}

	function Lget_color($name, $value='')
	{

		if($value == "1") { $color1 = "selected"; }
		if($value == "2") { $color2 = "selected"; }
		if($value == "3") { $color3 = "selected"; }
		if($value == "4") { $color4 = "selected"; }
		if($value == "5") { $color5 = "selected"; }
		if($value == "6") { $color6 = "selected"; }
		if($value == "7") { $color6 = "selected"; }

		$str = '<select name="'.$name.'">'.PHP_EOL;
		$str .= '	<option value="">색상'.PHP_EOL;
		$str .= '	<option value="1" '.$color1.'>빨간'.PHP_EOL;
		$str .= '	<option value="2" '.$color2.'>진회'.PHP_EOL;
		$str .= '	<option value="3" '.$color3.'>다홍'.PHP_EOL;
		$str .= '	<option value="4" '.$color4.'>적색'.PHP_EOL;
		$str .= '	<option value="5" '.$color5.'>파랑'.PHP_EOL;
		$str .= '	<option value="6" '.$color6.'>골드'.PHP_EOL;
		$str .= '	<option value="7" '.$color7.'>실버'.PHP_EOL;
		$str .= '</select>'.PHP_EOL;

		return $str;
	}

	function Ldeposit($idx, $type='')
	{
		global $member;

		$sql_common = " from cf_product_invest a, cf_product b, g5_member c ";
		$sql_search = " where a.product_idx = b.idx and a.member_idx = c.mb_no and a.member_idx = '".$member['mb_no']."'   and a.idx = '".$idx."'  and a.invest_state = 'Y' ";
		$sql_order	= " order by a.insert_date ";

		$sql = " select a.* {$sql_common} {$sql_search} {$sql_order} ";
		$result = sql_query($sql);

		$sql = " select b.* {$sql_common} {$sql_search} ";
		$product = sql_fetch($sql);

		$pay_date_day = 5;
		$start_date = new DateTime($product['loan_start_date']);
		$end_date = new DateTime(date('Y-m-d', strtotime($product['loan_start_date'].' +'.$product['invest_period'].' month')));
		if ($product['loan_end_date'] != '0000-00-00') {
			$end_date = new DateTime($product['loan_end_date']);
		}

		$loan_end_date = $end_date->format('Y-m-d');
		$rows = array();
		while ($row=sql_fetch_array($result)) {
			$rows[] = $row;
		}

		$start_date = new DateTime($product['loan_start_date']);

		for ($j=0; $j<=$product['invest_period']; $j++) {
		if (in_array($product['state'], array('', '3'))) {
			break;
		}

		$end_date = new DateTime(date('Y-m-d', strtotime($start_date->format('Y-m').' last day next month')));
		if ($product['loan_end_date'] != '0000-00-00' && $product['loan_end_date'] < $start_date->format('Y-m-d')) {
			break;
		}

		$diff = date_diff($start_date, $end_date);
		$last_day = $diff->days + 1;
		if ($end_date->format('Y-m-d') < $loan_end_date) {
			$ymd = $start_date->format('Y-m').'-'.sprintf('%02d', $pay_date_day);
			$ymd = date('Y-m-d', strtotime($ymd.' +1 month'));
		} else {
			$loan_date = new DateTime($loan_end_date);
			$diff = date_diff($start_date, $loan_date);
			$last_day = $diff->days;
			$loan_date->modify('-1 day');
			$ymd = $loan_date->format('Y-m-d');
		}

		$start_date->modify('first day of next month');
		$sql = "select * from cf_product_success where product_idx = '".$product['idx']."' and date = '".$ymd."'";
		$success = sql_fetch($sql);

		if ($last_day == 0){
			break;
		}

			for ($i=0; $i<count($rows); $i++) {

				if($ymd <= date('Y-m-d')) {
					$row = $rows[$i];
					$sql = "select count(idx) as cnt from cf_product_give where product_idx = '".$row['product_idx']."' and invest_idx = '".$row['idx']."' and date = '".$ymd."'";
					$row['give'] = sql_fetch($sql);
					if($type == '1') {
							$str = ($j+1).'차';
					} else if($type == '2') {
						if (($product['state'] == '1' || $product['state'] == '2' || $product['state'] == '5'  )&&$success['loan_interest_state'] == 'Y' && $success['invest_give_state'] == 'Y') {
							if($row['give']['cnt'] > 0){
								$str = "지급";
							} else {
								$str = "미지급";
							}
						} else {
							$str = "미지급";
						}
					}
				}else{
					if($type == '1') {
						$str = '1차';
					} else if($type == '2') {
						$str = "미지급";
					}
				}
			}
		}
		return $str;
	}

	// 투자 시작 종료일 시간 계산
	function Lget_date($Ldate='', $Lnum='')
	{

		$YY = substr($Ldate, 0, 4);
		$mm = substr($Ldate, 4, 2);
		$dd = substr($Ldate, 6, 2);
		$HH = substr($Ldate, 8, 2);
		$ii = substr($Ldate, 10, 2);
		$ss = substr($Ldate, 12, 2);

		if($Lnum == "8") {
			$str = date($YY."-".$mm."-".$dd);
		} else if($Lnum == "14") {
			$str = date($YY."-".$mm."-".$dd." ".$HH."-".$ii."-".$ss);
		}else{
			$str = date($YY."-".$mm."-".$dd." ".$HH."-".$ii."-".$ss);
		}

		return $str;
	}

	// 메인페이지 투자인원 출력 2016.11.07.LNJ
	function Lget_product_invest($pro_idx = '')
	{

		$sql	= " select count(idx) ct from cf_product_invest where product_idx = '".$pro_idx."' and invest_state = 'Y' ";
		$row	= sql_fetch($sql);
		$str	= $row["ct"];

		if(!$str) { $str = 0; }

		return $str;
	}

	// 메인페이지 상품종류 출력 2016.11.07.LNJ
	function Lget_repay($repay_type="")
	{

		if($repay_type == "1") {
			$str = "만기 일시상환";
		} else if($repay_type == "2") {
			$str = "원리금 균등상환";
		} else if($repay_type == "3") {
			$str = "원금 균등상환";
		}

		return $str;
	}

	// 대출신청 내용 qa에 출력
	function Lget_qa($id='')
	{

		$str	= sql_fetch(" select * from g5_loan where lo_id = '".$id."' ");

		return $str;
	}

	// filed 의 중복값을 제외하고 남은 값을 select로 출력
	function Lget_product($name='', $filed='', $value='', $option='')
	{

		$sql		= " select distinct(".$filed.") from cf_product";
		$result	= sql_query($sql);
		$str		= '<select name="'.$name.'">'.PHP_EOL;

		if($value) {
			$str .= '<option value='.$value.'>'.$value.'</option>'.PHP_EOL;
		} else {
			$str .= '<option value="">기간</option>'.PHP_EOL;
		}

		for ($i=0; $row=sql_fetch_array($result); $i++) {
			$str .= '<option>'.$row[$filed].'</option>'.PHP_EOL;
		}

		$str .= '</select>'.$option;

		return $str;
	}

	// 투자진행율 바
	function Lget_bar($product_row)
	{
		if($product_row["recruit_amount"]>0){
			if($product_row["total_invest_amount"]>0){
				$str =  round((($product_row["total_invest_amount"]/$product_row["recruit_amount"])*100),2);
			} else {
				$str = 0;
			}
		} else {
			$str = 0;
		}

		if($str >= 100) { $str = 100; }

		$str .= "%";

		return $str;
	}

	// /deposit/deposit_2.php : type=1:누적 투자 수익률, type=2:총 투자금액, type=3:총 투자 건수
	function Lget_deposit_2($member_id='', $type='1')
	{

		$sql	= " select sum(amount) as amount from cf_product_invest where invest_state ='Y' and cancel_date ='' and member_idx='".$m_idx."' "; //누적 투자금

		$sql	= " select sum(a.invest_return) as sum, sum(b.amount) as total, count(a.invest_return) as ct from cf_product as a left join cf_product_invest as b on a.idx = b.product_idx where b.invest_state = 'Y' and b.member_idx = '".$member_id."' ";
		$row	= sql_fetch($sql);

		if($type=="1") {
			$str = @round($row['sum'] / $row['ct'], 2);
		} else if($type=="2") {
			$str = number_format($row['total']);
		} else if($type=="3") {
			$str = number_format($row['ct']);
		}

		return $str;
	}

	// /deposit/deposit_2.php
	function Lget_deposit_2_2($member_id='', $type='1')
	{
	// state = 3 투자모집 실패
		if($type == "2") {			// 투자진행중
	//		$where_sql = " and a.start_datetime < now() and a.end_datetime > now() and a.invest_end_date = '' and b.invest_state = 'Y' ";
			$where_sql = " and a.start_datetime < now() and a.end_datetime > now() and b.invest_state = 'Y' and a.invest_end_date != '' ";
		} else if($type == "3") {	// 투자완료
			$where_sql = " and a.state = '' and b.invest_state = 'Y' and a.start_datetime <= now() and a.end_datetime <= now()";
	//	$where_sql = " and a.state != '' and b.invest_state = 'Y' and a.start_datetime >= now() and a.end_datetime <= now()";
		} else if($type == "4") {	// 상환중
			$where_sql = " and a.state = '1' and b.invest_state = 'Y' ";
		} else if($type == "5") {	// 상환완료
			$where_sql = " and a.state = '2' and b.invest_state = 'Y' ";
		} else if($type == "delete") {	// 회원탈퇴전에 진행중인 투자건이 있는지 확인
			$where_sql = " and (a.state = '1' or a.state = '') and b.invest_state = 'Y' ";
		}

		$sql = " select count(distinct b.product_idx) as cnt from cf_product a, cf_product_invest b where b.product_idx = a.idx and b.member_idx = '".$member_id."' {$where_sql} ";
		$row = sql_fetch($sql);
		$str = $row['cnt'];
		if(!$str) { $str = "-"; }
		return $str;
	}

	function Lget_certificate($product_idx='', $option='')
	{
		$row = sql_fetch(" select idx from cf_product_certificate where product_idx = '".$product_idx."' and certificate_idx = '000000' ");
		if($row['idx']) {
			if($option == '1') {
				$str = '';
			}else{
				$str = "발행";
			}
		} else {
			if($option == '1') {
				$str = ' style="background-color:#449d44;color:#fff;"';
			}else{
				$str = "미발행";
			}
		}
		return $str;
	}

	function Lprice_trans2($num)
	{
		$ret = "";
		if(!is_numeric($num))
		{
			return 0;
		}

		$arr_number = strrev($num);
		for($i =strlen($arr_number)-1; $i>=0; $i--)
		{
			/////////////////////////////////////////////////
			// 현재 자리를 구함
			$digit = substr($arr_number, $i, 1);

			///////////////////////////////////////////////////////////
			// 각 자리 명칭
			switch($digit)
			{
				case '-' : $ret .= "(-) ";
				break;
				case '0' : $ret .= "";
				break;
				case '1' : $ret .= "일";
				break;
				case '2' : $ret .= "이";
				break;
				case '3' : $ret .= "삼";
				break;
				case '4' : $ret .= "사";
				break;
				case '5' : $ret .= "오";
				break;
				case '6' : $ret .= "육";
				break;
				case '7' : $ret .= "칠";
				break;
				case '8' : $ret .= "팔";
				break;
				case '9' : $ret .= "구";
				break;
			}

			if($digit=="-") continue;

			///////////////////////////////////////////////////////////
			// 4자리 표기법 공통부분
			if($digit != 0)
			{
				if($i % 4 == 1)$ret .= "십";
				else if($i % 4 == 2)$ret .= "백";
				else if($i % 4 == 3)$ret .= "천";
			}

			///////////////////////////////////////////////////////////
			// 4자리 한자 표기법 단위


			if($i % 4 == 0){
				if( floor($i/ 4) ==0){
					$ret .= "";
				}else if(floor($i / 4)==1){
					if(strlen($arr_number) < 9){
						$ret .= "<b>만</b>";
					}
				}else if(floor($i / 4)==2){
					$ret .= "<b>억</b>";
				}else if(floor($i / 4)==3){
					$ret .= "<b>조</b>";
				}else if(floor($i / 4)==4){
					$ret .= "<b>경</b>";
				}
			}
		}

		return $ret;
	}

	function Lprice_trans3($num)
	{
		$ret = "";
		if(!is_numeric($num))
		{
			return 0;
		}

		$arr_number = strrev($num);
		for($i =strlen($arr_number)-1; $i>=0; $i--)
		{
			/////////////////////////////////////////////////
			// 현재 자리를 구함
			$digit = substr($arr_number, $i, 1);

			///////////////////////////////////////////////////////////
			// 각 자리 명칭
			switch($digit)
			{
				case '-' : $ret .= "(-) ";
				break;
				case '0' : $ret .= "";
				break;
				case '1' : $ret .= "1";
				break;
				case '2' : $ret .= "2";
				break;
				case '3' : $ret .= "3";
				break;
				case '4' : $ret .= "4";
				break;
				case '5' : $ret .= "5";
				break;
				case '6' : $ret .= "6";
				break;
				case '7' : $ret .= "7";
				break;
				case '8' : $ret .= "8";
				break;
				case '9' : $ret .= "9";
				break;
			}

			if($digit=="-") continue;

			///////////////////////////////////////////////////////////
			// 4자리 표기법 공통부분
			if($digit != 0)
			{
				if($i % 4 == 1)$ret .= "십";
				else if($i % 4 == 2)$ret .= "백";
				else if($i % 4 == 3)$ret .= "천";
			}

			///////////////////////////////////////////////////////////
			// 4자리 한자 표기법 단위


			if($i % 4 == 0){
				if( floor($i/ 4) ==0){
					$ret .= "";
				}else if(floor($i / 4)==1){
	//                if(strlen($arr_number) < 9){
						$ret .= "만";
	//                }
				}else if(floor($i / 4)==2){
					$ret .= "억";
				}else if(floor($i / 4)==3){
					$ret .= "조";
				}else if(floor($i / 4)==4){
					$ret .= "경";
				}
			}
		}

		return $ret;
	}

	function Lprice_trans($num)
	 {
	  $ret = "";
	  if(!is_numeric($num))
	  {
	   return 0;
	  }

	  $arr_number = strrev($num);
	  for($i =strlen($arr_number)-1; $i>=0; $i--)
	  {
	   /////////////////////////////////////////////////
	   // 현재 자리를 구함
	   $digit = substr($arr_number, $i, 1);

	   ///////////////////////////////////////////////////////////
	   // 각 자리 명칭
	   switch($digit)
	   {
		case '-' : $ret .= "(-) ";
			break;
		case '0' : $ret .= "";
			break;
		case '1' : $ret .= "일";
			break;
		case '2' : $ret .= "이";
			break;
		case '3' : $ret .= "삼";
			break;
		case '4' : $ret .= "사";
			break;
		case '5' : $ret .= "오";
			break;
		case '6' : $ret .= "육";
			break;
		case '7' : $ret .= "칠";
			break;
		case '8' : $ret .= "팔";
			break;
		case '9' : $ret .= "구";
			break;
	   }

		if($digit=="-") continue;

		///////////////////////////////////////////////////////////
		// 4자리 표기법 공통부분
		if($digit != 0)
		{
		 if($i % 4 == 1)$ret .= "십";
		 else if($i % 4 == 2)$ret .= "백";
		 else if($i % 4 == 3)$ret .= "천";
		}

		///////////////////////////////////////////////////////////
		// 4자리 한자 표기법 단위
		if($i % 4 == 0)
		{
		 if( floor($i/ 4) ==0)$ret .= "";
		 else if(floor($i / 4)==1)$ret .= "<b>만</b>";
		 else if(floor($i / 4)==2)$ret .= "<b>억</b>";
		 else if(floor($i / 4)==3)$ret .= "<b>조</b>";
		 else if(floor($i / 4)==4)$ret .= "<b>경</b>";
		 else if(floor($i / 4)==5)$ret .= "<b>해</b>";
		 else if(floor($i / 4)==6)$ret .= "<b>자</b>";
		 else if(floor($i / 4)==7)$ret .= "<b>양</b>";
		 else if(floor($i / 4)==8)$ret .= "<b>구</b>";
		 else if(floor($i / 4)==9)$ret .= "<b>간</b>";
		 else if(floor($i / 4)==10)$ret .= "<b>정</b>";
		 else if(floor($i / 4)==11)$ret .= "<b>재</b>";
		 else if(floor($i / 4)==12)$ret .= "<b>극</b>";
		 else if(floor($i / 4)==13)$ret .= "<b>항하사</b>";
		 else if(floor($i / 4)==14)$ret .= "<b>아승기</b>";
		 else if(floor($i / 4)==15)$ret .= "<b>나유타</b>";
		 else if(floor($i / 4)==16)$ret .= "<b>불가사의</b>";
		 else if(floor($i / 4)==16)$ret .= "<b>무량대수</b>";    }
	  }

	  return $ret;
	}

	function L_invest($option='1')
	{

		if($option == "1") {
			$L_row = sql_fetch(" SELECT sum(amount) Lsum FROM cf_product_invest WHERE invest_state = 'Y' ");
			$str = $L_row['Lsum'];
		}

		return $str;
	}

	// 수익률
	function L_product($option='1')
	{

		if($option == "1") {
			$L_row = sql_fetch(" select sum(invest_return)as Lsum, count(invest_return)as Lcount from cf_product where display = 'y' ");
			$str = $L_row['Lsum'];
		}else	if($option == "2") {
			$L_row = sql_fetch(" select sum(invest_return)as Lsum, count(invest_return)as Lcount from cf_product where display = 'y' ");
			$str = $L_row['Lcount'];
		}else	if($option == "3") {
			$L_row = sql_fetch(" select sum(invest_return)as Lsum, count(invest_return)as Lcount from cf_product where display = 'y' ");
			if( $L_row['Lsum'] !=''){
			$L_tot = ($L_row['Lsum'] / $L_row['Lcount']);
			}
			$str = round($L_tot, 2);
		}
		return $str;
	}

	function Lnumber($value='', $option='', $s='+')
	{
		$value = explode("|", $value);
		if($s=="*"){$str = "1";}else{$str = '';}
		for($i=0;$i<count($value);$i++) {
			$value_e[$i] = preg_replace("/[^0-9.]/", "", $value[$i]);
			if($s=="+") {
				$str += $value_e[$i];
			}else if($s=="-") {
				$str -= $value_e[$i];
			}else if($s=="*") {
				$str *= $value_e[$i];
			}else if($s=="/") {
				$str /= $value_e[$i];
			}
		}
		return @price_cutting($str).$option;
	}

	function Lnu($mid='', $pid='')
	{
		$row = sql_fetch("SELECT sum(invest_amount) as total_give FROM cf_product_give WHERE product_idx = '".$pid."' and sending='Y' and member_idx ='".$mid."' ");

		if($row['total_give']) {
			$str = $row['total_give'];
		}else{
			$str = "0";
		}

		return $str;
	}

	function L_sms_replace($value='')
	{
		global $config;

		$original[0]		= "/{상호명}/";
		$change[0]	= $config['cf_1'];
		$original[1]		= "/{대표번호}/";
		$change[1]	= $config['cf_2'];
		$original[2]		= "/{사이트URL}/";
		$change[2]	= $config['cf_3'];
	/*
		$original[3]		= "/{PROJECT_NAME}/";
		$change[3]	= "{".$value."}";
		$original[4]		= "/{OPEN_DATE}/";
		$change[4]	= "{".$value."}";
		$original[5]		= "/{TARGET_PRICE}/";
		$change[5]	= "{".$value."}";
		$original[6]		= "/{YEAR_PROFIT_PER}/";
		$change[6]	= "{".$value."}";
		$original[7]		= "/{TERM}/";
		$change[7]	= "{".$value."}";
		$original[8]		= "/{OPEN_HOUR}/";
		$change[8]	= "{".$value."}";
		$original[9]		= "/{FUNDING_PRICE}/";
		$change[9]	= "{".$value."}";
		$original[10]	= "/{USER_NAME}/";
		$change[10]	= "{".$value."}";
		$original[11]	= "/{PANCREAS}/";
		$change[11]	= "{".$value."}";
		$original[12]	= "/{PAY_DATE}/";
		$change[12]	= "{".$value."}";
		$original[13]	= "/{BANK}/";
		$change[13]	= "{".$value."}";
		$original[14]	= "/{ACCOUNT_NAME}/";
		$change[14]	= "{".$value."}";
		$original[15]	= "/{ACCOUNT}/";
		$change[15]	= "{".$value."}";
		$original[16]	= "/{W_PRICE}/";
		$change[16]	= "{".$value."}";
	*/
		$str = preg_replace($original, $change, $value);

		return $str;
	}

	function Lget_product_category($id="")
	{
		if($id=='1'){
			$str = "동산";
		} else if($id=='2'){
			$str = "부동산";
		} else if($id=='3'){
			$str = "구간투자";
		} else if($id=='e'){
			$str = "리워드 이벤트";
		}

		return $str;
	}

	function L_newwin_mpoint($id='', $option='nw_mpoint')
	{
		global $g5;

		$row = sql_fetch(" select * from {$g5['new_win_table']} where '".G5_TIME_YMDHIS."' between nw_begin_time and nw_end_time and nw_device IN ( 'both', 'pc' ) and nw_division IN ( 'both', 'pc' ) and nw_mpoint_chk = '".$id."' order by nw_id asc ");
		return $row[$option];
	}

	function L_point_event()
	{
		global $g5, $member;
		$amount = '0';

		$row = sql_fetch(" select * from {$g5['point_table']} where mb_id = '".$member['mb_id']."' and po_rel_table = '@newwin_mpoint' ");
		$row2 = sql_fetch(" select sum(b.amount) as total from cf_product as a left join cf_product_invest as b on a.idx = b.product_idx where a.loan_start_date != '0000-00-00' and a.loan_end_date != '0000-00-00' and a.delYN = 'N' and a.display = 'Y' and b.invest_state = 'Y' and b.member_idx = '".$member['mb_no']."' ");

		if( ($row['po_point'] - $row2['total']) < 0) {
			$str = 0;
		}else{
			$str = $row['po_point'] - $row2['total'];
		}

		return	$str;
	}

	function InvestTimeLimit($member_type)
	{
		$random_number = 0;

		if($member_type == 1 || $member_type == 3 || $member_type == 7){
			$random_number = mt_rand(3,7);
		}else{
			$random_number = mt_rand(1,5);
		}

		return $random_number * 1000;
	}

	function L_invest_outside($value='')
	{
		if ( $value=="AT" ) {
			$str = "알통직접투자";
		} else if ( $value=="EASY" ) {
			$str = "알통자동투자";
		} else if ( $value=="TARGET" ) {
			$str = "알통예약투자";
		} else if ( $value=="SAFE" ) {
			$str = "알통트랜스퍼투자";
		} else if ( $value=="BPO" ) {
			$str = "알통BPO투자";
		}
		return $str;
	}

	// 투자잔액 합 구하기.
	function L_member_view4($mb_no='')
	{
		$sql = "select cp.*, cpi.product_idx, cpi.invest_state, sum(cpi.amount) AS amount from cf_product as cp inner join cf_product_invest as cpi on cp.idx = cpi.product_idx where cp.state !='6' and cpi.invest_state ='Y' and cpi.member_idx = '".$mb_no."' GROUP BY cpi.product_idx ";
		$result = sql_query($sql);
		for ($i=0; $row=sql_fetch_array($result); $i++) {

			$sql = "select SUM(invest_amount) as amount from cf_product_part_return where member_idx = '".$mb_no."' AND product_idx = '".$row['product_idx']."' ";
			$part = sql_fetch($sql);

			$state = '';
			$date = date('Y-m-d H:i:s');
			if ($row['end_datetime'] < $date && $row['invest_end_date'] == '') {
				$state = '투자금 모집실패';
			}
			if ($row['state'] == '2') {
				$state = '만기상환';
			}
			if ($row['state'] == '4') {
				$state = '부실';
			}
			if ($row['state'] == '5') {
				$state = '중도일시상환';
			}

			if($state == '만기상환' || $state == '투자금 모집실패' || $state == '부실' || $state == '중도일시상환') {
				$str = $str + 0;
			} else {
//				echo $row["state"]."|| 1:".$str." / 2:".$row['amount']." / 3:".$part['amount'].br;
				$str = $str + ($row['amount'] - $part['amount']);
			}
		}
		return $str;
	}

	// 숫자 변환하여 금액으로 출력
	function L_nf($value='', $s='', $option='', $round='0')
	{
		if($value != "무제한"){
			$value_g = preg_replace("/[^0-9.-]/", "", $value);

			if($s == "," && $option == "원") {
				$str = $value_g*10000;
				$str = @number_format($str, $round);
			}else 	if($s == "," && $option == "p") {
				if(!$value_g) $value_g = 0;
				$str = @number_format($value_g, $round).' p';
			}else 	if($s == ",만원") {
				if($value_g <= 0) {
					$str = "";
				} else if($value_g < 10000){
					if($value_g <= 0)
						$value_g = 0;
					$str = $value_g;
					$str = @number_format($str, $round)."원";
				}else{
					$str = $value_g /10000;
					$str = @number_format($str, $round)."만원";
				}
			}else 	if($s == "백만원") {
				if($value_g < 1000000){
					if($value_g <= 0)
						$value_g = 0;
					$str = $value_g;
					$str = @number_format($str, $round)."만원";
				}else{
					$str = $value_g /1000000;
					$str = @number_format($str, $round)."백만원";
				}
			}else 	if($s == "억원") {
				if($value_g >= 100000000){
					$str = $value_g / 100000000;
					$str = @number_format($str, $round)."억원";
				}else if($value_g >= 10000000){
					$str = $value_g / 10000000;
					$str = @number_format($str, $round)."천만원";
				}else if($value_g >= 1000000){
					$str = $value_g / 1000000;
					$str = @number_format($str, $round)."백만원";
				}else{
					if($value_g <= 0)
						$str = "-";
					$str = @number_format($value_g, $round)."원";
				}
			}else if($s == ",원") {
				$str = @number_format($value_g, $round)."원";
				if(!$str)
					$str = "0원";
			}else if($s == ",") {
				$str = @number_format($value_g, $round);
				if(!$str)
					$str = "0";
			}else if($s == ",0") {
				$str = @number_format($value_g, $round);
				if(!$str || $str == 0)
					$str = "";
			}else if($s == ",-") {
				$str = @number_format($value_g, $round);
				if(!$str || $str == 0)
//					$str = "0";
					$str = "0";
			}else if($option == "원") {
				$str = $value_g*10000;
			}else{
				$str = $value_g;
			}
		}else{
			$str=$value;
		}

		if($str=="0" && $option == "nul"){
			$str = "";
		} else if($str=="원"){
//			$str = "-";
			$str = "0";
		} else if($str=="0.00"){
			$str = "0";
		}

		return $str;
	}

	function L_loanpay($idx="", $date="")
	{
		//상품 정보 함수
		$row = get_Product($idx);
		//정산식 함수
		$investData = calculate_Proc($row['idx']);
		/* 대출자 정보용 */
		$dataList = array("code" => "LOAN", "mb_no" => 1, "amount" => $row['recruit_amount']);
		$loanInvestData = calculate_Proc($row['idx'], $dataList);
		for ($i = 0; $i < $loanInvestData['total_date_count']; $i++) {
			if(mb_substr($loanInvestData[$i]['investData']['month'],0,7) == $date){
				$loan_month_interest		+= $loanInvestData[$i]['investData']['monthTotal']['month_interest']; // 납입이자(당월)
				$loan_month_charge		+= $loanInvestData[$i]['investData']['monthTotal']['month_charge'];   // 납입수수료(당월)
			}
		}
		return $loan_month_interest;
	}

	function L_shorturl($url='')
	{
		// 네이버 단축URL Open API 예제
		$client_id = "aGJBZ3ricSapruOiGb_5"; // 네이버 개발자센터에서 발급받은 CLIENT ID
		$client_secret = "jOOHUIjgnf";// 네이버 개발자센터에서 발급받은 CLIENT SECRET
		$encText = urlencode($url);
		$postvars = "url=".$encText;
		//$is_post = true;
		$url = "https://openapi.naver.com/v1/util/shorturl?url=".$encText ;
		$is_post = false;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, $is_post);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		//curl_setopt($ch,CURLOPT_POSTFIELDS, $postvars);
		$headers = array();
		$headers[] = "X-Naver-Client-Id: ".$client_id;
		$headers[] = "X-Naver-Client-Secret: ".$client_secret;
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		$response = json_decode(curl_exec($ch), true);
		$status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close ($ch);
		if($status_code == 200) {
			return $response['result']['url'];
		} else {
			return "Error 내용:".print_r2($response);
		}
	}

	function L_multi_lms_send($to_hp, $subject="", $send_msg, $send_date=null, $etc1="", $etc2="")
	{
	    global $config;
		$api		= new gabiaSmsApi('profit','3fa7618f6fc0888ce9ccafaf894db6e5');
		$sms_r	= sql_fetch(" select * from g5_member where mb_id = 'admin' ");
		if (!$subject) { $subject = $config['cf_admin_email_name']; }

		$r = $api->multi_lms_send($to_hp, $sms_r['mb_hp'], $send_msg, $subject, "", $send_date);

		if($r == "0000"){
			$success_cnt = $api->get_success_cnt();
			$fail_list = $api->get_fail_list();
			$str .= "성공 : $success_cnt 개\n";
			$str .= "실패 목록 : $fail_list\n";
			$str .=  "이전갯수 ".$api->getBefore()."\n";
			$str .=  "남은갯수 ".$api->getAfter()."\n";
		}else{
			$str = ("SEND FAIL – " . $api->getResultCode() . " : " . $api->getResultMessage());
		}
		return $str;
	}

	// 회원탈퇴시 문자 발송.
	function L_member_del_sms_send($to_hp, $mb_name)
	{
	    global $config;
		$api		= new gabiaSmsApi('profit','3fa7618f6fc0888ce9ccafaf894db6e5');
		$sms_r	= sql_fetch(" select * from g5_member where mb_id = 'admin' ");
		if (!$subject) { $subject = $config['cf_admin_email_name']; }
		$api->sms_send($to_hp, $sms_r['mb_hp'], $subject."\n".$mb_name."회원님 회원탈퇴가 정상적으로 처리 되었습니다.\n그동안 이용해 주셔서 감사합니다.", "", "");
	}

	// 모바일 체크
	function MobileCheck()
	{
		global $HTTP_USER_AGENT;
		$MobileArray  = array("iphone","lgtelecom","skt","mobile","samsung","nokia","blackberry","android","android","sony","phone");

		$checkCount = 0;
			for($i=0; $i<sizeof($MobileArray); $i++){
				if(preg_match("/$MobileArray[$i]/", strtolower($HTTP_USER_AGENT))){ $checkCount++; break; }
			}
	   return ($checkCount >= 1) ? "M" : "";
	}

	// 잔액 확인
	function L_balanceMoney($mb_id='')
	{
			$param = array();
			$param['apiGbn'] = 'balanceMoney';
			$param['mb_id'] = $mb_id;
			$result = welcomeCurl($param);
			return $result->data->balanceTotAmt;
	}

	// 종료일 계산
	function Ldday($value)
	{
		$to = date("Y-m-d",time());
		$val = Trim($value);
		$str = intval((strtotime($val)-strtotime($to)) / 86400);

		if ($str <= 0) {
			$str = "-";
		}

		return $str;
	}

	// 남은일수 계산
	function Lget_time($value='', $option='')
	{
		if ($value == '0') {
			$str = '00';
		}else{

			$t = mktime(substr($value,11,2),substr($value,14,2),substr($value,17,2),substr($value,5,2),substr($value,8,2),substr($value,0,4)) - time();

			$y = floor($t/31104000); $t-= $y*31104000; // 일
			$m = floor($t/2592000); $t-= $m*2592000; // 일
			$d = floor($t/86400); $t-= $d*86400; // 일
			$h = floor($t/3600); $t-= $h*3600; // 시간
			$i = floor($t/60); $t-= $i*60; // 분
			$s = $t; // 초

			if ($option == "Y") {
				$str = $y;
			} else if ($option == "m") {
				$str = $m;
			} else if ($option == "d") {
				$str = $d;
			} else if ($option == "h") {
				$str = $h;
			} else if ($option == "i") {
				$str = $i;
			} else if ($option == "s") {
				$str = $s;
			}
		}
		return $str;
	}

	// 원단위 이상 절삭
	function Lfloor($value, $type='')
	{
		if ($type == '10') {
			$str = floor($value / 10) * 10;
		} else if ($type == '100') {
			$str = floor($value / 100) * 100;
		} else if ($type == '1000') {
			$str = floor($value / 1000) * 1000;
		} else if ($type == '10000') {
			$str = floor($value / 10000) * 10000;
		} else {
			$str = floor($value);
		}

		return $str;
	}

	// 오늘이 이달의 몇주차인지
	function toweeknum($timestamp) {
		$w = date('w', mktime(0,0,0, date('n',$timestamp), 1, date('y',$timestamp)));
		return ceil(($w + date('j',$timestamp) -1) / 7);
	}

	// 문자 남은 건수
	function L_sf()
	{
	    global $config;

		$api	= new gabiaSmsApi('profit','3fa7618f6fc0888ce9ccafaf894db6e5');
		$row = sql_fetch(" select count(distinct mb_hp)as ct from g5_member where mb_sms = '1' and virtual_account != '' and mb_password != '' and mb_level2 = 'I' order by mb_no desc ");

		$str["sms"] = L_nf($api->getSmsCount(), ',');
		$str["lms"] = L_nf( floor($api->getSmsCount() / 3),',');
		$str["pt"] = L_nf( floor($api->getSmsCount() / ($row['ct']*3) ),' ,');

		return $str;
	}

	function calculate_Proc2($idx, $dataList="")
	{

		$product   = get_Product($idx);
		$date      = get_Date($product, $dataList);
		$investor  = get_Investor2($product, $dataList);
		$calculate = get_Calculate($product, $date, $investor, $dataList);

		return $calculate;

	}

	function get_Investor2($product, $dataList)
	{

		$idx     = $product['idx'];
		$bond_fl = $product['bond_fl'];

		if($dataList != "" && ($dataList['code'] == "USER" || $dataList['code'] == "LOAN")){ // 투자하기 페이지에서 사용, 대출자데이터 사용

			$row = sql_fetch(" select mb_id, member_type from g5_member where mb_no = '".$dataList['mb_no']."' ");

			$rows[0] = array();
			$rows[0]['mb_id']  		= $row['mb_id'];
			$rows[0]['amount'] 		= $dataList['amount'];
			$rows[0]['member_type'] = $row['member_type'];

		}else{

			if($dataList != "" && $dataList['code'] == "INVEST_IDX"){

				$sql_plus = " and cpi.idx in (".$dataList['memList'].") ";

			}else if($dataList != "" && $dataList['code'] == "MEMBER_IDX"){

				$sql_plus = " and cpi.member_idx in (".$dataList['memList'].") ";

			}

			if($bond_fl == "Y"){ // 투자 개별건 처리 채권거래 가능

				$sql_column = " cpi.amount , ";

			}else{ // 그룹건 처리 채권거래 불가능 ( 기존상품 )

				$sql_column = " sum(cpi.amount) as amount , ";
				$sql_group  = " group by cpi.member_idx ";

			}

			$investor_sql = " select gm.mb_id, gm.mb_name, cpi.member_idx, cpi.idx, cpi.invest_outside, {$sql_column} gm.member_type
							  from cf_product_invest_2021 as cpi inner join g5_member as gm on cpi.member_idx = gm.mb_no
							  where cpi.product_idx = '".$idx."' {$sql_plus} and cpi.invest_state = 'Y' and cpi.tr_no != ''
							  {$sql_group} order by cpi.idx desc
							";

			$investor_result = sql_query($investor_sql);

			for ($i = 0; $row = sql_fetch_array($investor_result); $i++) {

				$rows[$i] = array();
				$rows[$i]['mb_id']          = $row['mb_id'];
				$rows[$i]['member_idx']     = $row['member_idx'];
				$rows[$i]['mb_name']        = $row['mb_name'];
				$rows[$i]['amount']         = $row['amount'];
				$rows[$i]['member_type']    = $row['member_type'];
				$rows[$i]['invest_idx']     = $row['idx'];
				$rows[$i]['invest_outside'] = $row['invest_outside'];

			}

		}

		return $rows;

	}

	// 숫자 변환하여 금액으로 출력
	function L_adder($a='', $option='+', $b='')
	{
		$a = preg_replace("/[^0-9.-]/", "", $a);
		$b = preg_replace("/[^0-9.-]/", "", $b);
		if ($option=="-") {
			$return = (int)$a - (int)$b;
		} else if ($option=="*") {
			$return = (int)$a * (int)$b;
		} else if ($option=="/") {
			$return = (int)$a / (int)$b;
		}else{
			$return = (int)$a + (int)$b;
		}

		return $return;
	}

	// 투자원금 상환해야 할 상품
	function L_G_invest_return()
	{
		$str = "<table class='table'>".PHP_EOL;
		$sql	= " select idx, title from cf_product where state = '5' and display = 'Y' and delYN = 'N' ";
		$result	= sql_query($sql);
		for ($i=0; $row=sql_fetch_array($result); $i++) {
			$row_1 = sql_fetch(" select product_idx from cf_product_invest_return where product_idx = '".$row['idx']."' group by product_idx ");
			if (!$row_1["product_idx"]) {
				$ii++;
				if($ii=="1"){
				$str .= "	<tr><th style='text-align: center;'>번호</th><th style='text-align: center;'>코드</th><th style='text-align: center;'>상품호</th><th>투자원금 상환해야 할 상품명</th></tr>".PHP_EOL;
				}
				$str .= "	<tr>".PHP_EOL;
				$str .= "		<td style='text-align: center;'>".$ii."</td>".PHP_EOL;
				$str .= "		<td style='text-align: center;'>".$row["idx"]."</td>".PHP_EOL;
				$str .= "		<td style='text-align: center;'>".explode("호", $row["title"])["0"]."</td>".PHP_EOL;
				$str .= "		<td><a href='https://old2.pro-fit.co.kr/adm/wc/project/calculate.php?idx=".$row["idx"]."'>".$row["title"]."</a></td>".PHP_EOL;
				$str .= "</tr>".PHP_EOL;
			}
		}
		return $str;
	}


?>