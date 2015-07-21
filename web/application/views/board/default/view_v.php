<?php
//$board_skin_path=VIEW_ROOT."/board/views/".MENU_SKIN;
$img_size = 600;
$reply_img_size = 600;
$searcht = array('@<script[^>]*?>.*?</script>@si',  // Strip out javascript
       '@<style[^>]*?>.*?</style>@siU'    // Strip style tags properly
);

?>
<script>
$(function(){
	//기본 댓글

	$('#add_comment_btn').click(function(){

		var oEditor = CKEDITOR.instances.contents;
		var values = oEditor.getData();


		if (values == '') {
			alert('내용을 입력하세요!');
			return false;
		} else {
			$.ajax({
				type: "POST",
				url: "/action/index",
				data: {
					"url1": "/<?php echo $this->uri->segment(1)?>/reply_edit/",
					"url2": "/page/<?php echo $page_account?>",
					"contents":values,
					"no": '<?php echo $this->uri->segment(4);?>',
					"table": '<?php echo  MENU_ID ?>',
					"resize": '<?php echo $img_size?>',
					"wname":"name"
				},
				complete: function(r){
					$('#comment_list').html(r.responseText);
					oEditor.setData('');
				}
			})
		}
	});


 	// Ajax Submission 댓글의 댓글 쓰기 액션
	$(".add_comment_comment_btn").each(function(){
		$(this).click(function(){
			//alert('aa');
			var values = $(this).parent().children('.add_comment_comment').val();
			var comno = $(this).parent().children('.comment_no').val();
			var comor = $(this).parent().children('.comment_order_val').val();
			var boid = $(this).parent().children('.board_id').val();

			if (values == '') {
				alert('내용을 입력하세요!');
				return false;
			} else {
				$.ajax({
					type: "POST",
					url: "/action/comment2",
					data: {
						"url1": "/<?php echo $this->uri->segment(1)?>/reply_edit/",
						"url2": "/page/<?php echo $page_account?>",
						"contents":values,
						"comment_no": comno,
						"comment_order": comor,
						"board_id": boid,
						"table": '<?php echo  MENU_ID ?>',
						"resize": '<?php echo $img_size?>',
						"wname":"name"
					},
					complete: function(r){
						$('#comment_list').html(r.responseText);
						$(this).parent().children('.add_comment_comment').val('');
					}
				})
			}

		});
	});
});

function reply_edit(r_no, url1, url2){
	$(function(){
		jQuery.FrameDialog
		.create({
			url: url1+r_no+url2,
			title: '댓글 수정',
			width : 800,
			height : 450,
			draggable : false,
			resizable : false,
			buttons: { "닫기": function() { $(this).dialog("close"); } }
		})
	});
}

function comment_delete(board_id, row_no, views_no, comment_order){
	$(function(){
		$.ajax({
 			type: "POST",
 			url: "/action/delete",
 			data: {
 				"board_id" : board_id,
 				"row_no" : row_no,
 				"views_no" : views_no,
				"comment_order" : comment_order
 			},
 			success : function(data,status){
 				if(data == "1")
 					$('#row_num_'+row_no).remove();
 				else
 					alert(data);
 			}
		});
	});
}

/**
*	SNS 전송 스크립트
*
* 게시물 본문 내용 또는 첨부된 이미지를 트위터와 페이스북으로 전송합니다.
*
* @author	Choi Kwangmyung <mycastor@gmailcom>
* @since		2011. 07. 06
*/
/* 시작 */
var img_list = null; // 이미지 목록을 저장할 전역 변수
var img_pos = 0; // 이미지 선택 레이어에서 보여주는 이미지의 순서를 저장하는 변수
var img_event_set = false; // 이벤트 리스너가 중복되어 등록되는 것을 방지하기 위한 체크 플러그 변수

// 게시물을 sns으로 전송
function post_to_sns(type) {
	var no = '<?php echo $this->uri->segment(4);?>';

	if (type == 'twitter' && confirm('현재글을 트위터로 전송하시겠습니까?')) {
		window.open('/<?php echo  $this->uri->segment(1);?>/sns/' + type + '/' + no, '', null);
	} else if (type == 'facebook' && confirm('현재글을 페이스북으로 전송하시겠습니까?')) {
		$.ajax({
			url: '/action/imgs/' + no,
			dataType: 'json',
			success: function (data)
			{
				img_list = data;

				if (data.length > 1) {
					if (img_event_set == false) {
						$('#facebook_choice_img > .button > #prev').click(function () {
							if (img_pos > 0) img_pos--;
							else img_pos = img_list.length - 1;

							preview_img(img_pos);
						});

						$('#facebook_choice_img > .button > #next').click(function () {
							if (img_pos < img_list.length - 1) img_pos++;
							else img_pos = 0;

							preview_img(img_pos);
						});

						$('#facebook_choice_img > .thumb').click(function () {
							window.open('/<?php echo  $this->uri->segment(1);?>/sns/' + type + '/' + no + '/' + img_pos, '', null);
							$('#facebook_choice_img').hide();
							img_pos = 0;
						});

						img_event_set = true;
					}

					preview_img(img_pos);
				} else if (data.length > 0) {
					window.open('/<?php echo  $this->uri->segment(1);?>/sns/' + type + '/' + no + '/0', '', null);
				} else {
					window.open('/<?php echo  $this->uri->segment(1);?>/sns/' + type + '/' + no, '', null);
				}
			}
		});
	}
}

// 게시물 댓글을 sns으로 전송
function reply_to_sns(type, no) {
	if (type == 'twitter' && confirm('현재 댓글을 트위터로 전송하시겠습니까?')) {
		window.open('/<?php echo  $this->uri->segment(1);?>/sns_comment/' + type + '/' + no, '', null);
	} else if (type == 'facebook' && confirm('현재 댓글을 페이스북으로 전송하시겠습니까?')) {
		window.open('/<?php echo  $this->uri->segment(1);?>/sns_comment/' + type + '/' + no, '', null);
	}
}

// 이미지 선택을 위한 미리보기 레이어 팝업 보여줌
function preview_img(pos) {
	$('#facebook_choice_img').show();
	$('#facebook_choice_img > .thumb').html('<img src="' + img_list[pos] + '" />');

	var img = $('#facebook_choice_img > .thumb > img');
	var img_w = img.width();
	var img_h = img.height();

	if (img_w > img_h) {
		var ratio = 300 / img_w;
	} else {
		var ratio = 300 / img_h;
	}

	img.css({'width': (img_w * ratio) + 'px', 'height': (img_h * ratio) + 'px'});

	var offset = $('#facebook_post').offset();
	var position = {top: offset.top - $('#facebook_choice_img').height() - $('#facebook_post').height(), left: offset.left - ($('#facebook_choice_img').width() - $('#facebook_post').width())};

	$('#facebook_choice_img').css({'top': position.top + 'px', 'left': position.left + 'px'});
}
/* 끝 */
// 이미지 뷰어를 출력 - by Choi Kwangmyung at 2011. 07. 06

/* 기존 뷰어 출력 방법 - 주석처리
function show_viewer(no) {
	$.ajax({
		url: '/action/imgs/' + no,
		dataType: 'json',
		success: function (data)
		{
			if (data != null && data.length > 0) {
				$.viewer.array_init(data, {overlay_disable_container: '#board_list', overlay_opacity: 0.1, overlay_color: '#fff'});
				$.viewer.show();
			}
		}
	});
}
*/
</script>

<div class="right_content">
    
<h2><?php echo  MENU_BOARD_NAME ?></h2>

<table id="rounded-corner">
    <thead>
        <tr>
            <th scope="col" class="rounded-company" colspan="2"><b>제목</b> : <?php echo $views['subject']?>&nbsp;&nbsp;&nbsp;<b>작성자</b> : <?php echo ($views['nickname'])?$views['nickname']:$views['user_name']?></th>
            <th scope="col" class="rounded-q4" align="right" width="300px">조회수 : <span class="view_num"><?php echo number_format($views['hit'])?></span>,
    등록일 : <span class="date"><?php echo  $views['reg_date']?></span>
            </th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <td class="rounded-foot-left"><em>table footer</em></td>
            <td></td>
            <td class="rounded-foot-right">&nbsp;</td>
        </tr>
    </tfoot>
    <tbody>
        <tr>
    	<td colspan="3">
		<?php
        $views['contents'] = $this->board_m->auto_link($views['contents']);
        $enter = 'always">'.chr(13) . chr(10).'<';
        $content=str_replace($enter,'always"><',$views['contents']);

        //echo $content."---";
        //배열에 담기
        $addr = preg_split('/<div style="page-break-after: always"><\/div>/', $content, -1, PREG_SPLIT_OFFSET_CAPTURE);
        $arr_count = count($addr);

        if ($arr_count > 1)
        {
            //echo $arr_count;
            //var_dump($addr);
            //echo "<Br><Br>".$views['contents']."<Br><Br>";
        ?>
        <script src="<?php echo JS_DIR; ?>/jquery-1.5.1.js"></script>
    	<script src="<?php echo JS_DIR; ?>/ui/jquery.ui.core.js"></script>
    	<script src="<?php echo JS_DIR; ?>/ui/jquery.ui.widget.js"></script>
    	<script src="<?php echo JS_DIR; ?>/ui/jquery.ui.tabs.js"></script>
    	<script>
    	$(function() {
    		$( "#tabs" ).tabs();
    	});
    	</script>
        <div id="tabs">
    	<ul>
        <?php for ($i=0;$i < $arr_count ;$i++) {
            $j=$i+1;
        ?>
        	<li><a href="#page-<?php echo $j;?>">Page <?php echo $j;?></a></li>
        <?php } ?>
    	</ul>
        <?php for ($ii=0;$ii < $arr_count ;$ii++) {
            $jj=$ii+1;
        ?>
        <div id="page-<?php echo $jj;?>">
    		<p><?php echo $addr[$ii][0]?></p>
    	</div>
        <?php } ?>
    	</div>

        <?php

        }
        else
        { ?>
        <!--내용 출력-->
		<!--div id="contents"-->
		<div id="view_contents">
        <?php echo $views['contents']?>
		</div>
		<!--/div-->
        <?php
        }
        ?>

		<!--  예전소스 사용안함 file_list -->
		<?php if($files_cnt > 0) { ?>
		<br />
		<div id="file_list">
		<?php foreach ( $files as $fs ) { ?>
       	<a href="/<?php echo $this->uri->segment(1)?>/download/<?php echo $views['no']?>/<?php echo $fs['no']?>"><?php echo ($fs['original_name'])?$fs['original_name']:$fs['file_name']?></a><br>
		<?php } ?>
		</div>
		<?php } ?>
		<!--// file_list-->
    	</td>
    </tr>
    </tbody>
    </table>

<div class="board_button">
	<div style="float:left;" id="gBtn7">
	</div>
	<div style="float:right;" id="gBtn7">
        <a class="bt_green" href="/admin/<?php echo $this->uri->segment(2)?>/lists/0/page/<?php echo $page_account?>"><span class="bt_green_lft"></span><strong>목록</strong><span class="bt_green_r"></span></a>
		
<?php

    if ($this->tank_auth->get_user_id() == $views['user_id']) 
    { 
?>
		<a href="/admin/<?php echo $this->uri->segment(2)?>/edit/0/<?php echo $views['id']?>/page/<?php echo $page_account?>" id="btn_list"><span>&nbsp;&nbsp;수정&nbsp;&nbsp;</span></a>
<?php 
    } 
    
	if ($this->tank_auth->get_user_id() == $views['user_id']) 
	{ 
?>
        <a href="/admin/<?php echo $this->uri->segment(2)?>/delete/0/<?php echo $views['id']?>/page/<?php echo $page_account?>" id="btn_list"><span>&nbsp;&nbsp;삭제&nbsp;&nbsp;</span></a>
<?php 
    }

    if ( $this->session->userdata('auth_code') == '9' ) 
    { 
?>
        <a href="/admin/<?php echo $this->uri->segment(2)?>/write/0" id="btn_list" ><span>&nbsp;&nbsp;글쓰기&nbsp;&nbsp;</span></a>
<?php 
        if ($views['reply_order'] == '0') 
        { 
?>
        <a href="/admin/<?php echo $this->uri->segment(2)?>/write/<?php echo $views['board_pid'];?>" id="btn_list" ><span>&nbsp;&nbsp;답글쓰기&nbsp;&nbsp;</span></a>
<?php 
        }
        
    }
?>

	 </div>
</div>
<BR>
<!--덧글-->
<!--
<div id="comment_list">
<?php
$comments_cnt = count($comments);
if ( $comments_cnt > 0 ) { ?>
<table border=0 cellpadding=0 cellspacing=0 width="600" style="margin-top:15px;">
<tr>
	<td height=1 colspan=2 bgcolor="#dddddd"><td>
</tr>
<?php
//댓글 체크 초기화
$comment_check = '';
foreach ($comments as $row) {
	if($row['is_list'] == 'Y' and $row['is_delete'] == 'N') {

?>
<tr id="row_num_<?php echo $row['no']?>">
	<td>
		<table border=0 cellpadding=0 cellspacing=0 width="100%">
		<tr>
			<td height=5 colspan=2></td>
		</tr>
		<tr id="row_2_<?php echo $row['no']?>">
			
			<td valign=top style="padding-left:10px;">
				<div style="height:28px; line-height:20px;">
					<div style="float:left; margin:0px 0 0 2px;">
                    <?php
					$order_cnt = strlen($row['comment_order']);
					$order_cnt = $order_cnt / 3;
                    //댓글 depth 표시
					for($oo=1;$oo<$order_cnt;$oo++) {
						echo "+";
					}

					//comment_id asc, comment_order asc, no asc
                    ?>
					<strong><a href="#" name="row_num_<?php echo $row['no']?>"><?php echo ($row['nickname'])?$row['nickname']:$row['username']?></a></strong>
					<span style="color:#888888; font-size:11px;"><?php echo $row['reg_date']?></span> - -<?php echo $row['comment_id']?>-<?php echo $row['comment_order']?>-<?php echo $row['no']?>-<?php echo $row['board_id']?>--
                    <div id="add_comment2_btn" style="cursor:hand;" rel="<?php echo $row['no']?>">댓글 <a href="javascript:reply_to_sns('twitter', <?php echo $row['no'];?>);">트위터 전송</a> <a href="javascript:reply_to_sns('facebook', <?php echo $row['no'];?>);">페이스북 전송</a></div>
					</div>
					<div style="float:right;">
					&nbsp;<span style="color:#B2B2B2; font-size:11px;"><?php echo ($this->session->userdata('auth_code') >= '15')?$row['ip']:'' ?></span>&nbsp;
					<?php if( $row['user_no'] == $this->tank_auth->get_user_id() ) { ?>
					<a href="javascript:reply_edit('<?php echo $row['no']?>','/<?php echo $this->uri->segment(1)?>/reply_edit/', '/page/<?php echo $page_account?>');">수정</a> <a href="javascript:comment_delete('<?php echo MENU_ID?>','<?php echo $row['no']?>','<?php echo $views['no']?>', '<?php echo $row['comment_order']?>');" onclick="return confirm(&quot;삭제하시겠습니까?&quot;)">삭제</a>
					<?php } ?>
					</div>
				</div>
				<div style='line-height:18px; padding:7px; word-break:break-all; overflow:hidden; clear:both; text-align:left; '>
					<?php $row['contents'] = $this->board_m->auto_link($row['contents']); ?>
					<?php echo $row['contents']?>
				</div>
                <div id="add_comment2_layer" style='display:none'>
				<form name="add_comment2" id="add_comment2" method="post" action="">
				<input type="hidden" name="comment_no" class="comment_no" value="<?php echo $row['comment_id']?>">
				<input type="hidden" name="board_id" class="board_id" value="<?php echo $row['board_id']?>">
				<input type="hidden" name="comment_order_val" class="comment_order_val" value="<?php echo $row['comment_order']?>">
                <textarea name='add_comment_comment' class="add_comment_comment"></textarea>
            	<input type="button" value="등록" class="add_comment_comment_btn" />
            	</form>
				</div>
			</td>
		</tr>
		<tr>
			<td height=5 colspan=2></td>
		</tr>
		<tr>
			<td height=1 colspan=2 bgcolor="#dddddd"><td>
		</tr>
		</table>
	</td>
</tr>
<?php
	}
	else
	{
?>
<tr id="row_num_<?php echo $row['no']?>">
	<td>
		<table border=0 cellpadding=0 cellspacing=0 width="100%">
		<tr>
			<td height=5 colspan=2></td>
		</tr>
		<tr id="row_2_<?php echo $row['no']?>">

			<td valign=top style="padding-left:10px;">
				<div style="height:28px; line-height:20px;">
					<div style="float:left; margin:0px 0 0 2px;">
                    <?php
					$order_cnt = strlen($row['comment_order']);
					//댓글 depth 표시
					for($oo=1;$oo<$order_cnt;$oo++) {
						echo "+";
					}

					//comment_id asc, comment_order asc, no asc
                    ?>
					</div>
				</div>
				<div style='line-height:18px; padding:7px; word-break:break-all; overflow:hidden; clear:both; text-align:left; '>
					댓글이 달린 삭제된 댓글입니다.
				</div>

			</td>
		</tr>
		<tr>
			<td height=5 colspan=2></td>
		</tr>
		<tr>
			<td height=1 colspan=2 bgcolor="#dddddd"><td>
		</tr>
		</table>
	</td>
</tr>
<?php
	}
    $comment_check = '';//$row['comment_no'];
} ?>
</table>
</div>
<?php } ?>
</div>
	<?php if(($this->session->userdata('auth_code') == '9' ) or ($this->tank_auth->is_logged_in()) or ($comment_perm == 1) ) {
	?>
	<div id="comment_add">
	<form name="add_comment" id="add_comment" method="post" action="">

    <textarea name='contents' id="contents"></textarea>

	<input type="button" value="등록" id="add_comment_btn" />
	</form>
	</div>
    <?php
    //툴바, textarea name, 에디터 폭, 에디터 높이
    //툴바를 빈칸으로 하면 FULL 툴바가 나옵니다.
    //현재 선언해놓은 것은 reply와 basic인데 입맛에 맞게 선언하여 사용하면 됩니다.
    echo form_ckeditor(array(
        'toolbar'        => '',
        'id'              => 'contents',
        'width'           => '600',
        'height'          => '200'
    ));
    ?>
	<?php } ?>


-->


</div><!-- end of right content-->
</div>   <!--end of center content -->

<div class="clear"></div>
</div> <!--end of main content-->