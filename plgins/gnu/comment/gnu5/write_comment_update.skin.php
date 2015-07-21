<?php
if (!defined('_GNUBOARD_')) exit; // 개별 페이지 접근 불가

/*
* 게시물에 댓글이 등록되면 게시물 작성자의 핸드폰에 푸시 알림을 보내는 플러그인
* Author : 이현석 Pushwing Team
* 작성일 2014.2.11
* 수정일 2014.04.03
* - url 관련 버그 수정
*/

//글 작성자의 정보 조회
$mb = get_member($wr['mb_id']);

//전화 번호가 있으면, 댓글이 등록되었다고 푸시 발송
if($mb['mb_hp'])
{
    header("Content-Type: text/html; charset=UTF-8");

    //클라이언트 정보
    //클라이언트 정보는 푸시윙 파트너가 되면 발급이 됩니다. http://pushwing.com/main/partners 에서 신청해주세요. 
    //파트너 등록 비용은 무료입니다.
    $client_id = "YOUR_CLIENT_ID";     // 당신의 클라이언트 아이디
    $mysql_id = "YOUR_MYSQL_ID";       // 당신의 푸시윙 mysql id
    $mysql_pw = "YOUR_MYSQL_PASSWORD"; // 당신의 푸시윙 mysql password

    $hp = trim(str_replace("-","",$mb["mb_hp"]));
    $timestamp = time();

    $subject = '"'.$wr['wr_subject'].'" 글에 새 댓글이 등록되었습니다.';

    $content = '"'.$wr['wr_subject'].'"글에 '.$member['mb_nick'].'님이 "'.$wr_content.'" 라고 댓글을 달았습니다.';

    $callback_url = G5_HTTP_BBS_URL.'/board.php?bo_table='.$bo_table.'&amp;wr_id='.$wr['wr_parent'].'&amp;'.$qstr.'&amp;#c_'.$comment_id;

    $con = mysql_connect("www.pushwing.com",$mysql_id,$mysql_pw);

    @mysql_query("set names 'utf8'");

    mysql_select_db("pushwing", $con);

    $sql = "INSERT INTO push_wait (hp, client_id, subject, contents, url, timestamp) VALUES ('{$hp}', '{$client_id}', '{$subject}', '{$content}', '{$callback_url}', '{$timestamp}')";

    mysql_query($sql);

    mysql_close($con);
}
?>



