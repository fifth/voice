<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<title>top</title>
	<!--外部css文件-->
	<link rel="stylesheet" type="text/css" href="../Public/css/blue.css" />
	<link rel="stylesheet" type="text/css" href="../Public/css/main.css" />
	<!--外部js文件-->
	<script type="text/javascript" src='../Public/js/jquery.js'></script>
	<script type="text/javascript" src='../Public/js/jquery.jplayer.min.js'></script>
	<script type="text/javascript" src='../Public/js/main.js'></script>
</head>
<body>
	<div id="header">
	<ul class='link'>
	  	<li><b>前往</b></li>
	  	<li>&nbsp;&nbsp;&nbsp;:::&nbsp;&nbsp;&nbsp;</li>
	  	<li><a href="http://www.qsc.zju.edu.cn/">求是潮主站</a></li>
	  	<li>&nbsp;&nbsp;&nbsp;:::&nbsp;&nbsp;&nbsp;</li>
	  	<li><a href="http://123.myqsc.com/">校网导航</a></li>
	  	<li>&nbsp;&nbsp;&nbsp;:::&nbsp;&nbsp;&nbsp;</li>
		<li><a href="http://box.myqsc.com/">Box云U盘</a></li>
		<li>&nbsp;&nbsp;&nbsp;:::&nbsp;&nbsp;&nbsp;</li>
		<li><a href="http://www.qsc.zju.edu.cn/apps/share/share/">Share学习交流平台</a></li>
		<li>&nbsp;&nbsp;&nbsp;:::&nbsp;&nbsp;&nbsp;</li>
		<li><a href="http://www.qsc.zju.edu.cn/apps/notice/">Notice活动发布平台</a></li>
	</ul>
	<ul class='passport'>
	<?php if(isset($notlogin)): ?><li><a href="<?php echo ($passport); ?>"><b>尚未登录？</b></a>&nbsp;</li>
	<?php else: ?>
	    <li><a href="<?php echo ($logout); ?>"><b>注销</b></a>&nbsp;</li>
	   	<li><b>欢迎您，</b><a href="javascript:" onclick='view(<?php echo ($view); ?>)'><b><?php echo ($name); ?></a></b>&nbsp;&nbsp;</li><?php endif; ?>
	    <li><a href="javascript:" onclick='slide("searchbox")'><b>搜索</b></a>&nbsp;&nbsp;</li>
	    <li><a href="javascript:" onclick='slide("toplistbox")'><b>排行榜</b></a>&nbsp;&nbsp;</li>
	<?php if(isset($index)): else: ?>
	    <li><a href="<?php echo U('/Index/index');?>"><b>返回主页</b></a>&nbsp;&nbsp;</li><?php endif; ?>
	</ul>
</div>
<div id='footer'>
	<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Copyright ©2012 &nbsp;&nbsp; 浙江大学党委学生工作部&nbsp;&nbsp;&nbsp;:::&nbsp;&nbsp;&nbsp;求是潮&nbsp;   All Rights Reserved.&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;Powered by <a href="http://tech.myqsc.com/">浙江大学 求是潮网站&nbsp; ::: &nbsp;技术研发中心</a></p>
</div>
	
	<div id='topbox'>
	<ul>
	<?php if(is_array($hotsong_list)): $i = 0; $__LIST__ = $hotsong_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li><a href="" onclick="choose(<?php echo ($vo["id"]); ?>);return false"><?php echo ($vo["name"]); ?></a>[BY]<a href="./view?guestid=<?php echo ($vo["singerid"]); ?>"><?php echo ($vo["singer"]); ?></a>--liked by<?php echo ($vo["mark"]); ?></li><?php endforeach; endif; else: echo "" ;endif; ?>
	<br />
	---------------
	<br />
	<?php if(is_array($hotsinger_list)): $i = 0; $__LIST__ = $hotsinger_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li><a href="./view?guestid=<?php echo ($vo["id"]); ?>"><?php echo ($vo["nickname"]); ?></a>--liked by<?php echo ($vo["mark"]); ?></li><?php endforeach; endif; else: echo "" ;endif; ?>
	</ul>
	</div>

	<div id='listen'>
	<img onclick="go('listen')" onmouseover="hover('listen')" onmouseout="hover_cancel('listen')" id='listenimg' src='../Public/pic/listen.png' />
	</div>

</body>
</html>