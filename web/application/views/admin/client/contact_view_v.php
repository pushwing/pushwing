<?php
var_dump($view);
if($view['content'])
{
    $type = '문의';
}
else
{
    $type = '파트너 신청';
}
?>


    	<div class="right_content">
			<h2><?php echo $type?> 상세정보</h2>

        <div class="form">
		
        <table id="rounded-corner">
		    <tbody>
                <tr>
                    <td>구분</td><td ><?php echo $type?></td>
                </tr>
    		    <tr>
    		    	<td>담당자명</td><td><?php echo $view['name']?></td>
    		    </tr>
    		    <tr>
    		    	<td>사이트주소</td><td ><a href="http://<?php echo $view['web_url']?>" target="_blank"><?php echo $view['web_url']?></a></td>
    		    </tr>
                <tr>
                    <td>이메일</td><td ><?php echo $view['email']?></td>
                </tr>
<?php
if($view['content'])
{
?>
                <tr>
                    <td>내용</td><td ><?php echo $view['content']?></td>
                </tr>
<?php
}
else
{
?>

<?php
}
?>
       		</tbody>
		</table>
		</div>
</div>
      </div>   <!--end of center content -->

	<div class="clear"></div>
	</div> <!--end of main content-->
