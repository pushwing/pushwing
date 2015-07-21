<?php $this->load->view('main_top_v');?>

<div class="container">
    <div class="row">
        <div class="col-lg-6 col-lg-offset-3 marginTop30">
            <img src="/img/icon.png" class="pull-left marginRight10">
            <p>푸시윙 블로그에 오신 것을 환영합니다! <br />푸시윙 서비스의 업그레이드 소식들과 푸시윙 사람들의 이야기들, 푸시윙을 활용하는 다양한 사례들을 만나보세요.</p>
        </div>
    </div>
</div>
<div class="container white_container">
    <div id="blog_list_wrap" class="row">
        <div class="col-lg-8 col-lg-offset-2">
            <?php foreach($blog_list as $blog):?>
                <h1><a href="/main/blog/<?php echo $blog['id'];?>" class="color444"><?php echo $blog['subject'];?></a></h1>
                <div class="marginBottom50">
                    <?php echo $blog['contents'];?>
                </div>
            <?php endforeach;?>
        </div>
    </div>
</div><!-- /.container -->

<script>
    //이미지가 지나치게 큰 경우 크기를 조절
    $('#blog_list_wrap img').each(function(){
        var img_width = $(this).width();
        var wrap_width = $('#blog_list_wrap').width();
        if( img_width > wrap_width)
        {
            var img_ratio = $(this).height()/$(this).width();
            $(this).width(wrap_width * 0.6);
            $(this).height(wrap_width * 0.6 * (img_ratio));
        }
    });
</script>
<?php $this->load->view('main_bottom_v');?>