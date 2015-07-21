<div class="right_content">
    <h1>파트너 등록 후 일주일 이상 시작하지 않은 클라이언트</h1>
    <table class="table table-bordered">
        <tr>
            <th>아이디</th>
            <th>클라이언트명</th>
        </tr>
        <?php foreach($no_start_clients as $client):?>
            <tr>
                <td><?php echo $client['user_id'];?></td>
                <td><?php echo $client['name'];?></td>
            </tr>
        <?php endforeach;?>
    </table>

    <h1>최종 푸시 발송일이 일주일 이상된 클라이언트</h1>
    <table class="table table-bordered">
        <tr>
            <th>아이디</th>
            <th>클라이언트명</th>
        </tr>
        <?php foreach($no_push_in_week_clients as $client):?>
            <tr>
                <td><?php echo $client['user_id'];?></td>
                <td><?php echo $client['name'];?></td>
            </tr>
        <?php endforeach;?>
    </table>

</div><!-- end of right content-->
</div>   <!--end of center content -->

<div class="clear"></div>
</div> <!--end of main content-->