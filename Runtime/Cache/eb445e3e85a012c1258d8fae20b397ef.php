<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<title>view</title>
	<!--外部css文件-->
	<link rel="stylesheet" type="text/css" href="../Public/css/blue.css" />
	<link rel="stylesheet" type="text/css" href="../Public/css/main.css" />
	<style type="text/css">
		#playerbox {
			position: absolute;
			top: 170px;
			left: 400px;
			right: 0px;
		}
	</style>h
	<!--外部js文件-->
	<script type="text/javascript" src='../Public/js/jquery.js'></script>
	<script type="text/javascript" src='../Public/js/jquery.jplayer.min.js'></script>
	<script type="text/javascript" src="../Public/js/main.js"></script>
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
	    <li><a href="javascript:" onclick='slide("searchbox")'><b>搜索</b></a>&nbsp;&nbsp;</li>
	    <li><a href="javascript:" onclick='slide("toplistbox")'><b>排行榜</b></a>&nbsp;&nbsp;</li>
	    <?php if(isset($index)): else: ?>
	    <li><a href="<?php echo U('/Index/index');?>"><b>返回主页</b></a>&nbsp;&nbsp;</li><?php endif; ?>
	</ul>
</div>
<div id='footer'>
	<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Copyright ©2012 &nbsp;&nbsp; 浙江大学党委学生工作部&nbsp;&nbsp;&nbsp;:::&nbsp;&nbsp;&nbsp;求是潮&nbsp;   All Rights Reserved.&nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;Powered by <a href="http://tech.myqsc.com/">浙江大学 求是潮网站&nbsp; ::: &nbsp;技术研发中心</a></p>
</div>

	<div id='main'>
		<div id='welcome'>
		<p>欢迎您，<?php echo ($name); ?>：<br />&nbsp;&nbsp;&nbsp;&nbsp;
		<?php if(isset($manage)): ?>这里是个人管理页面。<br />&nbsp;&nbsp;&nbsp;&nbsp;
			您可以在这里管理您的个人信息，歌单以及查看您上传的歌曲。
		<?php else: ?>
			这里是<?php echo ($info["nickname"]); ?>的个人页面。<br />&nbsp;&nbsp;&nbsp;&nbsp;
			您可以查看<?php echo ($TA); ?>的个人信息，歌单以及上传的歌。<?php endif; ?>
		</p>
		</div>
		<div id='information'>
		<p>
		<?php if(isset($manage)): ?><form action="<?php echo U('Index/view_submit');?>" method='post' enctype='multipart/form-data'>
		<?php echo ($TA); ?>的个人信息&nbsp;&nbsp;&nbsp;&nbsp;<input type='button' id='edit' value='修改信息'/><input type='submit' id='confirm' value='提交修改' /><br /><br />
		昵称：<input type='text' id='nickname' maxlength='10' size='20' class='info' name='nickname' value='<?php echo ($info["nickname"]); ?>' /><br /><br />
		性别：<input type='text' class='info_dis' id='sex' value="<?php echo ($info["sex"]); ?>" readonly='readonly' /><select class='sel' name='sex'><option id='保密1' value='保密'>保密</option><option id='男' value='男'>男</option><option id='女' value='女'>女</option></select><br /><br />
		星座：<input type='text' class='info_dis' id='constellation' readonly='readonly' value='<?php echo ($info["constellation"]); ?>' /><select class='sel' name='constellation'><option id='保密2' value='保密'>保密</option><option id='水瓶座' value='水瓶座'>水瓶座</option><option id='双鱼座' value='双鱼座'>双鱼座</option><option id='白羊座' value='白羊座'>白羊座</option><option id='金牛座' value='金牛座'>金牛座</option><option id='双子座' value='双子座'>双子座</option><option id='巨蟹座' value='巨蟹座'>巨蟹座</option><option id='狮子座' value='狮子座'>狮子座</option><option id='处女座' value='处女座'>处女座</option><option id='天秤座' value='天秤座'>天秤座</option><option id='天蝎座' value='天蝎座'>天蝎座</option><option id='射手座' value='射手座'>射手座</option><option id='摩羯座' value='摩羯座'>摩羯座</option></select><br /><br />
		<span>头像：<input type='text' class='info' id='upbox' size='27' placeholder='请上传jpg/png/gif格式的图片' /><br />
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span id='explain'>请尽量上传800*600大小的图片</span><input type='file' id='upimg' name='portraits' /><input type='button' id='browse' value='浏览' /><br /></span>
		邮箱：<input type='text' id='email' class='info' name='email' value='<?php echo ($info["email"]); ?>' /><br /><br />
		个性签名：<br /><textarea class='info' cols='40' rows='6' name='signature'><?php echo ($info["signature"]); ?></textarea>
		</form>
		<?php else: ?>
		<?php echo ($TA); ?>的个人信息<br /><br />
		昵称：<?php echo ($info["nickname"]); ?><br /><br />
		性别：<?php echo ($info["sex"]); ?><br /><br />
		星座：<?php echo ($info["constellation"]); ?><br /><br />
		邮箱：<?php echo ($info["email"]); ?><br /><br />
		个性签名：<?php echo ($info["signature"]); endif; ?>
		<img id='portraits' src="/voice/Upload/portraits/<?php echo ($info["portraits"]); ?>" />
		</p>
		</div>
		<div id='uploadbox'>
			<p><?php echo ($TA); ?>上传了<?php echo ($num_upload); ?>首歌&nbsp;&nbsp;&nbsp;&nbsp;<?php echo ($TA); ?>的热门度：<?php echo ($hotsinger); ?></p>
			<ul>
			<?php if(is_array($upload_list)): $i = 0; $__LIST__ = $upload_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li><a href="" onclick="choose(<?php echo ($vo["id"]); ?>);return false"><?php echo ($vo["name"]); ?></a>[BY]<a href="./view?guestid=<?php echo ($vo["singerid"]); ?>"><?php echo ($vo["singer"]); ?></a>--liked by <?php echo ($vo["mark"]); ?></li><?php endforeach; endif; else: echo "" ;endif; ?>
			</ul>
		</div>
		<div id='uploadimg'></div>
		<div id='favoritebox'>
			<p><?php echo ($TA); ?>收藏了<?php echo ($num_favorite); ?>首歌</p>
			<ul id="favoritelist">
			<?php if(is_array($favorite_list)): $i = 0; $__LIST__ = $favorite_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li><a href="" onclick="choose(<?php echo ($vo["id"]); ?>);return false"><?php echo ($vo["name"]); ?></a>[BY]<a href="./view?guestid=<?php echo ($vo["singerid"]); ?>"><?php echo ($vo["singer"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
			</ul>
		</div>
		<div id='favoriteimg'></div>
		
	</div>

</body>
</html>