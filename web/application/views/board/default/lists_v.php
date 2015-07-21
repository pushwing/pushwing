<?php
//주소에서 검색어 삭제

$link_url_arr = $this->seg_exp;
//print_r($link_url_arr);
if (in_array('q', $this->seg_exp)) {
	$arr_key = array_keys($this->seg_exp, "q");
	$arr_val = $arr_key[0] + 1;

    if(@$this->seg_exp[$arr_val]){
	    $search_word = urldecode($this->seg_exp[$arr_val]);
    } else {
        $search_word = '검색어 없음';
    }
	$search_url = "q/".$search_word;
	$arr_q = array_search('q', $this->seg_exp);
	array_splice($this->seg_exp, $arr_q,2);
} else {
  	$search_word = '';
	$search_url = '';
}
//주소에서 검색필드 삭제
if (in_array('sfl', $this->seg_exp)) {
	$arr_key1 = array_keys($this->seg_exp, "sfl");
	$arr_val1 = $arr_key1[0] + 1;
	if(@$this->seg_exp[$arr_val1]){
	    $sfl = $this->seg_exp[$arr_val1];
    } else {
        $sfl = 'subject';
    }
	$search_sfl = "/sfl/".$sfl;
	$arr_s = array_search('sfl', $this->seg_exp);
	array_splice($this->seg_exp, $arr_s,2);
} else {
  	$sfl ='';
	$search_sfl = '';
}
//주소에서 말머리 삭제
if (in_array('division', $this->seg_exp)) {
	$arr_key = array_keys($this->seg_exp, "division");
	$arr_val = $arr_key[0] + 1;

	$arr_q = array_search('division', $this->seg_exp);
	array_splice($this->seg_exp, $arr_q,2);
} else {
  	$division = '';
}

//주소에서 page_entry 삭제
if (in_array('page_entry', $this->seg_exp)) {
	$arr_key = array_keys($this->seg_exp, "page_entry");
	$arr_val = $arr_key[0] + 1;

	$arr_q = array_search('page_entry', $this->seg_exp);
	array_splice($this->seg_exp, $arr_q,2);
} else {
  	$page_entry = '20';
}

$cnt = count($this->seg_exp);
$url='';
for ($i=0; $i < $cnt; $i++) {
	$url .= '/'.$this->seg_exp[$i];
	//echo $url."<BR>";
}

$cnt1 = count($link_url_arr);
$link_url='';
for ($ii=0; $ii < $cnt1; $ii++) {
	$link_url .= '/'.$link_url_arr[$ii];
	//echo $link_url."<BR>";
}

//답글용 변수 초기화
$reply_check = '';
?>
<!--script type="text/javascript" src="<?php echo  JS_DIR ?>/jquery.post.js"></script-->
<script>
$(document).ready(function(){
	$("#search_btn").click(function(){
		var sfl_val = $(':input[name="sfl"]').val();
//		var sfl_val = $(":select:option[name=sfl]:selected").val();
		if($("#q").val() == ''){
			alert('검색어를 입력하세요');
			return false;
		} else {
			var act = '<?php echo  $url ?>/q/'+$("#q").val()+'/sfl/'+sfl_val;
			location.href = act;
//			$("#bd_search").attr('action', act).submit();
    	}
	});
	$("#division").change(function(){
		var division=$("select[name=division] option:selected").val();

		if(division !=''){
			var act = '<?php echo  $url ?>/division/'+division;
		} else {
			var act = '<?php echo  $url ?>';
		}
		//alert("=="+act+"==");
//		$("#bd_search").attr('action', act).submit();
		location.href = act;
	});
    $("#page_entry").change(function(){
		var page_entry=$("select[name=page_entry] option:selected").val();

		if(page_entry !=''){
			var act = '<?php echo  $url ?>/page_entry/'+page_entry;
		} else {
			var act = '<?php echo  $url ?>';
		}
		//alert("=="+act+"==");
//		$("#bd_search").attr('action', act).submit();
		location.href = act;
	});
});

function board_search_enter(form, e) {
	if (e.keyCode == 13) $("#search_btn").click();
//    var keycode = window.event.keyCode;
//    if(keycode == 13) $("#search_btn").click();
}
</script>

<div class="right_content">
    

<h2><?php echo  MENU_BOARD_NAME ?></h2>
<?php
	if ( MENU_CATEGORY_WORD != '' ) {
		$c_word = explode("|", MENU_CATEGORY_WORD);
?>
				<select name="division" id="division">
					<option value="">전체보기</option>
<?php
		for ( $index = 0, $max_count = sizeof( $c_word ); $index < $max_count; $index++ ) {
			echo "<option value='$c_word[$index]'";

			if($division == $c_word[$index]) echo " selected";
			echo ">".$c_word[$index]."</option>";
		}
?>
				</select>
<?php
	}
?>

 총 게시물수 : <?php echo $list_total;?>
 <?php $entry_arr = array('5', '10', '20', '100', 'all'); ?>
 <select  name="page_entry" id="page_entry" style="width:65px;">
    <?php for ( $index0 = 0, $max_count0 = sizeof( $entry_arr ); $index0 < $max_count0; $index0++ ) {
			echo '<option value="'.$entry_arr[$index0].'"';

			if($page_entry == $entry_arr[$index0]) echo " selected";
			echo ">".$entry_arr[$index0]."</option>";
		} ?>
</select>
<table id="rounded-corner">
    <thead>
        <tr>
    	    <th scope="col" class="rounded-company">번호</th>
    	    <th scope="col" class="rounded">제&nbsp;&nbsp;&nbsp;목</th>
    	    <th scope="col" class="rounded">글쓴이</th>
    	    <th scope="col" class="rounded">날짜</th>
    	    <th scope="col" class="rounded-q4">조회</th>
    	</tr>
    <tfoot>
        <tr>
            <td colspan="4" class="rounded-foot-left"><em>table footer</em></td>
            <td class="rounded-foot-right">&nbsp;</td>
        </tr>
    </tfoot>
    <tbody>
<?php 
foreach ($list as $lt) 
{
	$vals = mktime(date("H") - 24, date("i"), date("s"), date("m"), date("d"), date("Y"));
	$dates = strtotime($lt['reg_date']);

	if ($dates >= $vals) 
	{
		$new_icon = ' <img src="/images/icon_new.gif">';
	} 
	else 
	{
		$new_icon = ' ';
	}

	if ($lt['hit'] > 100) 
	{
		$bl1 = '<b>';
		$bl2 = '</b>';
	} 
	else 
	{
		$bl1 = '';
		$bl2 = '';
	}

	if ($lt['voted_count'] > 30) 
	{
		$recom_icon = ' <img src="/images/recom_new.gif">';
	} 
	else 
	{
		$recom_icon = ' ';
	}

    $bubble_title = strip_tags(strcut_utf8($lt['contents'], 200));

    if($lt['is_delete'] == 'Y' && $lt['is_list'] == 'Y') 
    { 
?>
	<tr >
		<td><?php if($reply_check != $lt['board_pid']) { echo $lt['board_pid'];  } else { echo "-"; }?></td>
    	<td colspan="5">답글이 달린 삭제된 글입니다.</td>
  	</tr>
<?php 
    } 
    else if( $lt['is_delete'] == 'Y' && $lt['is_list'] == 'N' ) 
    { 
?>

<?php 
    } 
    else if( $lt['is_delete'] == 'N' && $lt['is_list'] == 'Y' ) 
    { 
?>
 	<tr>
    	<td><?php if($reply_check != $lt['board_pid']) { echo $lt['board_pid'];  } else { echo "-"; }?></td>
     	<td>
		<a href="/admin/<?php echo  $this->uri->segment(2) ?>/view/<?php echo  $this->uri->segment(4) ?>/<?php echo  $lt['id'] ?>/page/<?php echo $page_account?>/<?php echo $search_url?><?php echo $search_sfl?>" title="<?php echo  $bubble_title ?>"><?php echo  $bl1 ?><?php echo  strcut_utf8(strip_tags($lt['subject']), 30) ?><?php echo  $bl2 ?></a></a>
		</td>
		<td ><?php  if($lt['nickname']) { echo $lt['nickname']; } else { echo $lt['user_name']; } ?></td>
        <td><?php echo  substr($lt['reg_date'], 0, 10) ?></td>
        <td><?php echo  $lt['hit'] ?></td>
  	</tr>
<?php 
	} 
    $reply_check = $lt['board_pid'];
} 
?>
    </table>
    
    <a class="bt_green" href="/admin/<?php echo  $this->uri->segment(2) ?>/write/0"><span class="bt_green_lft"></span><strong>글쓰기</strong><span class="bt_green_r"></span></a>
    <br />
        <!-- 페이지 -->
    <div class="pagination"><?php echo  $pagination_links ?></div>

    <!-- 검색 -->
	<!--
    <div class="board_search">
    <form id="bd_search" method="post" onsubmit="javascript:return false;">
    <?php
		$sfl_arr = array('subject'=>'제목', 'contents'=>'내용', 'all'=>'제목+내용', 'user_id'=>'회원아이디', 'users.nickname'=>'닉네임');
	?>
        <select name="sfl">
        	<?php 
        	while (list($key, $value) = each($sfl_arr)) 
        	{
        		if ($sfl == $key) 
        		{
        			$chk = ' selected';
                } 
                else 
                {
                  	$chk = '';
                }
			?>
        	<option value="<?php echo $key?>" <?php echo $chk?>><?php echo $value?></option>
            <?php 
			} 
			?>

        </select>
        <input name="q" id="q" class="stx" maxlength="15" value="<?php echo $search_word?>" onkeypress="board_search_enter(document.q, event);">
        <input type="button" id="search_btn" value="검색">
    </form>
    </div>
	-->

</div><!-- end of right content-->
</div>   <!--end of center content -->

<div class="clear"></div>
</div> <!--end of main content-->