<?php $this->load->view('main_top_v');?>

<div class="container white_container">
    <div id="docs_wrap" class="row">
        <div class="col-lg-7">
            <h2 class="marginBottom30">푸시윙을 활용 중인 웹사이트들 (총 <?php echo sizeof($partners);?> 개)</h2>
            <?php foreach($partners as $partner):?>
                <?php if($partner['site_url']):?>
                <p><?php echo $partner['name'];?> <a href="<?php echo prep_url($partner['site_url']);?>" target="_blank"><?php echo $partner['site_url'];?></a></p>
                <?php endif;?>
            <?php endforeach;?>
        </div>

        <a name="request"></a>
        <div class="col-lg-5">
            <h2>푸시윙이 사용되길 희망하는 웹사이트</h2>
            <p>자주 방문하는 웹사이트 중에 푸시 알림 기능이 추가되길 원하는 웹사이트가 있나요? 그렇다면 아래에 댓글로 남겨주세요. 그들이 최대한 빨리 푸시윙을 알게 되도록 노력하겠습니다. ^^ 어떤 상황에서 푸시를 받고 싶은지도 남겨주시면 더욱 좋아요!(예, A사이트에 내가 올린 질문에 댓글이 달렸을 때 푸시를 받고 싶어요)</p>
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

<!-- DISQUS JS START -->
    <script type="text/javascript">
        /* * * CONFIGURATION VARIABLES: EDIT BEFORE PASTING INTO YOUR WEBPAGE * * */
        var disqus_shortname = 'pushwing'; // required: replace example with your forum shortname

        /* * * DON'T EDIT BELOW THIS LINE * * */
        (function () {
            var s = document.createElement('script'); s.async = true;
            s.type = 'text/javascript';
            s.src = '//' + disqus_shortname + '.disqus.com/count.js';
            (document.getElementsByTagName('HEAD')[0] || document.getElementsByTagName('BODY')[0]).appendChild(s);
        }());
    </script>
<!-- DISQUS JS END -->
<?php $this->load->view('main_bottom_v');?>