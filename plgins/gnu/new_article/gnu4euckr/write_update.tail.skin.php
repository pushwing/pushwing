<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

/*
* 게시물에 새글이 등록되면 지정한 핸드폰 번호로 푸시 알림을 보내는 플러그인
* Author : 이현석 Pushwing Team
* 작성일 2014.2.27
* 수정일 2014.04.03
* - url 관련 버그 수정
*/

header("Content-Type: text/html; charset=UTF-8");

//클라이언트 정보
//클라이언트 정보는 푸시윙 파트너가 되면 발급이 됩니다. http://pushwing.com/main/partners 에서 신청해주세요.
//파트너 등록 비용은 무료입니다.
$psw_client_id = "YOUR_CLIENT_ID";     // 당신의 클라이언트 아이디
$psw_mysql_id = "YOUR_MYSQL_ID";       // 당신의 푸시윙 mysql id
$psw_mysql_pw = "YOUR_MYSQL_PASSWORD"; // 당신의 푸시윙 mysql password

//푸시 받을 운영자 핸드폰 번호
//한명일때 $psw_to = array('01012345678');
//여러명일때 $psw_to = array('01012345678','01023456789','01056789098');
$psw_to = array();

//적용할 테이블
//하나의 게시판에 적용시 $psw_tbl = array('test1');
//여러 게시판에 적용시 $psw_tbl = array('test1','test3');
//내용을 비워두면 모든 게시판에 적용됩니다.
$psw_tbl = array();


$psw_timestamp = time();

$board['bo_subject'] = iconv('euc-kr','utf-8',$board['bo_subject']);
$member['mb_nick'] = iconv('euc-kr','utf-8',$member['mb_nick']);
$wr_subject = iconv('euc-kr','utf-8',$wr_subject);

$psw_subject = '"'.$board['bo_subject'].'" 게시판에 새 글이 등록되었습니다.';

$psw_content = '"'.$board['bo_subject'].'"게시판에 '.$member['mb_nick'].'님이 "'.$wr_subject.'" 글을 작성했습니다.';

$psw_callback_url = $g4[url]/$g4[bbs].'/board.php?bo_table='.$bo_table.'&wr_id='.$wr_id.$qstr;


if($w == '')
{
    if(empty($psw_tbl))
    {
        //모든 게시판에서 작동
        pushwing_send_push($member, $psw_to,$psw_mysql_id, $psw_mysql_pw, $psw_client_id, $psw_subject, $psw_content, $psw_callback_url, $psw_timestamp);
    }
    else
    {
        //지정된 게시판에서만 작동
        if(in_array($bo_table,$psw_tbl))
        {
            pushwing_send_push($member, $psw_to,$psw_mysql_id, $psw_mysql_pw, $psw_client_id, $psw_subject, $psw_content, $psw_callback_url, $psw_timestamp);
        }
    }
}


function pushwing_send_push($member, $psw_to,$psw_mysql_id, $psw_mysql_pw, $psw_client_id, $psw_subject, $psw_content, $psw_callback_url, $psw_timestamp)
{
    if(!empty($psw_to) && is_array($psw_to))
    {
        $psw_con = mysql_connect("www.pushwing.com",$psw_mysql_id,$psw_mysql_pw);

        @mysql_query("set names 'utf8'");

        mysql_select_db("pushwing", $psw_con);

        foreach($psw_to as $psw_hp)
        {
            if(trim(str_replace('-','',$member['mb_hp'])) != $psw_hp)
            {
                $psw_ymd = date('ymd');
                $psw_time = date('H');
                $psw_sql = "INSERT INTO push_wait (hp, client_id, subject, contents, url, timestamp, ymd, time) VALUES ('{$psw_hp}', '{$psw_client_id}', '{$psw_subject}', '{$psw_content}', '{$psw_callback_url}', '{$psw_timestamp}','{$psw_ymd}','{$psw_time}')";

                mysql_query($psw_sql);
            }
        }
        mysql_close($psw_con);
    }
}
?>



