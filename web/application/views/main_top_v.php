<html>
<head>
    <title><?php echo isset($blog) ? $blog['subject'] : "Pushwing, 모바일앱 개발없이 푸시 알림을 보내세요";?></title>
    <meta name="description" content="모바일앱이 없는 웹사이트에서 보내는 푸시를 받아줍니다. 모바일앱 개발없이 푸시 알림을 보내세요. 여러 웹사이트들에 쓴 글의 반응을 하나의 앱으로 관리하세요.">
    <meta name="keywords" content="푸시윙, 푸시, 푸시 알림, pushwing, push notification, gcm, notification center, mobile, free, 무료">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="utf-8">
    <script type="text/javascript" src="<?php echo JS_DIR;?>/jquery-1.6.1.min.js"></script>
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

<?php echo validation_errors(); ?>

<div id="navbar-top">
    <nav class="navbar navbar-default" role="navigation">
        <div class="container">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
            <span class="sr-only">Menus</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="/"><img src="<?php echo base_url('/img/logo.png');?>" height="36px"></a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                <li><a href="main#">Home</a></li>
                <li><a href="/main/docs">Documentation</a></li>
                <li><a href="/main/partners">Partners</a></li>
                <li><a href="/main/blog_list">Blog</a></li>
                <?php if($this->agent->is_mobile()):?>
                <li><a href="/main/download">Download</a></li>
                <?php endif;?>
                <li><a href="/main/communication">Contact</a></li>
            </ul>
        </div><!-- /.navbar-collapse -->
        </div>
    </nav>
</div>