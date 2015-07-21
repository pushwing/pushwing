<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
<meta name="author" content="author"/>
<link href="<?=CSS_DIR?>/jquery-ui-1.7.1.custom.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="<?=JS_DIR?>/common.js"></script>
<script type="text/javascript" src="<?= JS_DIR ?>/jquery-1.3.2.min.js"></script>
<script type="text/javascript"  src="<?=JS_DIR?>/jquery-ui-1.7.1.custom.min.js"></script>
<script type="text/javascript"  src="<?=JS_DIR?>/jquery.framedialog.js"></script>
<script type="text/javascript" src="<?=JS_DIR?>/jquery.jScale.js"></script>

<title>커뮤니티</title>
</head>
<body>
<div style="text-align:center;">
<form action="" method="post" name="edit_post" enctype="multipart/form-data">
<table width="100%" align="center">
<tr>
	<td>
	
    <textarea name='contents' cols="80" rows="10"><?php echo $views[0]['contents'];?></textarea>
    
	</td>
</tr>


<tr>
	<td align="center"><?php echo validation_errors(); ?></td>
</tr>
<tr>
	<td align="center"><input type="submit" value="수정" /></td>
</tr>
</table>
</form>
</div>
</body>
</html>