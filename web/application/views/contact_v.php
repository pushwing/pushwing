<?php $this->load->view('main_top_v');?>

<div id="main_intro">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 marginTop50">
                <img id="main_logo" src="<?php echo base_url('/img/logo_big.png');?>">
                <h3>통합 사설 알림 센터</h3>
                <h4>모바일 앱을 개발하지 않아도 당신 웹사이트 회원의 스마트폰에 <br /><b>무료로 푸시 알림</b>을 보낼 수 있습니다.</h4>
                <a href="http://play.google.com/store/apps/details?id=com.pushwing" target="_blank"><img class="download_btn marginTop10" src="/img/android_btn.png"></a>
            </div>
            <div class="col-lg-6 marginTop50">
                <img src="/img/main_example.png">
            </div>
        </div>
    </div>
</div>

<div>
    <div class="container white_container">
        <h2><span class="glyphicon glyphicon-exclamation-sign"></span> Why Pushwing?</h2>
        <div class="row">
            <div class="col-lg-4">
                <h3><span class="glyphicon glyphicon-plane"></span> Retention</h3>
                <p>푸시 알림은 재방문 사용자를 2배로 증가시킵니다. 사람들은 이메일보다 푸시 알림에 3배 빨리 반응합니다. 단언컨대 푸시 알림은 가장 완벽한 재방문 유도 도구입니다.</p>
            </div>
            <div class="col-lg-4">
                <h3><span class="glyphicon glyphicon-road"></span> Simple</h3>
                <p>푸시윙은 당신이 쉽게 푸시 알림을 보낼 수 있도록 아주 쉬운 API와 라이브러리를 제공합니다.</p>
            </div>
            <div class="col-lg-4">
                <h3><span class="glyphicon glyphicon-usd"></span> Free</h3>
                <p>푸시윙은 무료입니다. 자주가는 웹사이트로 부터 푸시 알림을 받고자 푸시윙 모바일 앱을 설치하는 사용자들은 물론이고, 이들에게 푸시 알림을 보내고자하는 웹사이트들도 무료로 사용할 수 있습니다.</p>
            </div>

        </div>
    </div>
</div>

<div>
    <div class="container white_container">
        <div class="row">
            <div id="example" class="col-lg-6">
                <h2><span class="glyphicon glyphicon-user"></span> Who need Pushwing?</h2>
                <h5 class="marginTop20">당신의 웹사이트가 아래 목록에 해당하나요?</h5>
                <ul>
                    <li><span class="glyphicon glyphicon-ok"></span>&nbsp; </span><strong>Q&A</strong> 혹은 문의</strong> 기능이 있는 웹사이트</li>
                    <li><span class="glyphicon glyphicon-ok"></span>&nbsp; <strong>회원간에 쪽지</strong>를 주고 받는 기능이 있는 웹사이트</li>
                    <li><span class="glyphicon glyphicon-ok"></span>&nbsp; <strong>회원이 글을 작성할 수 있는 게시판</strong>이 있는 웹사이트</li>
                    <li><span class="glyphicon glyphicon-ok"></span>&nbsp; 회원간 <strong>중고 거래</strong> 기능이 있는 웹사이트</li>
                </ul>
                <br />
                <h5>혹은 위의 목록에 해당하는 웹사이트를 제작해주는 업체인가요? </h5>
                <h5>그렇다면 푸시윙은 당신을 위한 서비스입니다.</h5>
                <a href="https://dl.dropboxusercontent.com/u/3541876/%ED%91%B8%EC%8B%9C%EC%9C%99/%ED%91%B8%EC%8B%9C%EC%9C%99%EC%86%8C%EA%B0%9C%EC%84%9C_with_demo.pdf" class="btn btn-warning" target="_blank">소개서 다운로드</a>
            </div>
            <div class="col-lg-5">
                <h2><span class="glyphicon glyphicon-plus-sign"></span> 파트너 신청</h2>
                <p class="marginTop20">푸시윙을 활용하여 무료로 푸시 알림을 보내고 당신의 웹사이트를 더욱 활성화 시키세요!</p>
                <form action="" method="post" class="marginTop30" style="width:80%">

                    <h5>소속과 성명</h5>
                    <input type="text" name="name" value="" class="form-control" />

                    <h5>웹사이트 주소</h5>
                    <input type="text" name="web_url" value="" class="form-control" />

                    <h5>Email</h5>
                    <input type="text" name="email" value="" class="form-control" />

                    <input type="hidden" name="mode" value="partner">
                    <div class="marginTop20"><input type="submit" value="Submit" class="btn btn-primary btn-block" /></div>

                </form>
            </div>
        </div>

    </div><!-- /.container -->
</div>

<script>
    $(window).load(function(){
        resize_main_logo_img_size();
    });

    function resize_main_logo_img_size()
    {
        var window_width = $(window).width();
        var main_logo = $('#main_logo');

        if(window_width < main_logo.width())
        {
            $('#main_logo').css('width',window_width * 0.8);
        }
    }

</script>
<?php $this->load->view('main_bottom_v');?>