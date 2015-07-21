<?php $this->load->view('main_top_v');?>

<div class="container white_container">
    <div id="docs_wrap" class="row">
        <div class="col-lg-8">
            <h2>푸시윙 푸시 알림 가이드</h2>
            <h3 class="margintop30">소개</h3>
            <p>푸시 알림은 당신의 웹사이트의 사용자 참여도 증가와 재방문율 증가에 크게 도움이 되는 도구입니다. 이 가이드는 푸시윙을 이용하여 푸시 알림을 보내는 방법을 안내해드립니다.</p>
            <h3 class="margintop30">푸시 알림 보내기</h3>
            <p>푸시 알림은 푸시 데이터베이스에 직접 입력하는 방식으로 이뤄집니다. 푸시윙을 이용하여 푸시 알림을 보내기 위해서는 먼저 푸시윙 파트너가 되어야 합니다. 파트너가 아니시면 <a href="<?php echo base_url('/main/communication');?>"><?php echo base_url('/main/communication');?></a> 에서 파트너 신청을 해주세요. 파트너가 되시면 데이터베이스 입력에 필요한 정보를 안내해드립니다.</p>
            <p>푸시 알림은 푸시윙 모바일 애플리케이션(아이폰, 안드로이드)을 설치한 사용자에게만 전송됩니다. 당신 웹사이트의 회원들이 푸시윙을 설치하도록 안내해주세요. <a href="http://play.google.com/store/apps/details?id=com.pushwing" target="_blank">푸시윙 안드로이드</a></p>
            <h4 class="margintop30"><strong>입력 항목</strong></h4>
            <table class="table">
                <tr>
                    <th>필드명</th>
                    <th>설명</th>
                </tr>
                <tr>
                    <td>hp</td>
                    <td>수신인 전화번호</td>
                </tr>
                <tr>
                    <td>client_id</td>
                    <td>파트너 본인의 아이디</td>
                </tr>
                <tr>
                    <td>subject</td>
                    <td>푸시 알림 제목 (최대 20자)</td>
                </tr>
                <tr>
                    <td>contents</td>
                    <td>푸시 알림 내용 (최대 200자)</td>
                </tr>
                <tr>
                    <td>url</td>
                    <td>이동 링크</td>
                </tr>
                <tr>
                    <td>ymd</td>
                    <td>연월일 정보 (YYMMDD 형식)</td>
                </tr>
                <tr>
                    <td>time</td>
                    <td>시간 정보 (HH 형)</td>
                </tr>
                <tr>
                    <td>timestamp</td>
                    <td>타임스탬프</td>
                </tr>
            </table>

            <h4 class="margintop30"><strong>코드 예시</strong></h4>
            <?php echo $this->geshilib->highlight('<?php

$config["push_server"] = "www.pushwing.com";
$config["mysql_id"] = "YOUR_PUSHWING_MYSQL_ID";
$config["mysql_password"] = "YOUR_PUSHWING_MYSQL_PW";
$config["client_id"] = "YOUR_PUSHWING_CLIENT_ID";

$hp = "01012345678";
$subject_msg = "test subject 테스트 제목 2";
$content_msg = "test msg 테스트 내용 2";
$url = "http://pushwing.com";

$idata = array(
    "hp" => $hp,
    "client_id" => $config["client_id"],
    "subject" => $subject_msg,
    "contents" => $content_msg,
    "url" => $url,
    "ymd" => date("ymd"),
    "time" => date("H")
);

send_pushwing($config,$idata);

function send_pushwing($config, $idata)
{
    $columns = "";
    $values = "";
    foreach($idata as $column => $value)
    {
        $columns .= $column . ", ";
        $values .= ("\'" . $value . "\', ");
    }

    $con = mysql_connect($config["push_server"], $config["mysql_id"], $config["mysql_password"]);
    mysql_select_db("pushwing", $con);
    mysql_query("set names utf8");
    mysql_query(sprintf("INSERT INTO push_wait (%s timestamp) VALUES (%s UNIX_TIMESTAMP())", $columns, $values));

    mysql_close($con);
}
?>', 'php'); ?>

            <h4 class="margintop30"><strong>화면 예시</strong></h4>
            <img src="<?php echo base_url('img/doc_sample.png');?>">

            <h3 class="margintop30">모듈/플러그인</h3>
            <h4><strong>XE용 애드온</strong></h4>
            <p><a href="http://www.xpressengine.com/index.php?mid=download&category_srl=18322925&parent_srl=18322917&package_srl=22616427" target="_blank" >관리자용 새글 푸시 알림 애드온</a></p>
            <p><a href="http://www.xpressengine.com/index.php?mid=download&package_srl=22616439" target="_blank" >댓글 푸시 알림 애드온</a></p>

            <h4 class="margintop20"><strong>그누보드용 플러그인</strong></h4>
            <p>게시물에 댓글이 등록되면, 게시물 작성자의 모바일로 푸시 알림을 보내주는 플러그인입니다.</p>
            <p><a href="http://sir.co.kr/bbs/board.php?bo_table=g5_plugin&wr_id=171" target="_blank">그누보드5용 플러그인</a></p>
            <p><a href="http://sir.co.kr/bbs/board.php?bo_table=g4_plugin&wr_id=15003" target="_blank">그누보드4용 플러그인</a></p>

            <h4 class="margintop20"><strong>킴스큐 플러그인</strong></h4>
            <p><a href="http://www.kimsq.co.kr/market/719/" target="_blank">푸시윙 - 스마트폰 푸시알림(IBNeer님 제작, 유료)</a></p>

            <h4 class="margintop20"><strong>푸시윙 코드이그나이터 라이브러리</strong></h4>
            <p>불의회상님이 작성해주신 코드이그나이터용 라이브러리 <a href="http://cikorea.net/source/view/785/page/1" target="_blank">http://cikorea.net/source/view/785/page/1</a></p>
       </div>
    </div>

</div><!-- /.container -->


<?php $this->load->view('main_bottom_v');?>