<html>
<head>
<title>Contact Us</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- Bootstrap -->
<link href="/include/bootstrap-3/dist/css/bootstrap.min.css" rel="stylesheet" media="screen">

<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!--[if lt IE 9]>
<script src="/include/bootstrap-3/assets/js/html5shiv.js"></script>
<script src="/include/bootstrap-3/assets/js/respond.min.js"></script>
<![endif]-->

<link href="/include/css/contact.css" rel="stylesheet" media="screen">
</head>
<body>
<h3>푸시 보내기</h3>
<p>성공시 대기중인 푸시에 추가됩니다.</p>
<form method="post" action="/index.php/osy/test_send_push">
    hp
    <input type="text" name="hp">
    <br />
    client_id
    <input type="text" name="client_id" value="3">
    <br />
    subject
    <input type="text" name="subject">
    <br />
    contents
    <textarea name="contents"></textarea>
    <br />
    url
    <input type="text" name="url">
    <br />
    <input type="submit">
</form>

<div>
    <h3>대기중인 푸시</h3>
    <table class="table">
        <tr>
            <td>hp</td>
            <td>client_id</td>
            <td>subject</td>
            <td>contents</td>
            <td>timestamp</td>
        </tr>
        <?php foreach($push_wait as $p):?>
        <tr>
            <td><?php echo $p['hp'];?></td>
            <td><?php echo $p['client_id'];?></td>
            <td><?php echo $p['subject'];?></td>
            <td><?php echo $p['contents'];?></td>
            <td><?php echo $p['timestamp'];?></td>
        </tr>
        <?php endforeach;?>
    </table>
</div>

<div>
    <h3>대기중인 푸시 처리</h3>
    <a href="/push/send_all" class="btn">send!</a>
</div>
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
<script src="//code.jquery.com/jquery.js"></script>
<!-- Include all compiled plugins (below), or include individual files as needed -->
<script src="/include/bootstrap-3/dist/js/bootstrap.min.js"></script>
</body>
</html>