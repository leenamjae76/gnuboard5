<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// add_stylesheet('css 구문', 출력순서); 숫자가 작을 수록 먼저 출력됨
add_stylesheet('<link rel="stylesheet" href="'.$board_skin_url.'/style.css">', 0);
include_once(G5_PLUGIN_PATH.'/jquery-ui/datepicker.php');
?>

<section id="bo_w">
    <h2 class="sound_only"><?php echo $g5['title'] ?></h2>

    <!-- 게시물 작성/수정 시작 { -->
    <form name="fwrite" id="fwrite" action="<?php echo $action_url ?>" onsubmit="return fwrite_submit(this);" method="post" enctype="multipart/form-data" autocomplete="off" style="width:<?php echo $width; ?>">
    <input type="hidden" name="uid" value="<?php echo get_uniqid(); ?>">
    <input type="hidden" name="w" value="<?php echo $w ?>">
    <input type="hidden" name="bo_table" value="<?php echo $bo_table ?>">
    <input type="hidden" name="wr_id" value="<?php echo $wr_id ?>">
    <input type="hidden" name="sca" value="<?php echo $sca ?>">
    <input type="hidden" name="sfl" value="<?php echo $sfl ?>">
    <input type="hidden" name="stx" value="<?php echo $stx ?>">
    <input type="hidden" name="spt" value="<?php echo $spt ?>">
    <input type="hidden" name="sst" value="<?php echo $sst ?>">
    <input type="hidden" name="sod" value="<?php echo $sod ?>">
    <input type="hidden" name="page" value="<?php echo $page ?>">
    <?php
    $option = '';
    $option_hidden = '';
    if ($is_notice || $is_html || $is_secret || $is_mail) {
        $option = '';
        if ($is_notice) {
            $option .= "\n".'<input type="checkbox" id="notice" name="notice" value="1" '.$notice_checked.'>'."\n".'<label for="notice">공지</label>';
        }

        if ($is_html) {
            if ($is_dhtml_editor) {
                $option_hidden .= '<input type="hidden" value="html1" name="html">';
            } else {
                $option .= "\n".'<input type="checkbox" id="html" name="html" onclick="html_auto_br(this);" value="'.$html_value.'" '.$html_checked.'>'."\n".'<label for="html">HTML</label>';
            }
        }

        if ($is_secret) {
            if ($is_admin || $is_secret==1) {
                $option .= "\n".'<input type="checkbox" id="secret" name="secret" value="secret" '.$secret_checked.'>'."\n".'<label for="secret">비밀글</label>';
            } else {
                $option_hidden .= '<input type="hidden" name="secret" value="secret">';
            }
        }

        if ($is_mail) {
            $option .= "\n".'<input type="checkbox" id="mail" name="mail" value="mail" '.$recv_email_checked.'>'."\n".'<label for="mail">답변메일받기</label>';
        }
    }

    echo $option_hidden;
    ?>

    <?php if ($is_category) { ?>
    <div class="bo_w_select write_div">
        <label for="ca_name"  class="sound_only">분류<strong>필수</strong></label>
        <select name="ca_name" id="ca_name" required>
            <option value="">분류를 선택하세요</option>
            <?php echo $category_option ?>
        </select>
    </div>
    <?php } ?>

    <div class="bo_w_info write_div">
        <?php if ($is_name) { ?>
            <label for="wr_name" class="sound_only">이름<strong>필수</strong></label>
            <input type="text" name="wr_name" value="<?php echo $name ?>" id="wr_name" required class="frm_input required" placeholder="이름">
        <?php } ?>

        <?php if ($is_password) { ?>
            <label for="wr_password" class="sound_only">비밀번호<strong>필수</strong></label>
            <input type="password" name="wr_password" id="wr_password" <?php echo $password_required ?> class="frm_input <?php echo $password_required ?>" placeholder="비밀번호">
        <?php } ?>

        <?php if ($is_email) { ?>
            <label for="wr_email" class="sound_only">이메일</label>
            <input type="text" name="wr_email" value="<?php echo $email ?>" id="wr_email" class="frm_input email " placeholder="이메일">
        <?php } ?>
    </div>

    <?php if ($is_homepage) { ?>
        <div class="write_div">
            <label for="wr_homepage" class="sound_only">홈페이지</label>
            <input type="text" name="wr_homepage" value="<?php echo $homepage ?>" id="wr_homepage" class="frm_input half_input" size="50" placeholder="홈페이지">
        </div>
    <?php } ?>

    <?php if ($option) { ?>
        <div class="write_div">
            <span class="sound_only">옵션</span>
            <?php echo $option ?>
        </div>
    <?php } ?>
<style>
	.input_title { float: left;
    padding: 12px;
    width: 100px; }
</style>
    <div class="bo_w_tit write_div">
        <label for="wr_subject" class="sound_only">상품번호<strong>필수</strong></label>
		<div class="input_title">상품번호</div>
        <div id="autosave_wrapper write_div">
            <input type="text" name="wr_subject" value="<?php echo $subject ?>" id="wr_subject" required class="frm_input half_input required" size="50" maxlength="255" placeholder="상품번호">
        </div>

    </div>

    <div class="bo_w_tit write_div">
        <label for="wr_content" class="sound_only">투자금액<strong>필수</strong></label>
		<div class="input_title">투자금액</div>
        <div class="wr_content">
            <input type="text" name="wr_content" value="<?php echo (htmlspecialchars($write['wr_content'])); ?>" required class="frm_input half_input required" placeholder="투자금액">
        </div>
    </div>

    <div class="bo_w_tit write_div">
        <label for="wr_3" class="sound_only">연수익%<strong>필수</strong></label>
		<div class="input_title">연수익%</div>
        <div class="wr_3">
            <input type="text" name="wr_3" value="<?=$write['wr_3']?>" required class="frm_input half_input required" placeholder="연수익%">
        </div>
    </div>

    <div class="bo_w_tit write_div">
        <label for="wr_4" class="sound_only">수수료%<strong>필수</strong></label>
		<div class="input_title">수수료%</div>
        <div class="wr_4">
<!--             <input type="text" name="wr_4" value="<?=$write['wr_4']?>" required class="frm_input half_input required" placeholder="수수료 0.66% < 연수익 15% <= 수수료 1.32%"> -->
            <input type="text" name="wr_4" value="<?=$write['wr_4']?>" required class="frm_input half_input required" placeholder="수수료 1.2%">
        </div>
    </div>

    <div class="bo_w_tit write_div">
        <label for="wr_5" class="sound_only">소득세%<strong>필수</strong></label>
		<div class="input_title">소득세%</div>
        <div class="wr_5">
            <input type="text" name="wr_5" value="<?=$write['wr_5']?>" required class="frm_input half_input required" placeholder="소득세 14% : 대부 25%">
        </div>
    </div>

    <div class="bo_w_tit write_div">
        <label for="wr_6" class="sound_only">주민세%<strong>필수</strong></label>
		<div class="input_title">주민세%</div>
        <div class="wr_6">
            <input type="text" name="wr_6" value="<?=$write['wr_6']?>" required class="frm_input half_input required" placeholder="주민세 10%">
        </div>
    </div>

    <div class="bo_w_tit write_div">
        <label for="wr_7" class="sound_only">대출금리%<strong>필수</strong></label>
		<div class="input_title">대출금리%</div>
        <div class="wr_7">
            <input type="text" name="wr_7" value="<?=$write['wr_7']?>" required class="frm_input half_input required" placeholder="대출금리 %">
        </div>
    </div>

    <div class="bo_w_tit write_div">
        <label for="wr_10" class="sound_only">대출자<strong>필수</strong></label>
		<div class="input_title">대출자</div>
        <div class="wr_10">
            <input type="text" name="wr_10" value="<?=$write['wr_10']?>" required class="frm_input half_input required" placeholder="대출자">
        </div>
    </div>

    <div class="bo_w_tit write_div">
        <label for="wr_1" class="sound_only">시작일<strong>필수</strong></label>
		<div class="input_title">시작일</div>
        <div class="wr_1">
            <input type="text" name="wr_1" placeholder="시작일" value="<?php echo $wr_1; ?>" class="frm_input half_input date_start">
        </div>
    </div>

    <div class="bo_w_tit write_div">
        <label for="wr_2" class="sound_only">종료일<strong>필수</strong></label>
		<div class="input_title">종료일</div>
        <div class="wr_2">
            <input type="text" name="wr_2" placeholder="종료일" value="<?php echo $wr_2; ?>" class="frm_input half_input date_end">
        </div>
    </div>

    <div class="bo_w_tit write_div">
        <label for="wr_8" class="sound_only">중도상환<strong>필수</strong></label>
		<div class="input_title">중도상환</div>
        <div class="wr_8">
            <input type="text" name="wr_8" placeholder="중도상환" value="<?=$write['wr_8']?>" class="frm_input half_input date_end">
        </div>
    </div>

    <?php if ($is_use_captcha) { //자동등록방지  ?>
    <div class="write_div">
        <?php echo $captcha_html ?>
    </div>
    <?php } ?>


    <div class="btn_confirm write_div">
        <a href="./board.php?bo_table=<?php echo $bo_table ?>" class="btn_cancel btn">취소</a>
        <input type="submit" value="작성완료" id="btn_submit" accesskey="s" class="btn_submit btn">
    </div>
    </form>

    <script>
    <?php if($write_min || $write_max) { ?>
    // 글자수 제한
    var char_min = parseInt(<?php echo $write_min; ?>); // 최소
    var char_max = parseInt(<?php echo $write_max; ?>); // 최대
    check_byte("wr_content", "char_count");

    $(function() {
        $("#wr_content").on("keyup", function() {
            check_byte("wr_content", "char_count");
        });
    });

    <?php } ?>
    function html_auto_br(obj)
    {
        if (obj.checked) {
            result = confirm("자동 줄바꿈을 하시겠습니까?\n\n자동 줄바꿈은 게시물 내용중 줄바뀐 곳을<br>태그로 변환하는 기능입니다.");
            if (result)
                obj.value = "html2";
            else
                obj.value = "html1";
        }
        else
            obj.value = "";
    }

    $(".date_start").datepicker({dateFormat: 'yy-mm-dd', showMonthAfterYear: true,
        changeMonth: true,
        changeYear: true,
        onClose: function( selectedDate ) {
            // 시작일(fromDate) datepicker가 닫힐때
            // 종료일(toDate)의 선택할수있는 최소 날짜(minDate)를 선택한 시작일로 지정
            $(".date_end").datepicker( "option", "minDate", selectedDate );
        }
    });

    $(".date_end").datepicker({dateFormat: 'yy-mm-dd', showMonthAfterYear: true,
        changeMonth: true,
        changeYear: true,
        onClose: function( selectedDate ) {
            // 시작일(fromDate) datepicker가 닫힐때
            // 종료일(toDate)의 선택할수있는 최소 날짜(minDate)를 선택한 시작일로 지정
            $(".date_start").datepicker( "option", "maxDate", selectedDate );
        }
    });

    <?php if($w=='u'){ ?>
    $(".date_start").datepicker( "option", "maxDate", '<?php echo $wr_2; ?>' );
    $(".date_end").datepicker( "option", "minDate", '<?php echo $wr_1; ?>' );
    <?php } ?>

    $(function() {
        $("input[name=wr_3]").on("change", function() {
			if($("input[name=wr_3]").val() >= 15) {
				$("input[name=wr_4]").val("1.2");
			} else if($("input[name=wr_3]").val() < 15) {
				$("input[name=wr_4]").val("1.2");
			}
			$("input[name=wr_5]").val("14");
			$("input[name=wr_6]").val("10");
			$("input[name=wr_7]").val("0");
        });
    });

    $(function() {
        $("input[name=wr_4]").on("change", function() {
			if($("input[name=wr_4]").val() == 0) {
				$("input[name=wr_5]").val("25");
			} else {
				$("input[name=wr_5]").val("14");
			}
        });
    });

    function fwrite_submit(f)
    {
        <?php echo $editor_js; // 에디터 사용시 자바스크립트에서 내용을 폼필드로 넣어주며 내용이 입력되었는지 검사함   ?>

        var subject = "";
        var content = "";
        $.ajax({
            url: g5_bbs_url+"/ajax.filter.php",
            type: "POST",
            data: {
                "subject": f.wr_subject.value,
                "content": f.wr_content.value
            },
            dataType: "json",
            async: false,
            cache: false,
            success: function(data, textStatus) {
                subject = data.subject;
                content = data.content;
            }
        });

        if (subject) {
            alert("제목에 금지단어('"+subject+"')가 포함되어있습니다");
            f.wr_subject.focus();
            return false;
        }

        if (content) {
            alert("내용에 금지단어('"+content+"')가 포함되어있습니다");
            if (typeof(ed_wr_content) != "undefined")
                ed_wr_content.returnFalse();
            else
                f.wr_content.focus();
            return false;
        }

        if (document.getElementById("char_count")) {
            if (char_min > 0 || char_max > 0) {
                var cnt = parseInt(check_byte("wr_content", "char_count"));
                if (char_min > 0 && char_min > cnt) {
                    alert("내용은 "+char_min+"글자 이상 쓰셔야 합니다.");
                    return false;
                }
                else if (char_max > 0 && char_max < cnt) {
                    alert("내용은 "+char_max+"글자 이하로 쓰셔야 합니다.");
                    return false;
                }
            }
        }

        <?php echo $captcha_js; // 캡챠 사용시 자바스크립트에서 입력된 캡챠를 검사함  ?>

        document.getElementById("btn_submit").disabled = "disabled";

        return true;
    }
    </script>
</section>
<!-- } 게시물 작성/수정 끝 -->