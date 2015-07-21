<script type="text/javascript">
// <![CDATA[
$(document).ready(function () {
	// 체크박스 전체선택/취소
	$('#btn_check_all').toggle(
		function () {
			$('.right_content table:last tbody :checkbox').attr('checked', true);
		},
		function () {
			$('.right_content table:last tbody :checkbox').attr('checked', false);
		}
	);

	// 삭제
	$('#btn_delete').click(function () {
		var count = $('.right_content table:last tbody :checkbox:checked').size();

		if (count < 1) {
			alert('삭제할 광고주를 선택해주세요.');
			return;
		} else {
			if (confirm('삭제하시겠습니까?')) {
				var element = $('.right_content table:last tbody :checkbox:checked');
				var checked = new Array();

				for (var i = 0; i < count; i++) {
					checked.push(element.eq(i).val());
				}

				$.ajax({
					type: 'POST',
					dataType: 'json',
					url: '/admin/business/delete_exec/1?profiler=0',
					data: 'checked=' + checked,
					success: function (response) {
						if (response.result == 'success') {
							alert('선택한 광고주를 삭제하였습니다.');
							location.reload();
						} else {
							alert('선택한 광고주를 삭제하지 못했습니다.');
						}
					}
				});
			}
		}
	});

	// 검색
	$('#btn_search').click(function () {
		var scope = $(':input[name="scope"] > option:selected').val();
		var keyword = $(':input[name="keyword"]').val();

		if (scope == '')
		{
		    alert('검색조건을 선택하세요');
		    return false;
		}
		else if(keyword == '')
		{
		    alert('검색어를 입력하세요');
		    $("#keyword").focus();
		    return false;
		}
		else
		{
		    var querystring = {
                scope: scope,
                keyword: keyword
            };

            var url = '/admin/client/contact/0/';
            location.href = url + '?' + $.param(querystring);
		}
	});
});
// ]]>
</script>
<div class="right_content">
	<h2>문의, 파트너 신청 관리</h2>
	<br />
	<table id="rounded-corner">
		<tr>
		    <td>검색조건</td>
			<td>
				<select name="scope">
					<option value="">선택</option>
					<option value="web_url" <?php echo $condition['scope'] == 'web_url' ? 'selected' : '';?>>사이트주소</option>
					<option value="name" <?php echo $condition['scope'] == 'name' ? 'selected' : '';?>>담당자명</option>
                    <option value="content" <?php echo $condition['scope'] == 'content' ? 'selected' : '';?>>내용</option>
				</select>
				<input type="text" id="keyword" name="keyword" value="<?php echo $condition['keyword'];?>" />
			</td>
			<td><a id="btn_search" class="bt_blue" href="javascript:void(0);"><span class="bt_blue_lft"></span><strong>검색</strong><span class="bt_blue_r"></span></a></td>
		</tr>
	</table>


	<br />
	<br />
	전체 : <?php echo $list_count;?>
	<table id="rounded-corner">
		<thead>
			<tr>
				<th scope="col" class="rounded-company">No</th>
                <th scope="col" class="rounded" align="center">구분</th>
				<th scope="col" class="rounded" align="center">담당자명</th>
				<th scope="col" class="rounded" align="center">사이트 주소</th>
				<th scope="col" class="rounded" align="center">이메일</th>
                <th scope="col" class="rounded" align="center">등록일</th>
				<th scope="col" class="rounded" align="center">이메일발송일</th>
                <th scope="col" class="rounded-q4" align="center">클라이언트체결</th>
			</tr>
		</thead>
		<tbody>
<?php
if($list_count > 0)
{
    foreach ($lists as $row)
    {
?>
			<tr>
				<td align="center"><?php echo $row['id'];?></td>
                <td align="center"><?php echo ($row['content'] != '')? '문의':'파트너 신청';?></td>
				<td align="center"><a href="/admin/client/register/0/<?php echo $row['id'];?>"><?php echo $row['name'];
                        ?></a></td>
				<td align="center"><a href="<?php echo prep_url($row['web_url']);?>" target="_blank"><?php echo $row['web_url'];?></a></td>
				<td align="center"><?php echo $row['email'];?></td>
				<td align="center"><?php echo $row['reg_date']?></td>
                <td align="center"><?php echo $row['email_date']?></td>
                <td align="center"><?php echo $row['check_date']?></td>

			</tr>
<?php
    }
}
else
{
?>
            <tr>
                <td align="center" colspan="5">검색결과가 없습니다.</td>
            </tr>
<?php
}
?>
		</tbody>
	</table>
    <!--<a class="bt_green" href="/admin/client/register/0"><span class="bt_green_lft"></span><strong>클라이언트 등록</strong><span class="bt_green_r"></span></a>

    <a id="btn_delete" class="bt_green" href="javascript:void(0);"><span
            class="bt_green_lft"></span><strong>삭제</strong><span class="bt_green_r"></span></a>

    <a id="btn_check_all" class="bt_red" href="javascript:void(0);"><span
    class="bt_red_lft"></span><strong>전체선택/취소</strong><span class="bt_red_r"></span></a>-->
	<div class="pagination"><?php echo  $pagination_links ?></div>
</div><!-- end of right content-->
</div>   <!--end of center content -->

<div class="clear"></div>
</div> <!--end of main content-->