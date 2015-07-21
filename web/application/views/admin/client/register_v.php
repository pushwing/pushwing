<?php
//var_dump($user_detail);
?>

    	<div class="right_content">
			<h2>클라이언트 등록</h2>
<?php
if (validation_errors())
{
?>
        <div class="warning_box">
            <?php echo validation_errors(); ?>
        </div>
<?php
}
?>
        <div class="form">
		<form id="user_edit" name="user_edit" method="post">
        <input type="hidden" name="business_images_url" id="business_images_url" value="" />
        <input type="hidden" name="contact_id" value="<?php echo $contact_id?>" />
		<table id="rounded-corner">
		    <tfoot>
		        <tr>
		            <td colspan="2"><input type="submit" value="등록" /></td>
		        </tr>
		    </tfoot>
    		<tbody>
    		    <tr>
    		    	<td>회사명 <span style="color: red">*</span></td><td><input type="text" name="name" value="<?php echo set_value('name', $view['name'])?>" /></td>
    		    </tr>
    		    <tr>
    		    	<td>사업자등록번호</td><td ><input type="text" name="business_no" value="<?php echo set_value('business_no')?>" /></td>
    		    </tr>
    		    <tr>
    		    	<td>주소</td>
    		    	<td><input type="text" id="zip_code" name="zip_code" value="<?php echo set_value('zip_code')?>" /><input type="button" value="우편번호 검색" onClick="window.open('/manage/zipcode_search_popup','search_zip','scrollbars=yes,toolbar=no,resizable=no,width=700,height=380,left=0,top=0');" /><br>
    		    	    <input type="text" id="addr1" name="address1" value="<?php echo set_value('address1')?>" size="50" /><br />
    		    	    <input type="text" id="addr2" name="address2" value="<?php echo set_value('address2')?>" size="50" />
    		    	</td>
    		    </tr>
    		    <tr>
    		    	<td>담당자 <span style="color: red">*</span></td><td><input type="text" name="charge_person_name" value="<?php echo set_value('charge_person_name', $view['name'])?>" /></td>
    		    </tr>
    		    <tr>
    		    	<td>연락처</td><td ><input type="text" name="telephone" value="<?php echo set_value('telephone')?>" /></td>
    		    </tr>
    		    <tr>
                    <td>휴대전화 <span style="color: red">*</span></td><td><input type="text" name="charge_phone" value="<?php echo set_value('charge_phone')?>" /></td>
                </tr>
                <tr>
                    <td>E-mail <span style="color: red">*</span></td><td ><input type="text" name="charge_email" value="<?php echo set_value('charge_email', $view['email'])?>" /></td>
                </tr>
                <tr>
                    <td>사업자등록증</td>
                    <td>
                        <div id="business_images">
                        <?php echo (set_value('business_file') == '')? '파일없음':set_value('business_file');?>
                        </div>

                        <div style="padding-top: 27px;float: left;">새로 등록&nbsp</div>
                        <div style="padding-top: 20px;float: left" id="upload-control">
                        <input type="button" id="buttons" />
                        <p id="queuestatus" ></p>
                        <ol id="log"></ol>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>클라이언트ID <span style="color: red">*</span></td><td><input type="text" name="username" value="<?php echo set_value('username', $view['username'])?>"></td>
                </tr>
                <tr>
                    <td>비밀번호 <span style="color: red">*</span></td><td ><input type="password" name="user_password" value="<?php echo set_value('user_password')?>" /></td>
                </tr>
                <tr>
                    <td>비밀번호 확인 <span style="color: red">*</span></td><td ><input type="password" name="user_password1"
                                                                                  value="<?php echo set_value('user_password1')?>" /></td>
                </tr>
				<tr>
                    <td>SITE URL <span style="color: red">*</span></td><td><input type="text" name="site_url" value="<?php echo set_value('site_url', $view['web_url'])?>" /></td>
                </tr>
                <tr>
                    <td>Server IP <span style="color: red">*</span></td><td><input type="text" name="ip_address" value="<?php echo set_value('ip_address', $view['ip_address'])?>" /></td>
                </tr>
                <tr>
                    <td>MySQL ID <span style="color: red">*</span></td><td><input type="text" name="mysql_id" value="<?php echo set_value('mysql_id', $view['mysql_id'])?>" /></td>
                </tr>
                <tr>
                    <td>MySQL PASSWORD <span style="color: red">*</span></td><td><input type="text" name="mysql_pass" value="<?php echo set_value('mysql_pass', $view['mysql_pass'])?>" /></td>
                </tr>
                <tr><td colspan="2"> <span style="color: red">*</span> 항목은 필수항목입니다.</td></tr>
       		</tbody>
		</table>
		</div>
</div>
      </div>   <!--end of center content -->

	<div class="clear"></div>
	</div> <!--end of main content-->

<script type="text/javascript" src="/include/jquery-swfupload/swfupload.js"></script>
<script type="text/javascript" src="/include/jquery-swfupload/jquery.swfupload.js"></script>
<script type="text/javascript" src="/include/jquery-swfupload/handler.js"></script>
<script type="text/javascript">
$(function(){
    $('#upload-control').swfupload({
        upload_url: "/upload/index/type/business",
        post_params: {"PHPSESSID": "<?php echo  $this->session->userdata('session_id');?>", "user_id": "<?php echo $this->session->userdata('user_id')?>"},
        file_post_name: 'uploadfile',
        file_size_limit : "1024",
        file_types : "*.jpg",
        file_types_description : "JPG Files",
        file_upload_limit : 10,
        flash_url : "/include/jquery-swfupload/swfupload.swf",
        button_image_url : "/include/jquery-swfupload/wdp_buttons_upload_114x29.png",
        button_width : 114,
        button_height : 29,
        button_placeholder : $('#buttons')[0],
        button_cursor: SWFUpload.CURSOR.HAND,
        debug_handler : FeaturesDemoHandlers.debug,

        debug: false
    })
        .bind('fileQueued', function(event, file){
            // start the upload since it's queued
            $(this).swfupload('startUpload');
        })
        .bind('uploadSuccess', function(event, file, serverData){
            var t = jQuery.parseJSON( serverData );

            if (t.code == 'error')
            {
                alert(t.message);
            }
            else
            {
                $('#business_images').html(t.profile_img);
                $('#business_images_url').val(t.thumb_url);
            }

            $(this).swfupload('startUpload');
        })
        .bind('uploadComplete', function(event, file){
            $(this).swfupload('startUpload');
        })
});
</script>