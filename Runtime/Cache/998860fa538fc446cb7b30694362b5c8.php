<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<title>index</title>
	<!--外部css文件-->
	<link rel="stylesheet" type="text/css" href="../Public/css/main.css" />
	<link rel="stylesheet" type="text/css" href="../Public/css/blue.css" />
	<!--外部js文件-->
	<script type="text/javascript" src="../Public/js/jquery.js"></script>
	<script type="text/javascript" src="../Public/js/main.js"></script>
	<script type="text/javascript" src="../Public/js/jquery.jplayer.min.js"></script>
</head>
<body onload='slidein("mainbox")'>
	
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
	<div id='mainbox'>
		<div id='mainbox_1'></div>
		<div id='mainbox_3'></div>
		<div id='mainbox_2'></div>
		<!--<div id='logo'><img src='../Public/pic/logo.png' /></div>
		
		<!--<div id='news'>
		<ul>
		<?php if(is_array($news_list)): $i = 0; $__LIST__ = $news_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; switch($vo["type"]): case "1": ?><li><a href="<?php echo U('/Index/view');?>?guestid=<?php echo ($vo["guestid"]); ?>"><?php echo ($vo["guestname"]); ?></a>加入了voice</li><?php break;?>
			<?php case "2": ?><li><a href="<?php echo U('/Index/view');?>?guestid=<?php echo ($vo["guestid"]); ?>"><?php echo ($vo["singername"]); ?></a>上传了<a href="" onclick="choose(<?php echo ($vo["id"]); ?>);return false"><?php echo ($vo["songname"]); ?></a></li><?php break;?>
			<?php case "3": ?><li><a href="<?php echo U('/Index/view');?>?guestid=<?php echo ($vo["guestid"]); ?>"><?php echo ($vo["guestname"]); ?></a>收藏了<a href="" onclick"choose(<?php echo ($vo["id"]); ?>);return false"><?php echo ($vo["songname"]); ?></a>[BY]<a href="<?php echo U('/Index/view');?>?guestid=<?php echo ($vo["singerid"]); ?>"><?php echo ($vo["singername"]); ?></a></li><?php break; endswitch; endforeach; endif; else: echo "" ;endif; ?>
		</ul>
		</div>-->
		<div id='upload_button' onclick='slide("uploadbox")'></div>
		<div id='listen_button' onclick='slide("playerbox")'></div>
		<div id='rightbottom_button_shadow'></div>
		<div id='rightbottom_button'></div>
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
		    	            <li><a href="javascript:;" class="jp-previous" tabindex="1" onclick="previous_click()">previous</a></li>
		    	            <li><a href="javascript:;" class="jp-next" tabindex="1" onclick="next_click()">next</a></li>
		    	            <?php if(isset($notlogin)): else: ?>
								<li><a href="javascript:;" class="jp-like" tabindex="1" onclick="like_click()" onmouseover="like_hover()" onmouseout="like_default()">like</a></li><?php endif; ?>
							<li><a href="javascript:;" class="jp-random" tabindex="1" onclick="random_click()" onmouseover="random_hover()" onmouseout="random_default()">random</a></li>
							<li><a href="javascript:;" class="jp-circle" tabindex="1" onclick="circle_click()" onmouseover="circle_hover()" onmouseout="circle_default()">circle</a></li>
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
		<div class="detail"></div>
	</div>
	<div id='playlistbox'>
		<div id='alllist'></div>
		<div id='favoritelist'></div>
	</div>
	<div id='uploadbox'>
		<form action='./upload_submit' method='post' enctype='multipart/form-data'>
			<div>
				<input type='text' id='upbox' readonly='readonly' placeholder='点击选择需要上传的歌' />
				<!--<input type='button' id='browse' value='选择文件' />-->
				<input type='file' id='uploadfile' name='song' />
			</div>
			<div>
				<input type='text' id='textbox' name='songname' placeholder='请在此输入歌曲名' />
			</div>
			<div>
				<textarea name='message' id='messagebox' cols='29' rows='5' placeholder='说几句吧？'></textarea>
			</div>
			<div>
				<input type='checkbox' id='read' />我已阅读并同意&nbsp;<a href="">《歌曲上传须知》</a>
				<input type='submit' id='submit' disabled='disabled' value='确认无误，上传并返回首页' />
			</div>
			<div>
				<span id="uploading">正在上传......</span>
			</div>			
		</form>
	</div>
	<div id='searchbox'>
		<p>查询目标：<select id='target'><option value='songname'>歌曲名</option><option value='nickname'>昵称</option><option value='sex'>性别</option><option value='constellation'>星座</option></select></p>
		<p>查询关键字：<input type='text' id='content' /><input type='button' id='search' value='search' /></p>
		<p>查询结果：</p>
		<ul id='result'>
		</ul>
	</div>
	<div id='viewbox'>
		<form action="<?php echo U('Index/view_submit');?>" method='post' enctype='multipart/form-data'>
		<div id='information'>
			<div id='signaturebox'></div>
			<div id='portraitsbox'></div>
		</div>
		</form>
		<div id='songlist'></div>
	</div>
	<div id='toplistbox'>
		<div id='topsongbox'></div>
		<div id='topsingerbox'></div>
	</div>
</body>
</html>