<?php $this->load->view('main_top_v');?>

<div class="container">
    <div class="row">
        <div class="col-lg-6 col-lg-offset-3 marginTop30">
            <img src="/img/icon.png" class="img-circle pull-left marginRight10">
            <p>푸시윙 블로그에 오신 것을 환영합니다! <br />푸시윙 서비스의 업그레이드 소식들과 푸시윙 사람들의 이야기들, 푸시윙을 활용하는 다양한 사례들을 만나보세요.</p>
        </div>
    </div>
</div>
<div class="container white_container">
    <div id="blog_wrap" class="row img_resize_needed">
        <div class="col-lg-8 col-lg-offset-2">
            <h1><a href="/main/blog/<?php echo $blog['id'];?>" class="color444"><?php echo $blog['subject'];?></a></h1>
            <div class="marginBottom50">
                <?php echo $blog['contents'];?>
            </div>
            <!-- DISQUS START -->
            <div id="disqus_thread"></div>
            <script type="text/javascript">
                /* * * CONFIGURATION VARIABLES: EDIT BEFORE PASTING INTO YOUR WEBPAGE * * */
                var disqus_shortname = 'pushwing'; // required: replace example with your forum shortname

                /* * * DON'T EDIT BELOW THIS LINE * * */
                (function() {
                    var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
                    dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
                    (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
                })();
            </script>
            <noscript>Please enable JavaScript to view the <a href="http://disqus.com/?ref_noscript">comments powered by Disqus.</a></noscript>
            <a href="http://disqus.com" class="dsq-brlink">comments powered by <span class="logo-disqus">Disqus</span></a>
            <!-- DISQUS END -->
        </div>
    </div>
</div><!-- /.container -->

<script>
    //이미지가 지나치게 큰 경우 크기를 조절
    $('#blog_wrap img').each(function(){
        var img_width = $(this).width();
        var wrap_width = $('#blog_wrap').width();
        if( img_width > wrap_width)
        {
            var img_ratio = $(this).height()/$(this).width();
            $(this).width(wrap_width * 0.6);
            $(this).height(wrap_width * 0.6 * (img_ratio));
        }
    });
</script>
<?php $this->load->view('main_bottom_v');?>