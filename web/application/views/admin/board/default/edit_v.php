<script>
$(function(){
 	$("a").each(function(){
		if($(this).attr("id")=="file_delete") {
			$('#file_delete').click(function(){
				var span_no = $(this).attr("ref");
				var hrefs = $(this).attr("raf");
				//alert(hrefs);

				var cfm = confirm('삭제하시겠습니까?');
				if(cfm) {
					$.ajax({
						type: "POST",
						url: hrefs,
						data:{
							"module_no":'<?=$this->uri->segment(3)?>',
							"table":'<?=MENU_BOARD_NAME_EN?>',
							"file_table":'files'
							},


						success : function(data,status){
							if(data == "1")
							{
								alert('삭제 되었습니다.');
								$('#file_row_'+span_no).hide();
							}
							else {
								alert('삭제 실패 하였습니다.');
							}
						}

					})
				}

			});
		}
	});
});
</script>

<br>
<form action="" method="post" name="edit_post" enctype="multipart/form-data">
<?php
$select_cate = explode("|", MENU_CATEGORY_WORD);
?>
<select name="category_word">
<?php 
foreach ($select_cate as $scate) 
{
	if ($scate == $views['division'])
	{
	    $selects = " selected ";
	}
	else
	{
	    $selects = "";
	}
?>
<option value="<?php echo $scate; ?>" <?php echo $selects; ?>><?php echo $scate; ?></option>
<?php 
} 
?>
</select>
<table width="95%" >
<tr>
	<td width="80">글 제목</td>
	<td><input type="text" value="<?=$views['subject']?>" size="50" name="subject" />
	</td>
</tr>
<tr>
	<td>글 내용</td>
	<td>
    <textarea name='contents'><?php echo $views['contents'];?></textarea>
	</td>
</tr>
<tr>
	<td>Tag</td>
	<td><input type="text" value="<?=$tags?>" size="50" name="tags" /> (예 : 제목, 한국, 홍수)</td>
</tr>
<!--tr>
	<td>파일첨부</td>
	<td><input type="file" name="userfile" /> (7z|tgz|tar|gz|zip|rar|pdf|ppt|xls|docx|xlsx|pptx)</td>
</tr-->
<!-- file_list
<? if($files_cnt > 0) { ?>
<tr>
	<td></td>
	<td height="25">
	<?
	$ss=0;
	foreach ( $files as $fs ) { ?>
	<span id="file_row_<?=$ss?>">
	<a href="/data/files/<?=$fs['file_name']?>"><?=$fs['original_name']?></a>&nbsp; - <a href="#" raf="/action/file_delete/<?=$fs['no']?>" ref="<?=$ss?>" id="file_delete">파일 삭제</a><br>
	</span>
	<?
	$ss++;
	} ?>
	</td>
</tr>
<? } ?>
 file_list-->
<tr>
	<td colspan="2" align="center"><?php echo validation_errors(); ?><?=@$file_error?></td>
</tr>
<tr>
	<td colspan="2" align="center"><input type="submit" value="수정" /></td>
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