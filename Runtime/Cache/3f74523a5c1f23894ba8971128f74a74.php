<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<title>upload</title>
	<!--外部css文件-->
	<link rel="stylesheet" type="text/css" href="../Public/css/main.css" />
	<!--外部js文件-->
	<script type="text/javascript" src="../Public/js/jquery.js"></script>
	<script type="text/javascript" src="../Public/js/main.js"></script>
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

	<div id='upload'>
	<img onclick="go('listen')" onmouseover="hover('upload')" onmouseout="hover_cancel('upload')" id='uploadimg' src='../Public/pic/upload.png' />
	</div>

	<div id='upload-function'>
		<br />
	<form action='./upload_submit' method='post' enctype='multipart/form-data'>
	<br />
	<br />
	<div class='welcome'>
		<p>欢迎您，<?php echo ($name); ?>：<br />&nbsp;&nbsp;&nbsp;&nbsp;您已上传<?php echo ($num); ?>首歌，这是您上传的第<?php echo ($newnum); ?>首歌。</p>
	</div>
	<br />
	<div class='uploadbox'>
		<p>请选择您要上传的歌曲&nbsp;<a href="">上传遇到问题？</a></p>
		<input type='text' id='upbox' readonly='readonly' placeholder='请选择mp3格式的音乐文件' />
		<input type='file' id='uploadfile' name='song' />
		<input type='button' id='browse' value='选择文件' />
		<br />
		<br />
		<p>请输入歌曲名：</p>
		<input type='text' id='textbox' name='songname' placeholder='请在此输入歌曲名' />
		<br />
		<br />
		<p>说几句吧？</p>
		<textarea name='message' cols='29' rows='5' placeholder='leave a message'></textarea>
		<br />
		<br />
		<p><input type='checkbox' id='read' />我已阅读并同意&nbsp;<a href="">《歌曲上传须知》</a></p>
		<input type='submit' id='submit' disabled='disabled' value='确认无误，上传' />
		<span id="uploading">正在上传......</span>
	</div>
	</form>
	</div>

</body>
</html>