<?php
$menu_archi = array(

	// First 1Depth Menu
	array(
		"idx" => "0",	"name" => "클라이언트 관리",	"title" => "클라이언트 관리",	"href" => "/admin/client/index/0",
        "sub_menu" => array(
            array("idx" => "0", "name" => "클라이언트 관리",  "title" => "클라이언트 관리", "href" => "/admin/client/index/0",   "sub_menu" => array()),
            array("idx" => "1", "name" => "문의, 파트너 관리",  "title" => "문의, 파트너 관리", "href" => "/admin/client/contact/0",   "sub_menu" => array()),
            array("idx" => "2", "name" => "공지사항",  "title" => "공지사항", "href" => "/admin/notice/lists/0",    "sub_menu" => array()),
            array("idx" => "3", "name" => "미사용 클라이언트",  "title" => "미사용 클라이언트", "href" => "/admin/client/nostart/0",    "sub_menu" => array())
        )
	),

    array(
        "idx" => "1",   "name" => "보고서 관리", "title" => "보고서 관리",    "href" => "/admin/report/main/1",   "sub_menu" => array(
            array("idx" => "1", "name" => "보고서 관리",  "title" => "보고서 관리", "href" => "/admin/report/main/1",    "sub_menu" => array())
        )
    ),

    array(
        "idx" => "2",   "name" => "도움말", "title" => "도움말",    "href" => "/admin/manage/help/2",   "sub_menu" => array(
            array("idx" => "0", "name" => "도움말",  "title" => "도움말", "href" => "/admin/manage/help/2",   "sub_menu" => array())
        )
    )
);

//로고처리
$logo = 'adv';
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>꽃살</title>

<link rel="stylesheet" type="text/css" media="all" href="<?php echo JS_DIR;?>/ui-lightness/jquery-ui-1.8.14.custom.css" />
<script type="text/javascript" src="<?php echo JS_DIR;?>/jquery-1.6.1.min.js"></script>
<script type="text/javascript" src="<?php echo JS_DIR;?>/ui/jquery-ui-1.8.14.custom.js"></script>
<script type="text/javascript" src="<?php echo JS_DIR;?>/common.js"></script>

<link rel="stylesheet" type="text/css" href="<?php echo INCLUDE_DIR;?>/in_admin/style.css" />
<script type="text/javascript" src="<?php echo INCLUDE_DIR;?>/in_admin/ddaccordion.js"></script>
<script type="text/javascript">
ddaccordion.init({
	headerclass: "submenuheader", //Shared CSS class name of headers group
	contentclass: "submenu", //Shared CSS class name of contents group
	revealtype: "click", //Reveal content when user clicks or onmouseover the header? Valid value: "click", "clickgo", or "mouseover"
	mouseoverdelay: 200, //if revealtype="mouseover", set delay in milliseconds before header expands onMouseover
	collapseprev: true, //Collapse previous content (so only one open at any time)? true/false
	defaultexpanded: [], //index of content(s) open by default [index1, index2, etc] [] denotes no content
	onemustopen: false, //Specify whether at least one header should be open always (so never all headers closed)
	animatedefault: false, //Should contents open by default be animated into view?
	persiststate: true, //persist state of opened contents within browser session?
	toggleclass: ["", ""], //Two CSS classes to be applied to the header when it's collapsed and expanded, respectively ["class1", "class2"]
	togglehtml: ["suffix", "<img src='<?php echo IMG_DIR;?>/plus.gif' class='statusicon' />", "<img src='<?php echo IMG_DIR;?>/minus.gif' class='statusicon' />"], //Additional HTML added to the header when it's collapsed and expanded, respectively  ["position", "html1", "html2"] (see docs)
	animatespeed: "fast", //speed of animation: integer in milliseconds (ie: 200), or keywords "fast", "normal", or "slow"
	oninit:function(headers, expandedindices){ //custom code to run when headers have initalized
		//do nothing
	},
	onopenclose:function(header, index, state, isuseractivated){ //custom code to run whenever a header is opened or closed
		//do nothing
	}
})
</script>
<script language="javascript" type="text/javascript" src="<?php echo INCLUDE_DIR;?>/in_admin/niceforms.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?php echo INCLUDE_DIR;?>/in_admin/niceforms-default.css" />

</head>
<body>
<div id="main_container">

	<div class="header">
	    <div class="logo"><a href="/" style="margin-bottom: 0px; font-size: 60px;">Pushwing</a></div>

	    <div class="right_header">
	    	Welcome <?php echo $this->tank_auth->get_username(); ?>,
	    	<a href="http://www.sweettracker.net" target="_blank">Visit site</a> |
	    	<!--
	    	<a href="#" class="messages">(3) Messages</a> |
	    	-->
	    	<?php if( $this->tank_auth->is_logged_in() ) { ?>
	    		<a href="/auth/logout" class="logout">Logout</a>
	    	<?php } else { ?>
	    		<a href="/auth/login" class="logout">Login</a>
	    	<?php } ?>
	    </div>
	    <div class="jclock"></div>
    </div>

    <div class="main_content">

		<div class="menu">
			<ul>
<?php
	$cur_menu_idx = $this->uri->segment(4);
	foreach ($menu_archi as $archi) {
		echo "<li>";
		if ($archi['idx'] == $cur_menu_idx) $class_cur = "class='current'"; else $class_cur = "";
		echo "<a ". $class_cur ." href='". $archi['href'] ."' title='". $archi['title'] ."'>". $archi['name'] ."</a>\n";
		if ($archi['idx'] != $cur_menu_idx && count($archi['sub_menu']) != 0) {
			echo "\t<ul>\n";
			foreach ($archi['sub_menu'] as $archi2) {
				echo "\t\t<li>";
				if (count($archi2['sub_menu']) != 0) $class_sub1 = "class='sub1'"; else $class_sub1 = "";
				echo "<a ". $class_sub1 ."href='". $archi2['href'] ."' title='". $archi2['title'] ."'>". $archi2['name'] ."</a>";
				if (count($archi2['sub_menu']) != 0) {
					echo "\t\t\t<ul>\n";
					foreach ($archi2['sub_menu'] as $archi3) {
						echo "\t\t\t\t<li>";
						if (count($archi3['sub_menu']) != 0) $class_sub2 = "class='sub2'"; else $class_sub2 = "";
						echo "<a ". $class_sub2 ." href='". $archi3['href'] ."' title='". $archi3['title'] ."'>". $archi3['name'] ."</a>";
							if (count($archi3['sub_menu']) != 0) {
								echo "\t\t\t\t\t<ul>\n";
								foreach ($archi3['sub_menu'] as $archi4) {
									echo "\t\t\t\t\t\t<li>";
									echo "<a href='". $archi4['href'] ."' title='". $archi4['title'] ."'>". $archi4['name'] ."</a>";
									echo "</li>\n";
								}
								echo "\t\t\t\t\t</ul>\n";
							}
						echo "</li>\n";
					}
					echo "\t\t\t</ul>\n";
				}
				echo "</li>\n";
			}
			echo "\t</ul>\n";
		}
		echo "</li>\n";
	}
?>
			</ul>
		</div>

		<div class="center_content">

			<div class="left_content">

				<div class="sidebarmenu">
<?php
	if ( $cur_menu_idx > -1 ) {
		foreach ($menu_archi[$cur_menu_idx]['sub_menu'] as $s_archi) {
			if (count($s_archi['sub_menu'])==0) $class_str = "menuitem"; else $class_str = "menuitem submenuheader";
			echo "<a class='". $class_str ."' href='". $s_archi['href'] ."'>". $s_archi['name'] ."</a>";
			if (count($s_archi['sub_menu'])!=0){
				echo "<div class='submenu'>";
				echo "<ul>";
				foreach ($s_archi['sub_menu'] as $s_archi2) {
					echo "<li><a href='". $s_archi2['href'] ."'>". $s_archi2['name'] ."</a></li>";
				}
				echo "</ul>";
				echo "</div>";
			}
		}
	}
?>
<!-- 필요할 경우에 사용하자.
					<a class="menuitem" href="">User Reference</a>
					<a class="menuitem"	href="">Blue button</a>
					<a class="menuitem_green" href="">Green	button</a>
					<a class="menuitem_red" href="">Red button</a>
-->
				</div>

<!--
				<div class="sidebar_box">
					<div class="sidebar_box_top"></div>
					<div class="sidebar_box_content">
						<h3>User help desk</h3>
						<img src="<?php echo IMG_DIR;?>/info.png" alt="" title=""
							class="sidebar_icon_right" />
						<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed
							do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
					</div>
					<div class="sidebar_box_bottom"></div>
				</div>

				<div class="sidebar_box">
					<div class="sidebar_box_top"></div>
					<div class="sidebar_box_content">
						<h4>Important notice</h4>
						<img src="<?php echo IMG_DIR;?>/notice.png" alt="" title=""
							class="sidebar_icon_right" />
						<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed
							do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
					</div>
					<div class="sidebar_box_bottom"></div>
				</div>

				<div class="sidebar_box">
					<div class="sidebar_box_top"></div>
					<div class="sidebar_box_content">
						<h5>Download photos</h5>
						<img src="<?php echo IMG_DIR;?>/photo.png" alt="" title=""
							class="sidebar_icon_right" />
						<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed
							do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
					</div>
					<div class="sidebar_box_bottom"></div>
				</div>

				<div class="sidebar_box">
					<div class="sidebar_box_top"></div>
					<div class="sidebar_box_content">
						<h3>To do List</h3>
						<img src="<?php echo IMG_DIR;?>/info.png" alt="" title=""
							class="sidebar_icon_right" />
						<ul>
							<li>Lorem ipsum dolor sit amet, consectetur adipisicing elit.</li>
							<li>Lorem ipsum dolor sit ametconsectetur <strong>adipisicing</strong>
								elit, sed do eiusmod tempor incididunt ut labore et dolore
								magna aliqua.</li>
							<li>Lorem ipsum dolor sit amet, consectetur <a href="#">adipisicing</a>
								elit.</li>
							<li>Lorem ipsum dolor sit amet, consectetur adipisicing elit.</li>
							<li>Lorem ipsum dolor sit amet, consectetur adipisicing elit.</li>
							<li>Lorem ipsum dolor sit amet, consectetur adipisicing elit.</li>
						</ul>
					</div>
					<div class="sidebar_box_bottom"></div>
				</div>
				-->
			</div>