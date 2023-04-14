<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// 변수 초기화
unset($list);

// 통합개발환경 오류 방지
if(!isset($board_skin_path) || !isset($custom_week)){
    $board_skin_path = __DIR__;
    $custom_week = array();
}

// 확장파일 불러오기
include_once($board_skin_path.'/lib.php');

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css">', 0);

// 회원 불러오기 (검색 조건은 여기서 추가하세요)
// 멤버 아이디를 그룹으로 묶어서 처리하는 방법이 있긴한데, 귀찮아서 그냥 아이디 다 가져오게 처리했습니다.
$mb_list = sql_query("select * from {$g5['member_table']}" );


if(!$_REQUEST["date"]){
	$date = date("Y-m", strtotime(G5_TIME_YMD));
}
$count = date('t', strtotime($date));
$start_date = calculate_prev_week($date);
$middle_date = calculate_week($date);
$end_date = calculate_next_week($date);
// 검색공식
$sch = "
    and (
        ( wr_1 < '{$start_date}' and ( wr_2 > '{$start_date}' and wr_2 < '{$end_date}' ) )
        || ( wr_1 = '{$start_date}' and wr_2 < '{$end_date}' )
        || ( wr_1 < '{$start_date}' and wr_2 > '{$end_date}' )
        || ( wr_1 >= '{$start_date}' and wr_2 = '{$end_date}' )
        || ( wr_1 >= '{$start_date}' and wr_2 < '{$end_date}' )
        || ( wr_1 = '' and wr_2 = '' )
        || ( ( wr_1 >= '{$start_date}' and wr_1 <= '{$end_date}' ) and wr_2 >= '{$end_date}' )
    )
";

$row_sm = sql_fetch(" select sum(wr_content)as sm from {$g5['write_prefix']}{$board['bo_table']} where substr(wr_2,1,7) = '".substr($date,0,7)."' ");
//	echo " select ca_name, wr_subject, wr_content, wr_1, wr_2, wr_3, wr_4, wr_5, wr_6, wr_7, wr_8, wr_10 from {$g5['write_prefix']}{$board['bo_table']} where substr(wr_2,1,7) = '".substr($date,0,7)."' ".br;
$row_tt_sm = sql_fetch(" select sum(wr_content)as sm from {$g5['write_prefix']}{$board['bo_table']} where wr_2 >= '".G5_TIME_YMD."' ");
//	echo " select ca_name, wr_subject, wr_content, wr_1, wr_2, wr_3, wr_4, wr_5, wr_6, wr_7, wr_8, wr_10 from {$g5['write_prefix']}{$board['bo_table']} where wr_2 >= '".G5_TIME_YMD."' ".br;
?>

<?php if ($admin_href) { ?>
<div id="bo_btn_top">
	<div id="bo_list_total">
		<span>Total <?=number_format($total_count) ?>건</span>
		<?=$page ?> 페이지
	</div>
	<ul class="btn_bo_user">
		<li><a href="<?=$admin_href ?>" class="btn_admin btn" title="관리자"><i class="fa fa-cog fa-spin fa-fw"></i><span class="sound_only">관리자</span></a></li>
	</ul>
</div>
<?php } ?>

<div id="hanbit_gantchart_title">
	<div style="float: left; margin: 7pxpx; width: 20%; text-align: right;">당월 상환금액 : <?=L_nf($row_sm["sm"],',만원')?></div>
	<div style="float: left; width: 60%;">
		<a href="<?=G5_BBS_URL?>/board.php?bo_table=<?=$bo_table?>&amp;date=<?=date("Y-m", strtotime($date.' -1 month'))?>"><img src="<?=$board_skin_url?>/img/arrow_back.png" alt="이전 달"></a>
		<a href="<?=G5_BBS_URL?>/board.php?bo_table=<?=$bo_table?>"><?=date("Y년 m월", strtotime($date))?></a>
		<a href="<?=G5_BBS_URL?>/board.php?bo_table=<?=$bo_table?>&amp;date=<?=date("Y-m", strtotime($date.' +1 month'))?>"><img src="<?=$board_skin_url?>/img/arrow_forward.png" alt="다음 달"></a>
	</div>
	<div style="float: left; margin: 7pxpx; width: 20%; text-align: right;">투자 잔액 : <?=L_nf($row_tt_sm["sm"],',만원')?></div>
</div>

<div id="hanbit_gantchart">
	<table style="width:100%;">
		<thead>
			<tr>
				<th width="60px">이름</th>
				<th colspan="4">프로젝트명</th>
				<?=calculate_week_view($date)?>
			</tr>
		</thead>
		<tbody>
		<?php
		for($i=0; $row=sql_fetch_array($mb_list); $i++){
			$article = sql_query("select * from {$g5['write_prefix']}{$board['bo_table']} where ca_name = '{$row['mb_name']}' {$sch} order by wr_subject desc ");
			$row_cnt = sql_num_rows($article);
			if($row_cnt==0){continue;}
			$last = '';
			$category_sum = sql_fetch(" select sum(wr_content)as sm, count(wr_content)as ct from {$g5['write_prefix']}{$board['bo_table']} where ca_name = '{$row['mb_name']}' and (wr_2 >= '".G5_TIME_YMD."' or wr_2 = '') ");
		?>
			<tr>
				<!-- 사용자 이름 -->
				<td class="last" rowspan="<?=$row_cnt+1?>"><?=$row['mb_name']?> <span style="color:red;"><?=$category_sum["ct"]?></span><br/><?=L_nf($category_sum["sm"],',만원')?></td>
			</tr>
			<?php for($k=0; $row2=sql_fetch_array($article); $k++){ ?>
				<?php
 					if(date("L")=="1"){$year_day = 366;}else{$year_day=365;}
					$date_s = preg_replace("/[^0-9]/", "", $row2["wr_1"]);
					$date_e = preg_replace("/[^0-9]/", "", $row2["wr_2"]);
					$invest_day = (strtotime($date_e) - strtotime($date_s))/60/60/24;
					$date_ii = date("Y-m", strtotime($date.' -1 month'));	// 2023-02-10 리스트에 한달 전 이자가 출력되어 한달 전 이자를 현제 달 이자로 출력하기 위한 월 -1로 수정.
//					$month_day = (date('t', strtotime($date_ii))+1);
					$month_day = (date('t', strtotime($date_ii)));

					if( $row2['wr_8'] && $date == date("Y-m") ) {
						$dts = date("Y-m-d", strtotime(substr($row2["wr_2"],0,7)."-".substr($row2["wr_1"],8,2)));
						$dte = $row2["wr_2"];
						$month_day = intval((strtotime(Trim($dte))-strtotime(Trim($dts))) / 86400);
					}

					$aa = Lfloor(($row2["wr_content"]*($row2["wr_3"]/100)/$year_day)*$invest_day);	//	전체이자 = (((투자금 * 연수익 율) / 연 총 일수) * 투자 기간 총 일수)
					$bb = Lfloor(($row2["wr_content"]*($row2["wr_3"]/100)*$month_day)/$year_day);	//	당월이자 = (((투자금 * 연수익 율) * 월 총 일수) / 연 총 일수)
					$cc = Lfloor(($row2["wr_content"]*($row2["wr_4"]/100)*$month_day)/$year_day);	//	수수료 = (((투자금 * 수수료 율) * 월 총 일수) / 연 총 일수)
					$dd = Lfloor( $bb * ( $row2["wr_5"] / 100), 10);	//	소득세 = (당월이자 * 14%) [10원미만 버림]
					$ee = Lfloor( $dd * ( $row2["wr_6"] / 100), 10);	//	주민세 = (소득세 * 10%) [10원미만 버림]
					$ff = Lfloor($dd+$ee);	//	원천징수 = (소득세 + 주민세)
					$hh = Lfloor(($row2["wr_content"]*($row2["wr_7"]/100)*$month_day)/$year_day);	//	전체이자 = (((투자금 * 연수익 율) / 연 총 일수) * 투자 기간 총 일수)
					$gg = Lfloor((($bb-$cc)-($ff))-$hh);	//	실수령이자 = ((당월이자 - 수수료) - 원천징수)
					$gg_tot = $gg_tot + $gg;
				?>
				<?php if($row_cnt <= $k+1){ $last = 'last'; } ?>

				<tr>
					<!-- 프로젝트 제목 -->
					<? if((int)substr($date_s,4,2) != (int)substr($date,5,2)) { ?>
					<td class="proj_title" <?=($row2["wr_2"]<G5_TIME_YMD)?'style="text-decoration: line-through;"':'';?> data-title="상품번호 : <?=$row2['wr_subject']?>" data-aa="전체이자(연) : <?=L_nf($aa,',원')?>" data-bb="당월이자 : <?=L_nf($bb,',원')?>" data-cc="수수료 : -<?=L_nf($cc,',원')?>" data-ff="원천징수 : -<?=L_nf($ff,',원')?>" data-gg="실수령이자 : <?=L_nf($gg,',원')?>" data-hh="대출이자 : <?=L_nf($hh,',원')?>">
						<?=$row2['wr_subject']?>
					</td>
					<? } else { ?>
					<td class="proj_title" <?=($row2["wr_2"]<G5_TIME_YMD)?'style="text-decoration: line-through;"':'';?> data-title="상품번호 : <?=$row2['wr_subject']?>" data-aa="전체이자 : -" data-bb="당월이자 : -" data-cc="수수료 : -" data-ff="원천징수 : -" data-gg="실수령이자 : -"  data-hh="대출이자 : -">
						<?=$row2['wr_subject']?>
					</td>
					<? } ?>
					<td <?=($row2["wr_2"]<G5_TIME_YMD)?'style="text-decoration: line-through;"':'';?>>
						<a href="<?=G5_BBS_URL.'/write.php?w=u&bo_table='.$bo_table.'&amp;wr_id='.$row2['wr_id']?>">
							<?=L_nf($row2['wr_content'],',')?>
						</a>
					</td>
					<td <?=($row2["wr_2"]<G5_TIME_YMD)?'style="text-decoration: line-through;"':'';?>>
						<a href="<?=G5_BBS_URL.'/board.php?bo_table='.$bo_table.'&amp;wr_id='.$row2['wr_id']?>">
							<?=$row2['wr_10']?>
						</a>
					</td>
					<td class="proj_title" <?=($row2["wr_2"]<G5_TIME_YMD)?'style="text-decoration: line-through;"':'';?> data-title="상품번호 : <?=$row2['wr_subject']?>" data-aa="전체이자(연) : <?=L_nf($aa,',원')?>" data-bb="당월이자 : <?=L_nf($bb,',원')?>" data-cc="수수료 : -<?=L_nf($cc,',원')?>" data-ff="원천징수 : -<?=L_nf($ff,',원')?>" data-gg="실수령이자 : <?=L_nf($gg,',원')?>" data-hh="대출이자 : <?=L_nf($hh,',원')?>">
						<?=$row2['wr_1']?>~<?=$row2['wr_2']?>
					</td>
					<?php for($m=0; $m<$count; $m++){ ?>

						<?php
						$tmp_s_date = $row2['wr_1'];
						$tmp_e_date = $row2['wr_2'];

						if(strtotime($start_date)>=strtotime($tmp_s_date)) $tmp_s_date = $start_date;
						if(strtotime($end_date)<strtotime($tmp_e_date)) $tmp_e_date = $end_date;
						?>
						<td class="<?=$last?>">
							<?php
//							echo date("Y-m", strtotime($date))."-".sprintf('%02d', ($m+1))."==".$tmp_e_date;
//								echo $custom_week[$m]." : ".$start_date." : ".$end_date.br;
								if( strtotime($custom_week[$m])>=strtotime($tmp_s_date) && strtotime($custom_week[$m])<=strtotime($tmp_e_date)) {
//								if( $custom_week[$m]>=$start_date && $custom_week[$m]<=$end_date) {
									if((int)substr($row2['wr_2'], -2) == ($m+1) ) {
//										echo (int)substr($row2['wr_2'], -2) ."==". ($m+1) ."&&". (int)substr($date_s,4,2) ."!=". (int)substr($date,5,2);
										if(date('Y-m-d') == date("Y-m", strtotime($date))."-".sprintf('%02d', ($m+1))) {
											echo '<div class="bar_red proj_title" style="background: yellow; color: red;" data-title="상품번호 : '.$row2['wr_subject'].'" data-aa="전체이자(연) : '.L_nf($aa,',원').'" data-bb="당월이자 : '.L_nf($bb,',원').'" data-cc="수수료 : -'.L_nf($cc,',원').'" data-ff="원천징수 : -'.L_nf($ff,',원').'" data-gg="실수령이자 : '.L_nf($gg,',원').'" data-hh="대출이자 : '.L_nf($hh,',원').'">'.($m+1).'</div>';
											$gg_d = $gg_d + $gg;
										} else {
											if((int)$row2['wr_2'] < (int)G5_TIME_YMD){
												echo '<div class="bar_red proj_title" style="cursor: pointer;" data-title="상품번호 : '.$row2['wr_subject'].'" data-aa="전체이자(연) : '.L_nf($aa,',원').'" data-bb="당월이자 : '.L_nf($bb,',원').'" data-cc="수수료 : -'.L_nf($cc,',원').'" data-ff="원천징수 : -'.L_nf($ff,',원').'" data-gg="실수령이자 : '.L_nf($gg,',원').'" data-hh="대출이자 : '.L_nf($hh,',원').'">'.($m+1).'</div>';
											}else{
												// 투자 시작일
												if(date("Y-m", strtotime($date))."-".sprintf('%02d', ($m+1))==$tmp_s_date){
// 여기 수정
													echo '<div class="bar_red2 proj_title" data-title="상품번호 : '.$row2['wr_subject'].'" data-aa="전체이자(연) : '.L_nf($aa,',원').'" data-bb="당월이자 : '.L_nf($bb,',원').'" data-cc="수수료 : -'.L_nf($cc,',원').'" data-ff="원천징수 : -'.L_nf($ff,',원').'" data-gg="실수령이자 : '.L_nf($gg,',원').'" data-hh="대출이자 : '.L_nf($hh,',원').'">→</div>';
												// 투자 종료일
												} else if(date("Y-m", strtotime($date))."-".sprintf('%02d', ($m+1))==$tmp_e_date){
													echo '<div class="bar_red2 proj_title" style="cursor: pointer;" data-title="상품번호 : '.$row2['wr_subject'].'" data-aa="전체이자(연) : '.L_nf($aa,',원').'" data-bb="당월이자 : '.L_nf($bb,',원').'" data-cc="수수료 : -'.L_nf($cc,',원').'" data-ff="원천징수 : -'.L_nf($ff,',원').'" data-gg="실수령이자 : '.L_nf(($gg+$row2['wr_content']),',원').'" data-hh="대출이자 : '.L_nf($hh,',원').'">←</div>';
												// 이자 지급일
												} else {
													echo '<div class="bar_red proj_title" style="cursor: pointer;" data-title="상품번호 : '.$row2['wr_subject'].'" data-aa="전체이자(연) : '.L_nf($aa,',원').'" data-bb="당월이자 : '.L_nf($bb,',원').'" data-cc="수수료 : -'.L_nf($cc,',원').'" data-ff="원천징수 : -'.L_nf($ff,',원').'" data-gg="실수령이자 : '.L_nf($gg,',원').'" data-hh="대출이자 : '.L_nf($hh,',원').'">'.($m+1).'</div>';
												}
											}
										}
									}else{
										if(date('Y-m-d') == date("Y-m", strtotime($date))."-".sprintf('%02d', ($m+1))) {
											echo '<div class="bar" style="border-bottom: 3px solid #ff0000; height: 20px;"></div>';
										} else {
											echo '<div class="bar"></div>';
										}
									}
								}else{
										if(date('Y-m-d') == date("Y-m", strtotime($date))."-".sprintf('%02d', ($m+1))) {
											echo '<div style="border-bottom: 3px solid #ff0000; height: 20px;"></div>';
										} else {
											echo '<div></div>';
										}
								}
							?>
						</td>
					<?php } ?>
				</tr>

			<?php } ?>
		<?php } ?>
				<tr><td colspan="2">당월이자:<?=L_nf($gg_tot,',')?></td><td colspan="2">당일이자:<?=L_nf($gg_d,',')?></td></tr>
		</tbody>
	</table>
	<div class="hanbit_tooltip dimmed"><div class="date">0000-00-00 ~ 0000-00-00</div></div>
</div>

<?php if($write_href){ ?>
<div id="btn_admin">
    <a href="<?=$write_href?>" class="btn_submit btn">프로젝트 작성</a>
</div>
<?php } ?>

<script>
	$(document).mouseup(function (e){
		if($(".hanbit_tooltip").has(e.target).length === 0){
			$(".hanbit_tooltip").hide();
		}
	});

	function closeLayer( obj ) {
		$(obj).parent().parent().hide();
	}

	$(function(){

		/* 클릭 클릭시 클릭을 클릭한 위치 근처에 레이어가 나타난다. */
		$('.proj_title').click(function(e)
		{
			var sWidth = window.innerWidth;
			var sHeight = window.innerHeight;

			var oWidth = $('.hanbit_tooltip').width();
			var oHeight = $('.hanbit_tooltip').height();

			// 레이어가 나타날 위치를 셋팅한다.
//			var divLeft = e.clientX - 0;
//			var divTop = e.clientY - 100;

			var divLeft = 10;
			var divTop = 20;

			// 레이어가 화면 크기를 벗어나면 위치를 바꾸어 배치한다.
			if( divLeft + oWidth > sWidth ) divLeft -= oWidth;
			if( divTop + oHeight > sHeight ) divTop -= oHeight;

			// 레이어 위치를 바꾸었더니 상단기준점(0,0) 밖으로 벗어난다면 상단기준점(0,0)에 배치하자.
			if( divLeft < 0 ) divLeft = 0;
			if( divTop < 0 ) divTop = 0;

			var hanbit_date = '<div class="date"></div>';
			if($(this).data("hh")=="대출이자 : 0") {
				$(".hanbit_tooltip").html($(this).data("title")+'<br>'+$(this).data("aa")+'<br>'+$(this).data("bb")+'<br>'+$(this).data("cc")+'<br>'+$(this).data("ff")+'<br>'+$(this).data("gg")+'<br>'+hanbit_date);
			} else {
				$(".hanbit_tooltip").html($(this).data("title")+'<br>'+$(this).data("aa")+'<br>'+$(this).data("bb")+'<br>'+$(this).data("cc")+'<br>'+$(this).data("ff")+'<br>'+$(this).data("hh")+'<br>'+$(this).data("gg")+'<br>'+hanbit_date);
			}

			$('.hanbit_tooltip').css({ "top": divTop, "left": divLeft, "position": "fixed", "line-height": "26px" }).show();
		});

	});
</script>
<?php if(!G5_IS_MOBILE){ ?>
<script>
	// 크로스 마우스
	if  ((document.getElementById) && window.addEventListener || window.attachEvent){
		(function(){
			var hairCol = "#ff0000";
			var d = document;
			var my = -10;
			var mx = -10;
			var r;
			var vert = "";
			var hori = "";
			var idx = document.getElementsByTagName('div').length;
			var thehairs = "<div id='ver"+idx+"' style='z-index: 128;position:absolute;top:-2px;left:-2px;"+"height:1px;width:1px;font-size:1px;border-left:dotted 1px "+hairCol+"'><\/div>"+"<div id='hor"+idx+"' style='z-index: 128;position:absolute;top:-2px;left:-2px;"+"height:1px;width:1px;font-size:1px;border-top:dotted 1px "+hairCol+"'><\/div>";
			document.write(thehairs);
			var pix = "px";
			var domWw = (typeof window.innerWidth == "number");
			var domSy = (typeof window.pageYOffset == "number");

			if (domWw) {
				r = window;
			}else{
				if (d.documentElement && typeof d.documentElement.clientWidth == "number" && d.documentElement.clientWidth != 0) {
					r = d.documentElement;
				}else{
					if (d.body && typeof d.body.clientWidth == "number") {
						r = d.body;
					}
				}
			}

			function hairs(){
				if (domWw){
					vert.height = r.innerHeight - 2 + pix;
					hori.width = '100%';
				}else{
					vert.height = r.clientHeight - 2 + pix;
					hori.width = r.clientWidth + pix;
				}
			}

			function scrl(yx){
				var y,x;
				if (domSy){
					y = r.pageYOffset;
					x = r.pageXOffset;
				}else{
					y = r.scrollTop;
					x = r.scrollLeft;
				}
				return (yx == 0)?y:x;
			}

			function mouse(e){
				var msy = (domSy)?window.pageYOffset:0;
				if (!e) e = window.event;
				if (typeof e.pageY == 'number'){
					my = e.pageY - 80 - msy;
					mx = e.pageX - 340;
				}else{
					my = e.clientY - 6 - msy;
					mx = e.clientX - 6;
				}
				vert.top = scrl(0) + pix;
				vert.left = mx + pix;
				hori.top = my + scrl(0) + pix;
			}

			function ani(){
				vert.top = scrl(0) + pix;
				hori.top = my + scrl(0) + pix;
				setTimeout(ani,300);
			}

			function init(){
				vert = document.getElementById("ver"+idx).style;
				hori = document.getElementById("hor"+idx).style;
				hairs();
				ani();
			}

			if (window.addEventListener){
				window.addEventListener("load",init,false);
				window.addEventListener("resize",hairs,false);
				document.addEventListener("mousemove",mouse,false);
			}else if (window.attachEvent){
				window.attachEvent("onload",init);
				window.attachEvent("onresize",hairs);
				document.attachEvent("onmousemove",mouse);
			}
		})();
	}
	// 크로스 마우스
</script>
<?php } ?>