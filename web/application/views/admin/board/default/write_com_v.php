<script type="text/javascript" src="/include/jquery-swfupload/swfupload.js"></script>
<script type="text/javascript" src="/include/jquery-swfupload/jquery.swfupload.js"></script>
<script type="text/javascript" src="/include/jquery-swfupload/handler.js"></script>
<script type="text/javascript">

$(function(){
	$('#swfupload-control').swfupload({
		//upload_url: "/include/jquery-swfupload/upload-file.php",
		upload_url: "/action/upload_process/<?php echo $this->tank_auth->get_username();?>",
		post_params: {"PHPSESSID": "<?php echo  $this->session->userdata('session_id');?>"},
		file_post_name: 'uploadfile',
		file_size_limit : "1024",
		//file_types : "*.jpg;*.png;*.gif",
		//file_types_description : "Image files",
		file_types : "*.*",
		file_types_description : "All Files",
		file_upload_limit : 5,
		flash_url : "/include/jquery-swfupload/swfupload.swf",
		button_image_url : "/include/jquery-swfupload/wdp_buttons_upload_114x29.png",
		button_width : 114,
		button_height : 29,
		button_placeholder : $('#button')[0],
/*
		file_queued_handler: fileQueued,
    	file_queue_error_handler: fileQueueError,
    	file_dialog_complete_handler: fileDialogComplete,
    	upload_start_handler: uploadStart,
    	upload_progress_handler: uploadProgress,
    	upload_error_handler: uploadError,
*/
		//upload_success_handler: uploadSuccess,
    	//upload_complete_handler: uploadComplete,
    	//queue_complete_handler: queueComplete,

		debug_handler : FeaturesDemoHandlers.debug,

		debug: false
	})
		.bind('fileQueued', function(event, file){
			var listitem='<li id="'+file.id+'" >'+
				'File: <em>'+file.name+'</em> ('+Math.round(file.size/1024)+' KB) <span class="progressvalue" ></span>'+
				'<div class="progressbar" ><div class="progress" ></div></div>'+
				'<p class="status" >Pending</p>'+
				'<span class="cancel" >&nbsp;</span>'+
				'</li>';
			$('#log').append(listitem);
			$('li#'+file.id+' .cancel').bind('click', function(){
				var swfu = $.swfupload.getInstance('#swfupload-control');
				swfu.cancelUpload(file.id);
				$('li#'+file.id).slideUp('fast');
			});
			// start the upload since it's queued
			$(this).swfupload('startUpload');
		})
		.bind('fileQueueError', function(event, file, errorCode, message){
			alert('Size of the file '+file.name+' is greater than limit');
		})
		.bind('fileDialogComplete', function(event, numFilesSelected, numFilesQueued){
			$('#queuestatus').text('Files Selected: '+numFilesSelected+' / Queued Files: '+numFilesQueued);
		})
		.bind('uploadStart', function(event, file){
			$('#log li#'+file.id).find('p.status').text('Uploading...');
			$('#log li#'+file.id).find('span.progressvalue').text('0%');
			$('#log li#'+file.id).find('span.cancel').hide();
		})
		.bind('uploadProgress', function(event, file, bytesLoaded){
			//Show Progress
			var percentage=Math.round((bytesLoaded/file.size)*100);
			$('#log li#'+file.id).find('div.progress').css('width', percentage+'%');
			$('#log li#'+file.id).find('span.progressvalue').text(percentage+'%');
		})
			/*
		.bind('uploadSuccess', function(event, file, serverData){
			var item=$('#log li#'+file.id);
			item.find('div.progress').css('width', '100%');
			item.find('span.progressvalue').text('100%');
			var pathtofile='<a href="/uploads/'+file.name+'" target="_blank" >view &raquo;</a>';
			item.addClass('success').find('p.status').html('Done!!! | '+pathtofile);
		})
		*/
		.bind('uploadSuccess', function(event, file, serverData){
			var item=$('#log li#'+file.id);
			item.find('div.progress').css('width', '100%');
			item.find('span.progressvalue').text('100%');
			var pathtofile='<a href="/data/<?php echo $this->tank_auth->get_username();?>/'+serverData+'" target="_blank" >view &raquo;</a>';
			item.addClass('success').find('p.status').html('Done!!! | '+pathtofile);

			var oEditor = CKEDITOR.instances.contents ;

			// Check the active editing mode.
			if (oEditor.mode == 'wysiwyg' )
			{
				var foo = serverData.split('.');

				if ( foo[1] == 'jpg' || foo[1] == 'JPG' || foo[1] == 'gif' ||foo[1] == 'GIF' || foo[1] == 'PNG' || foo[1] == 'png'  )
					oEditor.insertHtml( '<img src="/data/<?php echo $this->tank_auth->get_username();?>/'+serverData+'" />' );
				else
					oEditor.insertHtml( '<a href="/data/<?php echo $this->tank_auth->get_username();?>/'+serverData+'" target="_blank">' + foo[0] + ' (' + file.size + 'KB)</a>' );

			}
			else
				alert( 'You must be on WYSIWYG mode!' ) ;
		})
		.bind('uploadComplete', function(event, file){
			// upload has completed, try the next one in the queue
			$(this).swfupload('startUpload');
		})

});

</script>

<style type="text/css" >
#swfupload-control p{ margin:10px 5px; font-size:0.9em; }
#log{ margin:0; padding:0; width:500px;}
#log li{ list-style-position:inside; margin:2px; border:1px solid #ccc; padding:10px; font-size:12px;
	font-family:Arial, Helvetica, sans-serif; color:#333; background:#fff; position:relative;}
#log li .progressbar{ border:1px solid #333; height:5px; background:#fff; }
#log li .progress{ background:#999; width:0%; height:5px; }
#log li p{ margin:0; line-height:18px; }
#log li.success{ border:1px solid #339933; background:#ccf9b9; }
#log li span.cancel{ position:absolute; top:5px; right:5px; width:20px; height:20px;
	background:url('/include/jquery-swfupload/cancel.png') no-repeat; cursor:pointer; }
</style>

<?php
if($this->uri->segment(3) == '0') {
	$titles = "글쓰기";
} else {
	$titles = "답글쓰기";
}
?>
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
<table width="98%" border="1" align="center">
<tr>
	<td width="80" bgcolor="#FFCCCC"><strong>글 제목</strong></td>
	<td align="left" bgcolor="#FFFFFF"><input type="text" value="<?=set_value('subject')?>" size="50" name="subject" /></td>
</tr>
<tr>
	<td bgcolor="#FFCCCC"><strong>글 내용</strong></td>
	<td align="left" bgcolor="#FFFFFF">
    <textarea name='contents' id="contents"><?=(@$ckeditor_value)?@$ckeditor_value:''?></textarea>	</td>
</tr>
<tr>
	<td bgcolor="#FFCCCC"><strong>Tag </strong></td>
	<td align="left" bgcolor="#FFFFFF"><input type="text" value="<?=set_value('tags')?>" size="50" name="tags" /> (예 : 제목, 한국, 홍수)</td>
</tr>
<!--tr>
	<td>파일첨부</td>
	<td align="left"><input type="file" name="userfile" /> (7z|tgz|tar|gz|zip|rar|pdf|ppt|xls|docx|xlsx|pptx)</td>
</tr>
<tr>
	<td colspan="2" align="center"><?php echo validation_errors(); ?><?=@$file_error?></td>
</tr-->
<tr>
<td bgcolor="#FFFFFF"><?php echo validation_errors(); ?></td>
</tr>
<tr><td colspan="2" bgcolor="#FFFFFF">
<div id="swfupload-control">
	<p>모든파일, each having maximum size of 1MB</p>
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
</td></tr>
<tr>
	<td colspan="2" align="center"><input type="submit" value="등록" /></td>
</tr>
</table>
</form>
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