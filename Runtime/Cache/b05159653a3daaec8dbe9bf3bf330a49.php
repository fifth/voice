<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<title>listen</title>
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
	   	<li><b>欢迎您，</b><a href="<?php echo ($view); ?>"><b><?php echo ($name); ?></a></b>&nbsp;&nbsp;</li><?php endif; ?>
	    <li><a href="#"><b>帮助</b></a>&nbsp;&nbsp;</li>
	    <li><a href="./feedback"><b>反馈</b></a>&nbsp;&nbsp;</li>
	    <?php if(isset($index)): else: ?>
	    <li><a href="<?php echo U('/Index/index');?>"><b>返回主页</b></a>&nbsp;&nbsp;</li><?php endif; ?>
	</ul>
</div>
<div id='footer'>
	<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Copyright ©2012 &nbsp;&nbsp; 浙江大学党委学生工作部&nbsp;&nbsp;&nbsp;:::&nbsp;&nbsp;&nbsp;求是潮&nbsp;   All Rights Reserved.&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;Powered by <a href="http://tech.myqsc.com/">浙江大学 求是潮网站&nbsp; ::: &nbsp;技术研发中心</a></p>
</div>
	<div id='listen-function'>
		<div class='welcome'>
		<?php if(isset($notlogin)): ?><p>您还没有登陆呦！<a href="<?php echo ($passport); ?>">戳我登陆</a></p>
		<?php else: ?>
			<p>欢迎您，<?php echo ($name); ?>：<a href="<?php echo ($view); ?>">进入个人管理</a></p><?php endif; ?>
		</div>
		<div id='playerbox'>
	<div id="jquery_jplayer_1" class="jp-jplayer" ></div>
	<div id="jp_container_1" class="jp-audio">
	<div class="jp-type-single">
	    <div class="jp-gui jp-interface">
	        <ul class="jp-controls">
		                <li><a href="javascript:;" class="jp-play" tabindex="1">play</a></li>
	    	            <li><a href="javascript:;" class="jp-pause" tabindex="1">pause</a></li>
	        	        <li><a href="javascript:;" class="jp-stop" tabindex="1">stop</a></li>
	                    <li><a href="javascript:;" class="jp-mute" tabindex="1" title="mute">mute</a></li>
	               	    <li><a href="javascript:;" class="jp-unmute" tabindex="1" title="unmute">unmute</a></li>
	                   	<li><a href="javascript:;" class="jp-volume-max" tabindex="1" title="max volume">max volume</a></li>
		    </ul>
		   	<div class="jp-progress">
	       		<div class="jp-seek-bar">
	        	<div class="jp-play-bar"></div>
	        	</div>
		    </div>
	    	<div class="jp-volume-bar">
	        	<div class="jp-volume-bar-value"></div>
	        </div>
	        <div class="jp-time-holder">
		        <div class="jp-current-time"></div>
	    	    <div class="jp-duration"></div>
	        </div>
	    </div>
		<div class="jp-no-solution">
	    	<span>Update Required</span>
	       	To play the media you will need to either update your browser to a recent version or update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.
	    </div>
	</div>
	</div>
	
	<div class="detail" style="margin-left:110px;"></div>
	<div id='controller' style="display:none;">
		<input id='next' type='button' value='next' />
		<input id='mode' type='button' value='mode:order' />
		<input id='order' type='button' value='random' />
		<?php if(isset($notlogin)): else: ?>
		<input id='like' type='button' /><?php endif; ?>
	</div>
</div>
		<div class='playlist'>
			<ul id='nowlist'>
			<?php if(is_array($song_list)): $i = 0; $__LIST__ = $song_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li><a href="" onclick="choose(<?php echo ($vo["id"]); ?>);return false"><?php echo ($vo["name"]); ?></a>[BY]<a href="./view?guestid=<?php echo ($vo["singerid"]); ?>"><?php echo ($vo["singer"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
			</ul>
			<ul id='favoritelist'>
			<?php if(is_array($favorite_list)): $i = 0; $__LIST__ = $favorite_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li><a href="" onclick="choose(<?php echo ($vo["id"]); ?>);return false"><?php echo ($vo["name"]); ?></a>[BY]<a href="./view?guestid=<?php echo ($vo["singerid"]); ?>"><?php echo ($vo["singer"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
			</ul>
		</div>	
	</div>

	<div id='listen'>
	<img onclick="go('search')" onmouseover="hover('listen')" onmouseout="hover_cancel('listen')" id='listenimg' src='../Public/pic/listen.png' />
	</div>
	<script type="text/javascript">
		$('#controller').css('display','block');
	</script>
</body>
</html>