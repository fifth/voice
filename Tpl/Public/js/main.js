	//js操作cookie函数
		function SetCookie (name, value) { 
            var exp = new Date(); 
            exp.setTime (exp.getTime()+3600000000); 
            document.cookie = name + "=" + value + "; expires=" + exp.toGMTString()+"; path=/"; 
        }
        function getCookieVal (offset) { 
            var endstr = document.cookie.indexOf (";", offset); 
            if (endstr == -1) endstr = document.cookie.length; 
            return unescape(document.cookie.substring(offset, endstr)); 
        } 
        function DelCookie(name) {
            var exp = new Date();
            exp.setTime (exp.getTime() - 1);
            var cval = GetCookie (name);
            document.cookie = name + "=" + cval + "; expires="+ exp.toGMTString();
        }
        function GetCookie(name) {
            var arg = name + "="; 
            var alen = arg.length; 
            var clen = document.cookie.length; 
            var i = 0; 
            while (i < clen) { 
                var j = i + alen; 
                if (document.cookie.substring(i, j) == arg) return getCookieVal (j); 
                i = document.cookie.indexOf(" ", i) + 1; 
                if (i == 0) break; 
            } 
            return null; 
        }

//全局变量，用作储存当前播放歌曲的信息，json格式
	var now;

	function slidein(boxname) {
		$('#'+boxname).css('display','block');
		if (boxname=='mainbox') {
			$('#'+boxname).animate({
				left: '60px',
			})
		} else {
			$('#'+boxname).animate({
				right: '60px',
			});
		}
	}

	function slideout(boxname) {
		$('#'+boxname).animate({
			right: '-705px',
		},function(){
			$('#'+boxname).css('display','none');
		});
	}

	function slide(boxname) {
		switch (boxname) {
		case 'searchbox':
			slidein('searchbox');
			slideout('uploadbox');
			slidein('playerbox');
			slideout('playlistbox');
			slideout('viewbox');
			slideout('toplistbox');
			break;
		case 'uploadbox':
			slideout('searchbox');
			slidein('uploadbox');
			slideout('playerbox');
			listen_reset();
			slideout('playlistbox');
			//slideout('viewbox');
			slideout('toplistbox');
			break;
		case 'playerbox':
			slideout('searchbox');
			slideout('uploadbox');
			slidein('playerbox');
			slidein('playlistbox');
			slideout('viewbox');
			slideout('toplistbox');
			$.get('/fifth/voice/index.php/Index/ajax?action=getalllist',function(data){
				data = eval("("+data+")");
				$('#alllist').empty();
				var htmldata = '<ul>';
				for (var i = 0; i < data.length; i++) {
					htmldata += "<li><a href='javascript:' onclick='choose(";
					htmldata += data[i].id;
					htmldata += ")'>";
					htmldata += data[i].name;
					htmldata += "</a>[BY]<a href='javascript:' onclick='view(";
					htmldata += data[i].singerid;
					htmldata += ")'>";
					htmldata += data[i].singer;
					htmldata += "</a></li>";
				};
				htmldata += '</ul>';
				$('#alllist').append(htmldata);
			});
			$.get('/fifth/voice/index.php/Index/ajax?action=getfavoritelist',function(data){
				data = eval("("+data+")");
				$('#favoritelist').empty();
				htmldata = '<ul>';
				if (data!=0) {
					for (var i = 0; i < data.length; i++) {
						htmldata += "<li><a href='javascript:' onclick='choose(";
						htmldata += data[i].id;
						htmldata += ")'>";
						htmldata += data[i].name;
						htmldata += "</a>[BY]<a href='javascript:' onclick='view(";
						htmldata += data[i].singerid;
						htmldata += ")'>";
						htmldata += data[i].singer;
						htmldata += "</a></li>";
					};
				} else {
					htmldata += 'not login';
				}
				htmldata += '</ul>';
				$('#favoritelist').append(htmldata);
			});
			$('#jquery_jplayer_1').jPlayer({
			ready:function(){
				$.get('/fifth/voice/index.php/Index/ajax?action=play',function(data){
					now=data;
					detail();
					$('#jquery_jplayer_1').jPlayer('clearMedia').jPlayer('setMedia',{
						mp3: '/fifth/voice/Upload/song/'+data.address
					}).jPlayer('play');
				},'json');
			},
			ended:function(){
				$.get('/fifth/voice/index.php/Index/ajax?action=next',function(data){
					now=data;
					detail();
					$('#jquery_jplayer_1').jPlayer('clearMedia').jPlayer('setMedia',{
						mp3: '/fifth/voice/Upload/song/'+data.address
					}).jPlayer('play');
				},'json');
			},
			swfPath:'/fifth/voice/Tpl/Public',
			supplied:'mp3'
			});

			break;
		case 'viewbox':
			slideout('searchbox');
			slidein('playerbox');
			slideout('uploadbox');
			slideout('playlistbox');
			slidein('viewbox');
			slideout('toplistbox');
			break;
		case 'toplistbox':
			slideout('searchbox');
			slideout('uploadbox');
			slidein('playerbox');
			slideout('playlistbox');
			slideout('viewbox');
			slidein('toplistbox');

			$.get('/fifth/voice/index.php/Index/ajax_gettoplist',function(data){

			});
			break;
		default:
			slideout('searchbox');
			slideout('uploadbox');
			slideout('playerbox');
			slideout('playlistbox');
			slideout('viewbox');
			slideout('toplistbox');
			listen_reset();
			break;
		}
	}

	function listen_reset() {
		$('#jquery_jplayer_1').jPlayer('clearMedia');
	}
	function listen_start() {
	}
//选择歌曲播放
	function choose(songid) {
		$.get('/fifth/voice/index.php/Index/ajax_choose?songid='+songid,function(data){
			now=data;
			detail();
			$("#jquery_jplayer_1").jPlayer('clearMedia').jPlayer('setMedia',{
				mp3: '/fifth/voice/Upload/song/'+data.address
			}).jPlayer('play');
		},'json');
	}

//显示当前播放歌曲的信息
	function detail(){
		$('#detail').remove();
		$('div.detail').append("<p id='detail'>正在播放：<br />"+now.name+"[BY]<a href='javascript:' onclick='view("+now.singerid+")'>"+now.singer+"</a><br />--------------------<br />"+now.message+"</p>");
		$.get('/fifth/voice/index.php/Index/ajax_likeOrNot?songid='+now.id,function(data){
			// alert('s');
			if (data==1) {
				content='like_hover';
				now.like=1;
			} else {
				content='like';
				now.like=0;
			}
			$(".jp-like").css("background-image","url(/fifth/voice/Tpl/Public/pic/"+content+".png)");
		},'text');
	}
//查看个人信息
	function view(uid) {
		slide('viewbox');
		$.get('/fifth/voice/index.php/Index/ajax_view?uid='+uid,function(data){
			data=eval("("+data+")");
			var htmldata="<div class='information'>";
			if (data[0].manage==data[0].id) {
				htmldata+="<input type='button' id='startedit' onclick='start_edit()' value='启用编辑' />";
				htmldata+="<input type='submit' id='submitedit' style='display:none' value='提交修改' />";
			} else {
				htmldata+="<input type='button' onclick='view("+data[0].manage+")' value='查看我的资料' />";
			}
			htmldata+="</div>";
			htmldata+="<div class='information'><input type='text' id='nickname' readonly='readonly' name='nickname' value='"+data[0].nickname+"' placeholder='一个帅气漂亮的昵称？' /></div>";
			htmldata+="<div class='information'><input type='text' id='sex' readonly='readonly' name='sex' value='"+data[0].sex+"' placeholder='告诉别人你的性别？' /></div>";
			$('.information').remove();
			$('#information').append(htmldata);
			htmldata="<textarea id='signature' name='signature' readonly='readonly' placeholder='说点儿什么吧？'>"+data[0].signature+"</textarea>";
			$('#signaturebox').empty();
			$('#signaturebox').append(htmldata);
			$('#portraitsbox').empty();
			$('#portraitsbox').append('<div><img id="portraits" src="/fifth/voice/Upload/portraits/'+data[0].portraits+'" /></div>');
			$('#portraitsbox').append('<input type="file" id="newimg" name="newimg" style="display:none" />');

			$('#songlist').empty();
				htmldata = '<ul>';
				if (data[1]!=0) {
					for (var i = 0; i < data[1].length; i++) {
						htmldata += "<li><a href='javascript:' onclick='choose(";
						htmldata += data[1][i].id;
						htmldata += ")'>";
						htmldata += data[1][i].name;
						htmldata += "</a>[BY]<a href='javascript:' onclick='view(";
						htmldata += data[1][i].singerid;
						htmldata += ")'>";
						htmldata += data[1][i].singer;
						htmldata += "</a></li>";
					};
				} else {
					htmldata += 'no data';
				}
				htmldata += '</ul>';
				$('#songlist').append(htmldata);
		})
	}

	function start_edit() {
		$('#nickname').removeAttr('readonly','readonly');
		$('#sex').removeAttr('readonly','readonly');
		$('#signature').removeAttr('readonly','readonly');
		$('#portraits').attr('onclick','changeimg()');
		$('#startedit').css('display','none');
		$('#submitedit').css('display','block');
	}

	function changeimg() {
		$('#newimg').click();
		$('#newimg').change(function(){
	        $('#portraitsbox').append('<div style="text-align:center;vertical-align:center;background-color:white;">已经收到新头像</div>')
	    });
	}

//此js文件用于上传歌曲页面的表单效果展示
//上传表单效果
	$(document).ready(function() {
	    $('#upbox').click(function(){
	        $('#uploadfile').click();
	    })
	    $('#uploadfile').change(function(){
	        $('#upbox').val($('#uploadfile').val());
	    })
	    $("#submit").click(function(){
	        $("#uploading").css("display","block");
	    })
	    $('#submit').ready(function(){
	        if ($('#read').attr("checked")=="checked") {
	            $('#submit').removeAttr('disabled','disabled');
	        }
	    })
	    $('#read').click(function(){
	        if ($('#read').attr("checked")=="checked") {
	            $("#submit").removeAttr('disabled','disabled');
	         } else {
	            $('#submit').attr('disabled','disabled');
	         }
	    })
	})

/*****************************************************/

//此js文件用于搜索页面的搜索功能实现
//搜索功能
	$(document).ready(function(){
		$('#search').click(function(){
			var target=$('#target').val();
			var content=$('#content').val();
			$('#result').html('');
			$.get('/fifth/voice/index.php/Index/ajax_search?target='+target+'&content='+content,function(data){
				for (i=0;i<data.length;i++) {
					$('#result').append("<li>"+data[i]+"</li>");
				};
			},'json');
		});
	});

/*****************************************************/

//此js文件用于用户个人信息的管理和修改
	$(document).ready(function(){
		//默认设置
		if ($('textarea.info').val()=='') {
			$('textarea.info').attr('placeholder','说点什么吧？');
		}
		if ($('#email').val()=='') {
			$('#email').attr('placeholder','留个联系方式吧？');
		}
		$('.info').attr('readonly','readonly');
		$('.info_dis').attr('readonly','readonly');
		var sex='#';
		sex+=$('#sex').val();
		if ($('#sex').val()=='保密') {
			sex+='1';
		}
		var constellation='#';
		constellation+=$('#constellation').val();
		if ($('#constellation').val()=='保密') {
			constellation+='2';
		}
		//启动修改按钮
		$('#edit').click(function(){
			$('.sel').css('display','inline');
			$('.info').removeAttr('readonly');
			$('.info_dis').css('display','none');
			$('#confirm').css('display','inline');
			$('#edit').css('display','none');
			$(sex).attr('selected','selected');
			$(constellation).attr('selected','selected');
			$('span').css('display','inline');
			$('#upbox').attr('readonly','readonly');
		})
		//上传头像图片
		$('#browse').click(function(){
			$('#upimg').click();
		})
		$('#upimg').change(function(){
			$('#upbox').val($('#upimg').val());
		})
	})

/*****************************************************/

//此js文件用于首页滚动新闻的显示
//滚动插件
	// (function($){
	// $.fn.extend({
	//     Scroll:function(opt,callback){
	//         //参数初始化
	//        if(!opt) var opt={};
	//        var _this=this.eq(0).find("ul:first");
	//        var    lineH=_this.find("li:first").height(), //获取行高
	//            line=opt.line?parseInt(opt.line,10):parseInt(this.height()/lineH,10), //每次滚动的行数，默认为一屏，即父容器高度
	//             speed=opt.speed?parseInt(opt.speed,10):500, //卷动速度，数值越大，速度越慢（毫秒）
	//             timer=opt.timer?parseInt(opt.timer,10):3000; //滚动的时间间隔（毫秒）
	//       if(line==0) line=1;
	//       var upHeight=0-line*lineH;
	//       //滚动函数
	//       scrollUp=function(){
	//           _this.animate({
 //  	              marginTop:upHeight
 //   	         },speed,function(){
 //    	           for(i=1;i<=line;i++){
 //    	               _this.find("li:first").appendTo(_this);
 //    	           }
 //           		    _this.css({marginTop:0});
	//             });
 //       		}
	//         //鼠标事件绑定
	//         _this.hover(function(){
	//             clearInterval(timerID);
 //    	    },function(){
 //           		timerID=setInterval("scrollUp()",timer);
 //       		}).mouseout();
 //    	}    
	// })
	// })(jQuery);
	// 	$(document).ready(function(){
 //   		$("#news").Scroll({line:1,speed:1000,timer:2000});
	// });

/*****************************************************/

//此js文件用于播放相关功能配置，包括播放器功能键以及播放控制按钮及收藏/取消收藏功能
//播放控制
	$(document).ready(function(){
	//jPlayer播放器
		// $('#jquery_jplayer_1').jPlayer({
		// 	ready:function(){
		// 		$.get('/fifth/voice/index.php/Index/ajax_get',function(data){
		// 			now=data;
		// 			detail();
		// 			$('#jquery_jplayer_1').jPlayer('clearMedia').jPlayer('setMedia',{
		// 				mp3: '/fifth/voice/Upload/song/'+data.address
		// 			}).jPlayer('play');
		// 		},'json');
		// 	},
		// 	ended:function(){
		// 		$.get('/fifth/voice/index.php/Index/ajax_get',function(data){
		// 			now=data;
		// 			detail();
		// 			$('#jquery_jplayer_1').jPlayer('clearMedia').jPlayer('setMedia',{
		// 				mp3: '/fifth/voice/Upload/song/'+data.address
		// 			}).jPlayer('play');
		// 		},'json');
		// 	},
		// 	swfPath:'/fifth/voice/Tpl/Public',
		// 	supplied:'mp3'
		// });
	//下一首
		// $('.jp-next').click(function(){
		// 	$.get('/fifth/voice/index.php/Index/ajax?action=next',function(data){
		// 		now=data;
		// 		detail();
		// 		$('#jquery_jplayer_1').jPlayer('clearMedia').jPlayer('setMedia',{
		// 			mp3 :'/fifth/voice/Upload/song/'+now.address
		// 		}).jPlayer('play');
		// 	},'json')
		// });
		// $('.jp-previous').click(function(){
		// 	//alert('d');
		// 	$.get('/fifth/voice/index.php/Index/ajax?action=previous',function(data){
		// 		now=data;
		// 		detail();
		// 		$('#jquery_jplayer_1').jPlayer('clearMedia').jPlayer('setMedia',{
		// 			mp3 :'/fifth/voice/Upload/song/'+now.address
		// 		}).jPlayer('play');
		// 	},'json')
		// });
	//切换播放模式
		$('#mode').click(function(){
			$.get('/fifth/voice/index.php/Index/ajax_changemode',function(data){
				$('#mode').val('mode:'+data);
			},'text');
		});
	//切换播放列表
		$('#order').click(function(){
			var action=$('#order').val();
			if (action=='order') {
				action='random';
			} else if (action=='random') {
				action='order';
			}
			$.get('/fifth/voice/index.php/Index/ajax_changeList?action='+action,function(data){
					$('#nowlist').html('');
					for (i=0;i<data.length;i++) {
						$('#nowlist').append("<li><a href='' onclick='choose("+data[i].id+");return false'>"+data[i].name+"</a>[BY]<a href='/fifth/voice/index.php/Index/view?guestid="+data[i].singerid+"'>"+data[i].singer+"</li>");
					}
				},'json');
			$('#order').val(action);
		});
	//收藏/取消收藏
		// $('.jp-like').click(function(){
		// 	var action=$('.jp-like').css("background-image");
		// 	if (action.match("like_hover")) {
		// 		action=1;
		// 	} else {
		// 		action=0;
		// 	}
		// 	$.get('/fifth/voice/index.php/Index/ajax_like?action='+action,function(data){
		// 		if (data) {
		// 			$(".jp-like").css("background-image","url(/fifth/voice/Tpl/Public/pic/like_hover.png)");
		// 		} else if (!action) {
		// 			$(".jp-like").css("background-image","url(/fifth/voice/Tpl/Public/pic/like.png)");
		// 		}
		// 		$.get('/fifth/voice/index.php/Index/ajax?action=getfavoritelist',function(data){
		// 			data = eval("("+data+")");
		// 			$('#favoritelist').empty();
		// 			htmldata = '<ul>';
		// 			if (data!=0) {
		// 				for (var i = 0; i < data.length; i++) {
		// 					htmldata += "<li><a href='javascript:' onclick='choose(";
		// 					htmldata += data[i].id;
		// 					htmldata += ")'>";
		// 					htmldata += data[i].name;
		// 					htmldata += "</a>[BY]<a href='javascript:' onclick='view(";
		// 					htmldata += data[i].singerid;
		// 					htmldata += ")'>";
		// 					htmldata += data[i].singer;
		// 					htmldata += "</a></li>";
		// 				};
		// 			} else {
		// 				htmldata += 'not login';
		// 			}
		// 			htmldata += '</ul>';
		// 			$('#favoritelist').append(htmldata);
		// 	});
		// 	},'json');
		// });
	})

/*****************************************************/
function random_click(){
	random=GetCookie('random');
	DelCookie('random');
	SetCookie('random', 1-random);
	if (random==0) {
		$(".jp-random").css("background-image","url(/fifth/voice/Tpl/Public/pic/random_hover.png)");
	} else {
		$(".jp-random").css("background-image","url(/fifth/voice/Tpl/Public/pic/random.png)");
	}
}
function random_hover(){
	$(".jp-random").css("background-image","url(/fifth/voice/Tpl/Public/pic/random_hover.png)");
}
function random_default(){
	if (GetCookie('random')==1) {
		$(".jp-random").css("background-image","url(/fifth/voice/Tpl/Public/pic/random_hover.png)");
	} else {
		$(".jp-random").css("background-image","url(/fifth/voice/Tpl/Public/pic/random.png)");
	}
}
function circle_click(){
	circle=GetCookie('circle');
	DelCookie('circle');
	SetCookie('circle', 1-circle);
	if (circle==0) {
		$(".jp-circle").css("background-image","url(/fifth/voice/Tpl/Public/pic/circle_hover.png)");
	} else {
		$(".jp-circle").css("background-image","url(/fifth/voice/Tpl/Public/pic/circle.png)");
	}
}
function circle_hover(){
	$(".jp-circle").css("background-image","url(/fifth/voice/Tpl/Public/pic/circle_hover.png)");
}
function circle_default(){
	if (GetCookie('circle')==1) {
		$(".jp-circle").css("background-image","url(/fifth/voice/Tpl/Public/pic/circle_hover.png)");
	} else {
		$(".jp-circle").css("background-image","url(/fifth/voice/Tpl/Public/pic/circle.png)");
	}
}
function next_click(){
	$.get('/fifth/voice/index.php/Index/ajax?action=next',function(data){
		if (data==null) listen_reset();
		now=data;
		detail();
		$('#jquery_jplayer_1').jPlayer('clearMedia').jPlayer('setMedia',{
			mp3 :'/fifth/voice/Upload/song/'+now.address
		}).jPlayer('play');
	},'json');
}
function previous_click(){
	$.get('/fifth/voice/index.php/Index/ajax?action=previous',function(data){
		if (data==null) listen_reset();
		now=data;
		detail();
		$('#jquery_jplayer_1').jPlayer('clearMedia').jPlayer('setMedia',{
			mp3 :'/fifth/voice/Upload/song/'+now.address
		}).jPlayer('play');
	},'json');
}
function like_hover(){
	$(".jp-like").css("background-image","url(/fifth/voice/Tpl/Public/pic/like_hover.png)");
}
function like_default(){
	if (now.like==1) {
			$(".jp-like").css("background-image","url(/fifth/voice/Tpl/Public/pic/like_hover.png)");
	} else if (now.like==0) {
			$(".jp-like").css("background-image","url(/fifth/voice/Tpl/Public/pic/like.png)");
	}
	// $.get('/fifth/voice/index.php/Index/ajax_likeOrNot?songid='+now.id,function(data){
	// 	if (data==1) {
	// 		$(".jp-like").css("background-image","url(/fifth/voice/Tpl/Public/pic/like_hover.png)");
	// 	} else {
	// 		$(".jp-like").css("background-image","url(/fifth/voice/Tpl/Public/pic/like.png)");
	// 	}
	// },'text');
}
function like_click(){
	var action=$('.jp-like').css("background-image");
	if (action.match("like_hover")) {
		action=1;
	} else {
		action=0;
	}
	$.get('/fifth/voice/index.php/Index/ajax_like?action='+action,function(data){
		if (data) {
			$(".jp-like").css("background-image","url(/fifth/voice/Tpl/Public/pic/like_hover.png)");
		} else if (!action) {
			$(".jp-like").css("background-image","url(/fifth/voice/Tpl/Public/pic/like.png)");
		}
		$.get('/fifth/voice/index.php/Index/ajax?action=getfavoritelist',function(data){
			data = eval("("+data+")");
			$('#favoritelist').empty();
			htmldata = '<ul>';
			if (data!=0) {
				for (var i = 0; i < data.length; i++) {
					htmldata += "<li><a href='javascript:' onclick='choose(";
					htmldata += data[i].id;
					htmldata += ")'>";
					htmldata += data[i].name;
					htmldata += "</a>[BY]<a href='javascript:' onclick='view(";
					htmldata += data[i].singerid;
					htmldata += ")'>";
					htmldata += data[i].singer;
					htmldata += "</a></li>";
				};
			} else {
				htmldata += 'not login';
			}
			htmldata += '</ul>';
			$('#favoritelist').append(htmldata);
	});
	},'json');
	detail();
}