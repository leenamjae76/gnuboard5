<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

// 일자에 대한 요일을 반환합니다.
// 입력 값 예시 => 2019-01-01
// 출력 값 예시 => 일(요일) / 7
function calculate_yoil($date=G5_TIME_YMD, $output=true)
{
    $day = date('N', strtotime($date));

    if ($output == true)
    {
        switch ($day)
        {
            case '1':
                return '월';
                break;
            case '2':
                return '화';
                break;
            case '3':
                return '수';
                break;
            case '4':
                return '목';
                break;
            case '5':
                return '금';
                break;
            case '6':
                return '토';
                break;
            case '7':
                return '일';
                break;
        }
    }
    else
    {
        return $day;
    }

    return false;
}

// 오늘이 속한 주의 첫번째 날을 가져옵니다.
// 입력 값 예시 => 2019-01-01
// 출력 값 예시 => 2018-12-30
function calculate_week($date=G5_TIME_YMD)
{
    $day = date('Y-m-01', strtotime($date.' -'.calculate_yoil($date, false).'day'));

    return $day;
}

// 지난 주의 첫번째 날을 가져옵니다.
function calculate_prev_week($date=G5_TIME_YMD)
{
//    $current = calculate_week($date);

    return date('Y-m-01', strtotime(
        $date
    ));
}

// 다음 주의 마지막 날을 가져옵니다.
function calculate_next_week($date=G5_TIME_YMD)
{
//    $current = calculate_week($date);

    return date('Y-m-'.date('t', strtotime($date)), strtotime(
        $date
    ));
}

// 주말인지 아닌지 구합니다.
// 주말을 설정해야하는 경우, 이 파일을 수정해주세요.
function calculate_weekday($date=G5_TIME_YMD)
{
    $current = calculate_yoil($date);

    if($current === '일' || $current === '토')
    {
        return true;
    }
    else
    {
        return false;
    }
}

// 화면에 주를 뿌려줍니다. (테이블 헤더)
function calculate_week_view($date=G5_TIME_YMD)
{
    global $custom_week;

    // 이 변수에는 날짜가 들어갑니다.
    // 날짜 형식 : [0]=>2019-01-01, [1]=>2019-01-02 (배열 형식으로 들어감)
    $custom_week = array();

    // 변수 초기화
    $week = '';

    // 지난 주의 첫 번째 날 가져오기
    $prev = calculate_prev_week($date);

    // 테이블 데이터 작성.
    // 21까지 하는 이유는 3주만 보이게 하기 위함임. (저번주, 금주, 차주)
    for($i=0; $i<date('t', strtotime($date)); $i++)
    {
        $holiday = $template = $today = '';
        // 기준점 설정(오늘이 아니라 지난 주 ~ 차주 까지)
        $today = date('Y-m-d', strtotime("{$prev} +{$i}day"));
        $custom_week[$i] = $today;
        $yoil = calculate_yoil($today);

        // 주말 / 휴일 체크
        if(calculate_weekday($today))
        {
            $holiday = 'holiday';
            if($yoil=='토'){ $holiday.=' sat'; }
        }

        $template .= '<th class="'.$holiday.'" width="25">'.substr($today, 8, 2).'<p>'.$yoil.'</p></th>'.PHP_EOL;
        $week .= $template;
    }

    echo $week;
}
?>