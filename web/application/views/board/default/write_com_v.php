<script type="text/javascript" src="/include/jquery-swfupload/swfupload.js"></script>
<script type="text/javascript" src="/include/jquery-swfupload/jquery.swfupload.js"></script>
<script type="text/javascript" src="/include/jquery-swfupload/handler.js"></script>
<script type="text/javascript">

$(document).ready(function() {
	$('#swfupload-control').swfupload({
		upload_url: "/action/upload_process/",
		post_params: {"PHPSESSID": "<?php echo  $this->session->userdata('session_id');?>", "user_id": "<?php echo $this->session->userdata('user_id')?>"},
		file_post_name: 'uploadfile',
		file_size_limit : "3024",
		file_types : "*.*",
		file_types_description : "All Files",
		file_upload_limit : 10,
		flash_url : "/include/jquery-swfupload/swfupload.swf",
		button_image_url : "/include/jquery-swfupload/wdp_buttons_upload_114x29.png",
		button_width : 114,
		button_height : 29,
		button_placeholder : $('#button')[0],
		button_cursor: SWFUpload.CURSOR.HAND,
		debug_handler : FeaturesDemoHandlers.debug,

		debug: false
	})
		.bind('fileQueued', function(event, file){
			// start the upload since it's queued
			$(this).swfupload('startUpload');
		})
		.bind('uploadSuccess', function(event, file, serverData){

			var oEditor = CKEDITOR.instances.contents ;

			// Check the active editing mode.
			if (oEditor.mode == 'wysiwyg' )
			{
				var foo = serverData.split('.');

				if ( foo[1] == 'jpg' || foo[1] == 'JPG' || foo[1] == 'gif' ||foo[1] == 'GIF' || foo[1] == 'PNG' || foo[1] == 'png'  )
					oEditor.insertHtml( '<img src="<?php echo UP_DIR?>/board/'+serverData+'" />' );
				else
					oEditor.insertHtml( '<a href="<?php echo UP_DIR?>/board/'+serverData+'" target="_blank">' + foo[0] + ' (' + file.size + 'KB)</a>' );

			}
			else
				alert( 'You must be on WYSIWYG mode!' ) ;

		})
		.bind('uploadComplete', function(event, file){
			$(this).swfupload('startUpload');
		})

});

</script>

<?php
if($this->uri->segment(3) == '0') {
	$titles = "글쓰기";
} else {
	$titles = "답글쓰기";
}
?>
<div class="right_content">

<h2><?php echo $titles;?></h2>
<br>
<form action="" method="post" name="write_post" enctype="multipart/form-data">
<?php
$select_cate = explode("|", MENU_CATEGORY_WORD);
?>
<!--
<select name="category_word">
<?php foreach ($select_cate as $scate) { ?>
<option value="<?php echo $scate; ?>"><?php echo $scate; ?></option>
<?php } ?>
</select>
-->
<table id="rounded-corner">
<tr>
	<td width="80" ><strong>글 제목</strong></td>
	<td align="left"><input type="text" value="<?php echo set_value('subject')?>" size="50" name="subject" /></td>
</tr>
<tr>
	<td><strong>글 내용</strong></td>
	<td align="left">
    <textarea name="contents" id="contents"><?php echo (@$ckeditor_value)?@$ckeditor_value:''?></textarea>	</td>
</tr>
<tr>
	<td><strong>Tag </strong></td>
	<td align="left"><input type="text" value="<?php echo set_value('tags')?>" size="50" name="tags" /> (예 : 제목, 한국, 홍수)</td>
</tr>
<!--tr>
	<td>파일첨부</td>
	<td align="left"><input type="file" name="userfile" /> (7z|tgz|tar|gz|zip|rar|pdf|ppt|xls|docx|xlsx|pptx)</td>
</tr>
<tr>
	<td colspan="2" align="center"><?php echo validation_errors(); ?><?php echo @$file_error?></td>
</tr-->
<tr>
<td colspan="2"><?php echo validation_errors(); ?></td>
</tr>
<tr>
	<td colspan="2">
	<div id="swfupload-control">
		<p>모든파일, each having maximum size of 3MB</p>
		<input type="button" id="button" />
		<p id="queuestatus" ></p>
		<ol id="log"></ol>
	</div>
	<!--
	<div class="fieldset">
		<span class="legend">Debug</span>
		<div>
			<textarea id="SWFUpload_Console" wrap="off"></textarea>
		</div>
	</div>
	-->
	</td>
</tr>
<tr>
	<td colspan="2" align="center"><input type="submit" value="등록" /></td>
</tr>
</table>
</form>




</div><!-- end of right content-->
</div>   <!--end of center content -->

<div class="clear"></div>
</div> <!--end of main content-->
<?php
//툴바, textarea name, 에디터 폭, 에디터 높이
//툴바를 빈칸으로 하면 FULL 툴바가 나옵니다.
//현재 선언해놓은 것은 reply와 basic인데 입맛에 맞게 선언하여 사용하면 됩니다.
echo form_ckeditor(array(
    'toolbar'        => '',
    'id'              => 'contents',
    'width'           => '700',
    'height'          => '400'
));
?>
