<?php
class IndexAction extends Action{
    private function check_session(){
    //私有函数，用作检查session
        if (isset($_SESSION['token'])) {
            $s=file_get_contents(PP_TOKEN.$_SESSION['token'],true);
            //API服务器以json的格式返回值
            $a=json_decode($s,true);
            if (isset($a['code'])) {
                return 0;
            }//code==0表明通行证验证失败
            return $a;
        }//session['token']存在则返回登陆数据，否则返回0
        return 0;   
    }
    private function cookie2json($json=''){
    //私有函数，用作去除cookie中获取的json中多余的反斜杠
    //参数默认值为空串
        //先找出原先是反斜杠的位置（此时为双反斜杠），将其替换成TTT
        $json=str_replace("\\\\", "TTT", $json);
        //将多出来的反斜杠替换成空串
        $json=str_replace("\\", "", $json);
        //再将被替换成的TTT替换回一个反斜杠
        $json=str_replace("TTT", "\\", $json);
        //返回去除多余反斜杠的新串$json
        return $json;
    }
    public function passport(){
    //公有函数，用作验证登陆
        $_SESSION['token']=$this->_param('token');
        $a=$this->check_session();
        $this->login($a,'TTTIndexTTTindex');
    }
    public function login($tmp_data=0,$tmp_url=NULL){
    //公有函数，用作登陆
        if (isset($_POST['token'])) {
            $tmp_data=1;
            $tmp_url=$this->_get('url');
        }
        if ($tmp_data==0) {
            header('Location:'.PP_RD.ROOT_URL.U("/Index/login?data=1&url=".$tmp_url));
            die();
        }
        if ($tmp_data==1) {
            $_SESSION['token']=$this->_param('token');
            $tmp_data=$this->check_session();
            if ($tmp_data['stuid']==0) {
                session_start();
                session_destroy();
                $this->error('这个账号没有激活,请使用浙大通行证重新注册！','https://passport.myqsc.com');
            }
        }
        //连接数据库
        $guest=M('guest');
        $news=M('news');

        $search['stuid']=$tmp_data['stuid'];
        $exist=$guest->where($search)->find();
        unset($search);
        $tmp_url=str_replace('TTT','/',$tmp_url);
        //添加首次登陆的用户并创建动态
        if ($exist==NULL) {
            $data['nickname']=$tmp_data['username'];
            $data['stuid']=$tmp_data['stuid'];
            $guestid=$guest->add($data);
            unset($data);
            $data['stuid']=$tmp_data['stuid'];
            $data['type']=1;
            $data['guestid']=$guestid;
            $news->add($data);
            unset($data);
        }
        if (isset($_POST['token'])) {
            $this->redirect($tmp_url);
        }
    }
    public function logout(){
    //公有函数，用作登出
        session_start();
        session_destroy();
        $url=$this->_param('redirect');
        if ($url) {
            header('Location:https://passport.myqsc.com/member/logout?redirect='.$url);
        } else {
            header('Location:https://passport.myqsc.com/member/logout?redirect='.U('/Index/index'));
        }
    }
    public function ajax(){
        $guest=M('guest');
        $song=M('song');
        $news=M('news');
        $favorite=M('favorite');
        $hotsong=M('hotsong');
        $hotsinger=M('hotsinger');
        $upload=M('upload');
        $action=$this->_param('action');
        $a=$this->check_session();
        switch ($action) {
            case 'getalllist'://获取所有歌曲列表，返回json格式的数组
                $song_id_list=$song->getField('id',true);
                //shuffle($song_id_list);
                foreach ($song_id_list as $key => $value) {
                    $search['id']=$value;
                    $song_list[]=$song->where($search)->find();
                    $song_list[$key]['singer']=$guest->where('id='.$song_list[$key]['singerid'])->getField('nickname');
                }
                unset($search);
                setcookie('list',json_encode($song_id_list));
                setcookie('nowplaying',0);
                if (!isset($_COOKIE['random'])) {
                    setcookie('random',0);
                }
                if (!isset($_COOKIE['circle'])) {
                    setcookie('circle',1);
                }
                echo json_encode($song_list);
                break;
            case 'getfavoritelist'://获取已收藏的歌曲，返回json格式的数组，若未登录则返回0
                //验证是否登陆
                if ($a!=0) {

                    // $guest=M('guest');
                    // $song=M('song');
                    // $news=M('news');
                    // $favorite=M('favorite');
                    // $hotsong=M('hotsong');
                    // $hotsinger=M('hotsinger');
                    // $upload=M('upload');

                    $search['stuid']=$a['stuid'];
                    $guestinfo=$guest->where($search)->find();
                    unset($search);
                    $search['guestid']=$guestinfo['id'];
                    $song_id_list=$favorite->where($search)->select();
                    unset($search);
                    foreach ($song_id_list as $key => $value) {
                        $search['id']=$value['songid'];
                        $song_list[]=$song->where($search)->find();
                        $song_list[$key]['singer']=$guest->where('id='.$song_list[$key]['singerid'])->getField('nickname');
                        //$search['id']被覆盖，不需要清空
                    }
                    unset($search);
                    echo json_encode($song_list);
                } else {
                    echo 0;
                }
                break;
            case 'play':
                if (!isset($_COOKIE['list'])) {
                    die();
                }
                //从cookie获取播放信息
                $song_id_list=json_decode($this->cookie2json($_COOKIE['list']),true);
                if (isset($_COOKIE['nowplaying'])) {
                    $nowplaying=(int)$_COOKIE['nowplaying'];
                } else {
                    $nowplaying=0;
                }
                $search['id']=$song_id_list[$nowplaying];
                $re=$song->where($search)->find();
                $re['singer']=$guest->where('id='.$re['singerid'])->getField('nickname');
                echo json_encode($re);
                break;
            case 'next':
                if (!isset($_COOKIE['list'])) {
                    die();
                }
                //从cookie获取播放信息
                $song_id_list=json_decode($this->cookie2json($_COOKIE['list']),true);
                if (isset($_COOKIE['nowplaying'])) {
                    $nowplaying=(int)$_COOKIE['nowplaying'];
                } else {
                    $nowplaying=0;
                }
                $circle=$_COOKIE['circle'];
                $random=$_COOKIE['random'];
                if ($random==1) {
                    $nowplaying=rand(0,count($song_id_list)-1);
                } else {
                    $nowplaying++;
                }
                if (($nowplaying>=count($song_id_list))&&($circle==1)) {
                    $nowplaying=0;
                }
                $search['id']=$song_id_list[$nowplaying];
                $re=$song->where($search)->find();
                if ($re==null) {
                    setcookie('nowplaying',$nowplaying);
                    break;
                }
                $re['singer']=$guest->where('id='.$re['singerid'])->getField('nickname');
                setcookie('nowplaying',$nowplaying);
                echo json_encode($re);
                break;
            case 'previous':
                if (!isset($_COOKIE['list'])) {
                    die();
                }
                //从cookie获取播放信息
                $song_id_list=json_decode($this->cookie2json($_COOKIE['list']),true);
                if (isset($_COOKIE['nowplaying'])) {
                    $nowplaying=(int)$_COOKIE['nowplaying'];
                } else {
                    $nowplaying=0;
                }
                $circle=$_COOKIE['circle'];
                $random=$_COOKIE['random'];
                if ($random==1) {
                    $nowplaying=rand(0,count($song_id_list)-1);
                } else {
                    $nowplaying--;
                }
                if (($nowplaying<0)&&($circle==1)) {
                    $nowplaying=count($song_id_list)-1;
                }
                $search['id']=$song_id_list[$nowplaying];
                $re=$song->where($search)->find();
                if ($re==null) {
                    setcookie('nowplaying',$nowplaying);
                    break;
                }
                $re['singer']=$guest->where('id='.$re['singerid'])->getField('nickname');
                setcookie('nowplaying',$nowplaying);
                echo json_encode($re);
                break;
            case 'choose'://ajax动作，用作选择歌曲播放,list为0表示全部列表，1表示收藏列表
                $songid=$this->_param('songid');
                $list=$this->_param('list');
                if (!isset($_COOKIE['list'])) {
                    $search['id']=$songid;
                    echo json_encode($song->where($search)->find());
                    die();
                }
                // $song_id_list=json_decode($this->cookie2json($_COOKIE['list']),true);
                
                $a=$this->check_session();
                if (($list==1)&&($a!=0)) {
                    $search['stuid']=$a['stuid'];
                    $guestinfo=$guest->where($search)->find();
                    unset($search);
                    $search['guestid']=$guestinfo['id'];
                    $song_id_list=$favorite->where($search)->getField('songid',true);
                    unset($search);
                } else {
                    $song_id_list=$song->getField('id',true);
                }
                setcookie('list',json_encode($song_id_list));
                $id=$list==1?'songid':'id';
                foreach ($song_id_list as $key => $value) {
                    if ($value[$id]==$songid) {
                        setcookie('nowplaying',$key);
                        $search['id']=$songid;
                        $re=$song->where($search)->find();
                        $re['singer']=$guest->where('id='.$re['singerid'])->getField('nickname');
                        echo json_encode($re);
                        die();
                    }
                }
                break;
            default:
                
                break;
         }
    }
    // public function ajax_getalllist(){
    
    //     $guest=M('guest');
    //     $song=M('song');
    //     $news=M('news');
    //     $favorite=M('favorite');
    //     $hotsong=M('hotsong');
    //     $hotsinger=M('hotsinger');
    //     $upload=M('upload');

    //     $song_id_list=$song->getField('id',true);
    //     shuffle($song_id_list);
    //     foreach ($song_id_list as $key => $value) {
    //         $search['id']=$value;
    //         $song_list[]=$song->where($search)->find();
    //         $song_list[$key]['singer']=$guest->where('id='.$song_list[$key]['singerid'])->getField('nickname');
    //     }
    //     unset($search);
    //     setcookie('list',json_encode($song_id_list));
    //     setcookie('nowplaying',-1);
    //     setcookie('random',0);
    //     setcookie('circle',0);
    //     echo json_encode($song_list);
    // }
    // public function ajax_getfavoritelist(){
    
    //     //验证是否登陆
    //     $a=$this->check_session();
    //     if ($a!=0) {

    //         $guest=M('guest');
    //         $song=M('song');
    //         $news=M('news');
    //         $favorite=M('favorite');
    //         $hotsong=M('hotsong');
    //         $hotsinger=M('hotsinger');
    //         $upload=M('upload');

    //         $search['stuid']=$a['stuid'];
    //         $guestinfo=$guest->where($search)->find();
    //         unset($search);
    //         $search['guestid']=$guestinfo['id'];
    //         $song_id_list=$favorite->where($search)->select();
    //         unset($search);
    //         foreach ($song_id_list as $key => $value) {
    //             $search['id']=$value['songid'];
    //             $song_list[]=$song->where($search)->find();
    //             $song_list[$key]['singer']=$guest->where('id='.$song_list[$key]['singerid'])->getField('nickname');
    //             //$search['id']被覆盖，不需要清空
    //         }
    //         unset($search);
    //         echo json_encode($song_list);
    //     } else {
    //         echo 0;
    //     }
    // }
    // public function ajax_getsonglist(){
    //     //数据库连接
    //     $guest=M('guest');
    //     $song=M('song');
    //     $favorite=M('favorite');
    //     //拉取所有已上传的歌曲
    //     $song_id_list=$song->getField('id',true);
    //     shuffle($song_id_list);
    //     foreach ($song_id_list as $key => $value) {
    //         $search['id']=$value;
    //         $song_list[]=$song->where($search)->find();
    //         $song_list[$key]['singer']=$guest->where('id='.$song_list[$key]['singerid'])->getField('nickname');
    //         //$search['id']被覆盖，不需要清空
    //     }
    //     unset($search);
    //     //die(print_r($song_list));
    //     $returnlist[]=$song_list;

    //     //验证是否登陆
    //     $a=$this->check_session();
    //     //登陆情况下获取收藏列表
    //     if ($a!=0) {
    //         $search['stuid']=$a['stuid'];
    //         $guestinfo=$guest->where($search)->find();
    //         unset($search);
    //         $search['guestid']=$guestinfo['id'];
    //         $favorite_id_list=$favorite->where($search)->select();
    //         unset($search);
    //         foreach ($favorite_id_list as $key => $value) {
    //             $search['id']=$value['songid'];
    //             $favorite_list[]=$song->where($search)->find();
    //             $song_list[$key]['singer']=$guest->where('id='.$song_list[$key]['singerid'])->getField('nickname');
    //             //$search['id']被覆盖，不需要清空
    //         }
    //         //die(var_dump($song_list));
    //         unset($search);
    //         $returnlist[]=$favorite_list;
    //     } else {
    //         $returnlist[]=0;
    //     }
    //     //设置cookie
    //     setcookie('list',json_encode($song_id_list));
    //     setcookie('nowplaying',0);
    //     setcookie('mode',1);

    //     echo json_encode($returnlist);
    // }
    // public function ajax_gettoplist(){
    // }
    public function ajax_changeList(){
    //ajax动作，用作切换播放列表
        //连接数据库
        $song=M('song');
        //获取操作
        $action=$this->_param('action');
        //从cookie获取当前列表信息
        $song_id_list=json_decode($this->cookie2json($_COOKIE['list']),true);
        $nowplaying=$song_id_list[$_COOKIE['nowplaying']];
        //切换播放列表
        if ($action=='order') {//以id排序
            for ($i=0;$i<count($song_id_list)-1;$i++) { 
                for ($j=$i+1;$j<count($song_id_list);$j++) {
                    if ($song_id_list[$i]['id']<$song_id_list[$j]['id']) {
                        $tmp=$song_id_list[$i];
                        $song_id_list[$i]=$song_id_list[$j];
                        $song_id_list[$j]=$tmp;
                    }
                }
            }
        } elseif ($action=='random') {//打乱顺序
            shuffle($song_id_list);
        }
        //获取歌曲信息
        foreach ($song_id_list as $key => $value) {
            $search['id']=$value['id'];
            $song_list[]=$song->where($search)->find();
            //$search['id']被覆盖，不需要清空
        }
        unset($search);
        //
        setcookie('nowplaying',array_search($nowplaying, $song_id_list));
        setcookie('list',json_encode($song_id_list));
        //die(var_dump($song_list));
        echo json_encode($song_list);
    }
    public function ajax_likeOrNot(){
    //ajax动作，用作判断歌曲是否已收藏
    //已收藏返回1，未收藏返回0
        //数据库连接
        $song=M('song');
        $guest=M('guest');
        $favorite=M('favorite');
        //检查是否已登陆，若未登录则跳转回首页
        $a=$this->check_session();
        if ($a==0) {
            $this->redirect('/Index/index');
            die();
        }
        //从数据库获取歌手信息
        $search['stuid']=$a['stuid'];
        $guestinfo=$guest->where($search)->find();
        unset($search);
        //在favorite中查找记录
        $search['guestid']=$guestinfo['id'];
        $search['songid']=$this->_param('songid');
        $exist=$favorite->where($search)->find();
        unset($search);
        //判断是否存在于收藏列表
        if ($exist) {
            echo 1;
        } elseif (!$exist) {
            echo 0;
        }
    }
    public function ajax_like(){
    //ajax动作，用作收藏/取消收藏歌曲
    //若新加了收藏，则返回1；若删除收藏，则返回0
        //数据库连接
        $guest=M('guest');
        $song=M('song');
        $news=M('news');
        $favorite=M('favorite');
        $hotsong=M('hotsong');
        $hotsinger=M('hotsinger');
        //检查是否已登陆，若未登录则跳转回首页
        $a=$this->check_session();
        if ($a==0) {
            $this->redirect('/Index/index');
            die();
        }
        //从cookie获取播放信息
        $action=$this->_param('action');
        $song_id_list=json_decode($this->cookie2json($_COOKIE['list']),true);
        //从数据库拉取信息
        $search['id']=$song_id_list[$_COOKIE['nowplaying']];
        $songinfo=$song->where($search)->find();
        unset($search);
        $search['stuid']=$a['stuid'];
        $guestinfo=$guest->where($search)->find();
        unset($search);
        //查询是否已经收藏该歌曲
        $search['guestid']=$guestinfo['id'];
        $search['songid']=$songinfo['id'];
        $exist=$favorite->where($search)->find();
        unset($search);
        //收藏/取消收藏操作
        if (($action='like')&&(!$exist)) {
            //歌手是否被收藏
            $search['guestid']=$guestinfo['id'];
            $search['singerid']=$songinfo['singerid'];
            $find=$favorite->where($search)->find();
            unset($search);
            //若从未收藏过该歌手的歌，则该歌手热门度+1
            if (!$find) {
                $search['singerid']=$songinfo['singerid'];
                $hotsinger->where($search)->setInc('mark');
                unset($search);
            }
            //添加到收藏列表
            $data['guestid']=$guestinfo['id'];
            $data['songid']=$songinfo['id'];
            $data['singerid']=$songinfo['singerid'];
            $favorite->add($data);
            unset($data);
            //歌曲被收藏数+1
            $search['songid']=$songinfo['id'];
            $hotsong->where($search)->setInc('mark');
            unset($search);
            //添加到动态
            $data['type']=3;
            $data['stuid']=$guestinfo['stuid'];
            $data['songid']=$songinfo['id'];
            $data['singerid']=$songinfo['singerid'];
            $data['guestid']=$guestinfo['id'];
            $news->add($data);
            unset($data);
            echo 1;
        } elseif (($action='dislike')&&($exist)) {
            //从收藏夹删除
            $search['guestid']=$guestinfo['id'];
            $search['songid']=$songinfo['id'];
            $favorite->where($search)->delete();
            unset($search);
            //歌曲被收藏数-1
            $search['songid']=$songinfo['id'];
            $hotsong->where($search)->setDec('mark');
            unset($search);
            //从动态中删除
            $search['type']=3;
            $search['guestid']=$guestinfo['id'];
            $search['songid']=$songinfo['id'];
            $news->where($search)->delete();
            unset($search);
            //歌手是否被收藏
            $search['guestid']=$guestinfo['id'];
            $search['singerid']=$songinfo['singerid'];
            $find=$favorite->where($search)->find();
            unset($search);
            //操作后若没有收藏该歌手的歌，则歌手热门度-1
            if (!$find) {
                $search['singerid']=$songinfo['singerid'];
                $hotsinger->where($search)->setDec('mark');
                unset($search);
            }
            echo 0;
        }

        // //获取更新之后的收藏列表
        // $search['guestid']=$guestinfo['id'];
        // $favorite_id_list=$favorite->where($search)->select();
        // unset($search);
        // //获取收藏歌曲的信息
        // foreach ($favorite_id_list as $key => $value) {
        //     $search['id']=$value['songid'];
        //     $favorite_list[]=$song->where($search)->find();
        //     //由于$search['id']的值被覆盖，这边不需要清空$search数组
        // }
        // unset($search);
        // echo json_encode($favorite_list);
    }
    public function ajax_get(){
    //ajax动作，用作获取歌曲
        //连接数据库
        $guest=M('guest');
        $song=M('song');
        //非听歌页面时没有list，初始状态为空
        if (!isset($_COOKIE['list'])) {
            die();
        }
        //从cookie获取播放信息
        $song_id_list=json_decode($this->cookie2json($_COOKIE['list']),true);
        if (isset($_COOKIE['nowplaying'])) {
            $nowplaying=(int)$_COOKIE['nowplaying'];
        } else {
            $nowplaying=0;
        }
        $nowplaying++;
        // //根据播放模式来获得下一首歌的id
        // switch ($_COOKIE['mode']) {
        //     case '3'://single模式id不变
        //         break;
        //     case '2'://random模式随机获得一个id
        //         $nowplaying=rand(0,count($song_id_list)-1);
        //         break;
        //     case '1'://order模式id+1
        //         if (($nowplaying>=count($song_id_list)-1)||($nowplaying<0)) {
        //             $nowplaying=0;
        //         } else {
        //             $nowplaying++;
        //         }
        //         break;
        //     default:
        //         $nowplaying=0;
        //         break;
        // }
        //设置cookie
        setcookie('nowplaying',$nowplaying);
        $search['id']=$song_id_list[$nowplaying];
        $re=$song->where($search)->find();
        $re['singer']=$guest->where('id='.$re['singerid'])->getField('nickname');
        echo json_encode($re);
    }
    public function ajax_changemode(){
    //ajax动作，用作改变播放模式
    //1 for order, 2 for random, 3 for single
        if (isset($_COOKIE['mode'])) {
            $mode=fmod($_COOKIE['mode'],3)+1;
        } else {
            $mode=1;
        }
        //设置cookie
        setcookie('mode',$mode);
        switch ($mode) {
            case '1':
                echo 'order';
                break;
            case '2':
                echo 'random';
                break;
            case '3':
                echo 'single';
                break;
            default:
                echo 'order';
                break;
        }
    }
    public function ajax_choose(){
    //ajax动作，用作选择歌曲播放
        $song=M('song');
        $guest=M('guest');
        $songid=$this->_param('songid');
        if (!isset($_COOKIE['list'])) {
            $search['id']=$songid;
            echo json_encode($song->where($search)->find());
            die();
        }
        $song_id_list=json_decode($this->cookie2json($_COOKIE['list']),true);
        foreach ($song_id_list as $key => $value) {
            if ($value['id']==$songid) {
                setcookie('nowplaying',$key);
                $search['id']=$songid;
                $re=$song->where($search)->find();
                $re['singer']=$guest->where('id='.$re['singerid'])->getField('nickname');
                echo json_encode($re);
                die();
            }
        }
    }
    public function ajax_view(){
        $guest=M('guest');
        $favorite=M('favorite');
        $song=M('song');
        $uid=$this->_param('uid');
        $a=$this->check_session();
        if ($a==0) {
            $this->redirect('/Index/index');
            die();
        }
        $tmp[]=$guest->where('id='.$uid)->find();
        $tmp[0]['manage']=$guest->where('stuid='.$a['stuid'])->getField('id');
        
        $search['guestid']=$uid;
        $favorite_id_list=$favorite->where($search)->select();
        unset($search);
        foreach ($favorite_id_list as $key => $value) {
            $search['id']=$value['songid'];
            $favorite_list[]=$song->where($search)->find();
            $favorite_list[$key]['singer']=$guest->where('id='.$favorite_list[$key]['singerid'])->getField('nickname');
            //$search['id']被覆盖，不需要清空
        }
        //die(var_dump($song_list));
        unset($search);
        $tmp[]=$favorite_list;

        echo json_encode($tmp);
    }
    // public function ajax_upload(){
    //     $error = "";
    //     $msg = "";
    //     $fileElementName = 'fileToUpload';
    //     if(!empty($_FILES[$fileElementName]['error']))
    //     {
    //         switch($_FILES[$fileElementName]['error'])
    //         {

    //             case '1':
    //                 $error = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
    //                 break;
    //             case '2':
    //                 $error = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
    //                 break;
    //             case '3':
    //                 $error = 'The uploaded file was only partially uploaded';
    //                 break;
    //             case '4':
    //                 $error = 'No file was uploaded.';
    //                 break;

    //             case '6':
    //                 $error = 'Missing a temporary folder';
    //                 break;
    //             case '7':
    //                 $error = 'Failed to write file to disk';
    //                 break;
    //             case '8':
    //                 $error = 'File upload stopped by extension';
    //                 break;
    //             case '999':
    //             default:
    //                 $error = 'No error code avaiable';
    //         }
    //     }elseif(empty($_FILES['fileToUpload']['tmp_name']) || $_FILES['fileToUpload']['tmp_name'] == 'none')
    //     {
    //         $error = 'No file was uploaded..';
    //     }else 
    //     {
    //             $msg .= " File Name: " . $_FILES['fileToUpload']['name'] . ", ";
    //             $msg .= " File Size: " . @filesize($_FILES['fileToUpload']['tmp_name']);
    //             //for security reason, we force to remove all uploaded file
    //             @unlink($_FILES['fileToUpload']);       
    //     }



    //     echo "{";
    //     echo                "error: '" . $error . "',\n";
    //     echo                "msg: '" . $msg . "'\n";
    //     echo "}";
    // //公共函数，用作上传歌曲提交
    //     //数据库连接
    //     $guest=M('guest');
    //     $song=M('song');
    //     $news=M('news');
    //     $upload=M('upload');
    //     $hotsinger=M('hotsinger');
    //     $hotsong=M('hotsong');
    //     //验证是否登陆，若未登陆则返回首页
    //     $a=$this->check_session();
    //     if ($a==0) {
    //         $this->redirect('/Index/index');
    //         die();
    //     }
    //     //获取用户信息
    //     $search['stuid']=$a['stuid'];
    //     $guestinfo=$guest->where($search)->find();
    //     unset($search);
    //     //上传歌曲文件
    //     import("ORG.Net.UploadFile");
    //     $uploadfile=new UploadFile;
    //     $uploadfile->savePath='./Upload/song/';
    //     $uploadfile->saveRule='uniqid';
    //     $uploadfile->maxSize=20971520;
    //     $uploadfile->allowExts=array('mp3');
    //     if (!$uploadfile->upload()) {
    //         $this->error($uploadfile->getErrormsg());
    //     } else {
    //         $uploadinfo=$uploadfile->getUploadFileInfo();
    //     }
    //     //验证重复文件
    //     $search['singerid']=$guestinfo['id'];
    //     $search['md5']=$uploadinfo[0]['hash'];
    //     if ($song->where($search)->find()) {
    //         $this->error('你已经传了这首歌了呦！');
    //     }
    //     unset($search);
    //     //添加数据库记录
    //     $done=1;
    //     //添加song
    //     $data['stuid']=$guestinfo['stuid'];
    //     $data['name']=$this->_param('songname');
    //     $data['singer']=$guestinfo['nickname'];
    //     $data['singerid']=$guestinfo['id'];
    //     $data['message']=nl2br($this->_param('message'));
    //     $data['address']=$uploadinfo[0]['savename'];
    //     $data['md5']=$uploadinfo[0]['hash'];
    //     $songid=$song->add($data);
    //     if (!$songid) {
    //         $done=0;
    //     }
    //     unset($data);
    //     //添加到upload
    //     $data['singerid']=$guestinfo['id'];
    //     $data['songid']=$songid;
    //     if (!$upload->add($data)) {
    //         $done=0;
    //     }
    //     unset($data);
    //     //添加到hotsong
    //     $data['songid']=$songid;
    //     if (!$hotsong->add($data)) {
    //         $done=0;
    //     }
    //     unset($data);
    //     //添加到hotsinger
    //     $search['singerid']=$guestinfo['id'];
    //     if (!$hotsinger->where($search)->find()) {
    //         $data['singerid']=$guestinfo['id'];
    //         if (!$hotsinger->add($data)) {
    //             $done=0;
    //         }
    //         unset($data);
    //     }
    //     unset($search);
    //     //添加到news
    //     $data['type']=2;
    //     $data['stuid']=$guestinfo['stuid'];
    //     $data['songid']=$songid;
    //     $data['singerid']=$guestinfo['id'];
    //     if (!$news->add($data)) {
    //         $done=0;
    //     }
    //     unset($data);

    //     if ($done==1) {
    //         $this->redirect('/Index/index');
    //     } else{
    //         $this->error('上传失败');
    //     }
    // }

    public function ajax_search(){
    //ajax动作，用作搜索
        //数据库连接
        $song=M('song');
        $guest=M('guest');
        //检查是否已登陆，若未登录则跳转回首页
        $a=$this->check_session();
        if ($a==0) {
            $this->redirect('/Index/index');
            die();
        }
        //获取参数
        $target=$this->_param('target');
        $content='%'.$this->_param('content').'%';
        switch ($target) {
            case 'songname':
                $search['name']=array('like',$content);
                foreach ($song->where($search)->select() as $key => $value) {
                    $result[]="<a href='' onclick='choose(".$value['id'].");return false'>".$value['name']."</a>[BY]<a href='/fifth/voice/index.php/Index/view?guestid=".$value['singerid']."'>".$value['singer']."</a>";
                }
                unset($search);
                break;
            case 'nickname':
                $search['nickname']=array('like',$content);
                foreach ($guest->where($search)->select() as $key => $value) {
                    $result[]="<a href='/fifth/voice/index.php/Index/view?guestid=".$value['id']."'>".$value['nickname']."</a>";
                }
                unset($search);
                break;
            case 'constellation':
                $search['constellation']=array('like',$content);
                foreach ($guest->where($search)->select() as $key => $value) {
                    $result[]="[".$value['constellation']."]<a href='/fifth/voice/index.php/Index/view?guestid=".$value['id']."'>".$value['nickname']."</a>";
                }
                unset($search);
                break;
            case 'sex':
                $search['sex']=array('like',$content);
                foreach ($guest->where($search)->select() as $key => $value) {
                    $result[]="[".$value['sex']."]<a href='/fifth/voice/index.php/Index/view?guestid=".$value['id']."'>".$value['nickname']."</a>";
                }
                unset($search);
                break;
            default:
                $rasult[]='好的嘛，找不到呦XD';
                break;
        }
        if (!$result) {
            $rasult[]='好的嘛，找不到呦XD';
        }
        echo json_encode($result);
    }
    public function index(){
    //共有函数，用作首页信息展示
        //数据库连接
        $guest=M('guest');
        $news=M('news');
        $song=M('song');
        //验证是否登陆
        $a=$this->check_session();
        if ($a!=0) {
            $search['stuid']=$a['stuid'];
            $guestinfo=$guest->where($search)->find();
            unset($search);
            $this->assign('name',$guestinfo['nickname']);
            $this->assign('view',$guestinfo['id']);
            $this->assign('logout',ROOT_URL.U('/Index/logout').'?redirect='.ROOT_URL.$_SERVER['REQUEST_URI']);
        } else {
            $this->assign('passport',PP_RD.ROOT_URL.U('/Index/passport'));
            $this->assign('notlogin',1);
        }
        // //获取动态
        // $tmp_news=$news->order('date desc')->limit(10)->select();
        // foreach ($tmp_news as $key => $value) {
        //     $search['id']=$value['guestid'];
        //     $tmp_guestinfo=$guest->where($search)->find();
        //     //$search['id']被覆盖，无需清空
        //     $search['id']=$value['songid'];
        //     $tmp_songinfo=$song->where($search)->find();
        //     unset($search);
        //     $news_list[]=array(
        //         'type'=>$value['type'],
        //         'guestid'=>$value['guestid'],
        //         'songid'=>$value['songid'],
        //         'singerid'=>$value['singerid'],
        //         'guestname'=>$tmp_guestinfo['nickname'],
        //         'songname'=>$tmp_songinfo['name'],
        //         'singername'=>$tmp_songinfo['singer']
        //         );
        //     unset($tmp_guestinfo);
        //     unset($tmp_songinfo);
        // }
        // $this->assign('index',1);
        // $this->assign('news_list',$news_list);
        $this->display();
    }
    public function listen(){
    //共有函数，用作听歌页面信息展示
        //数据库连接
        $guest=M('guest');
        $song=M('song');
        $favorite=M('favorite');
        //验证是否登陆
        $a=$this->check_session();
        if ($a!=0) {
            $search['stuid']=$a['stuid'];
            $guestinfo=$guest->where($search)->find();
            unset($search);
            $this->assign('name',$guestinfo['nickname']);
            $this->assign('view',ROOT_URL.U('/Index/view').'?guestid='.$guestinfo['id']);
            $this->assign('logout',ROOT_URL.U('/Index/logout').'?redirect='.ROOT_URL.$_SERVER['REQUEST_URI']);
        } else {
            $this->assign('passport',PP_RD.ROOT_URL.U('Index/passport'));
            $this->assign('notlogin',1);
        }
        //拉取所有已上传的歌曲
        $song_id_list=$song->getField('id',true);
        shuffle($song_id_list);
        foreach ($song_id_list as $key => $value) {
            $search['id']=$value['id'];
            $song_list[]=$song->where($search)->find();
            //$search['id']被覆盖，不需要清空
        }
        unset($search);
        //登陆情况下获取收藏列表
        if ($a!=0) {
            $search['guestid']=$guestinfo['id'];
            $favorite_id_list=$favorite->where($search)->select();
            unset($search);
            foreach ($favorite_id_list as $key => $value) {
                $search['id']=$value['songid'];
                $favorite_list[]=$song->where($search)->find();
                //$search['id']被覆盖，不需要清空
            }
            unset($search);
            $this->assign('favorite_list',$favorite_list);
        }
        //设置cookie
        setcookie('list',json_encode($song_id_list));
        setcookie('nowplaying',-1);
        setcookie('mode',1);

        $this->assign('song_list',$song_list);
        $this->display();
    }
    public function upload(){
    //公有函数，用作传歌页面信息展示
        //数据库连接
        $guest=M('guest');
        $upload=M('upload');
        //验证是否登陆，若未登陆则返回首页
        $a=$this->check_session();
        if ($a==0) {
            $this->redirect('/Index/index');
            die();
        }
        //获取用户信息
        $search['stuid']=$a['stuid'];
        $guestinfo=$guest->where($search)->find();
        unset($search);
        //获取用户上传的歌曲数量
        $search['singerid']=$guestinfo['id'];
        $num=$upload->where($search)->count();

        $this->assign('name',$guestinfo['nickname']);
        $this->assign('view',ROOT_URL.U('/Index/view').'?guestid='.$guestinfo['id']);
        $this->assign('logout',ROOT_URL.U('/Index/logout').'?redirect='.ROOT_URL.$_SERVER['REQUEST_URI']);
        $this->assign('num',$num);
        $this->assign('newnum',$num+1);
        $this->display();
    }
    public function upload_submit(){
    //公共函数，用作上传歌曲提交
        //数据库连接
        $guest=M('guest');
        $song=M('song');
        $news=M('news');
        $upload=M('upload');
        $hotsinger=M('hotsinger');
        $hotsong=M('hotsong');
        //验证是否登陆，若未登陆则返回首页
        $a=$this->check_session();
        if ($a==0) {
            $this->redirect('/Index/index');
            die();
        }
        //获取用户信息
        $search['stuid']=$a['stuid'];
        $guestinfo=$guest->where($search)->find();
        unset($search);
        //上传歌曲文件
        import("ORG.Net.UploadFile");
        $uploadfile=new UploadFile;
        $uploadfile->savePath='./Upload/song/';
        $uploadfile->saveRule='uniqid';
        $uploadfile->maxSize=20971520;
        $uploadfile->allowExts=array('mp3');
        if (!$uploadfile->upload()) {
            $this->error($uploadfile->getErrormsg());
        } else {
            $uploadinfo=$uploadfile->getUploadFileInfo();
        }
        //验证重复文件
        $search['singerid']=$guestinfo['id'];
        $search['md5']=$uploadinfo[0]['hash'];
        if ($song->where($search)->find()) {
            $this->error('你已经传了这首歌了呦！');
        }
        unset($search);
        //添加数据库记录
        $done=1;
        //添加song
        $data['stuid']=$guestinfo['stuid'];
        $data['name']=$this->_param('songname');
        $data['singer']=$guestinfo['nickname'];
        $data['singerid']=$guestinfo['id'];
        $data['message']=nl2br($this->_param('message'));
        $data['address']=$uploadinfo[0]['savename'];
        $data['md5']=$uploadinfo[0]['hash'];
        $songid=$song->add($data);
        if (!$songid) {
            $done=0;
        }
        unset($data);
        //添加到upload
        $data['singerid']=$guestinfo['id'];
        $data['songid']=$songid;
        if (!$upload->add($data)) {
            $done=0;
        }
        unset($data);
        //添加到hotsong
        $data['songid']=$songid;
        if (!$hotsong->add($data)) {
            $done=0;
        }
        unset($data);
        //添加到hotsinger
        $search['singerid']=$guestinfo['id'];
        if (!$hotsinger->where($search)->find()) {
            $data['singerid']=$guestinfo['id'];
            if (!$hotsinger->add($data)) {
                $done=0;
            }
            unset($data);
        }
        unset($search);
        //添加到news
        $data['type']=2;
        $data['stuid']=$guestinfo['stuid'];
        $data['songid']=$songid;
        $data['singerid']=$guestinfo['id'];
        if (!$news->add($data)) {
            $done=0;
        }
        unset($data);

        if ($done==1) {
            $this->success('上传成功');
        } else{
            $this->error('上传失败');
        }
    }

    //——————————————————————————————————————

    public function view(){
    //公有函数，用作浏览个人页面展示
        //连接数据库
        $guest=M('guest');
        $song=M('song');
        $favorite=M('favorite');
        $upload=M('upload');
        $hotsong=M('hotsong');
        $hotsinger=M('hotsinger');
        //检查是否已登陆，若未登录则跳转回首页
        $a=$this->check_session();
        if ($a==0) {
            $this->redirect('/Index/index');
            die();
        }
        //清除cookie
        setcookie('list','');
        setcookie('mode','');
        setcookie('nowplaying','');
        //获取用户信息
        $search['stuid']=$a['stuid'];
        $guestinfo=$guest->where($search)->find();
        unset($search);
        $this->assign('name',$guestinfo['nickname']);
        $this->assign('view',ROOT_URL.U('/Index/view').'?guestid='.$guestinfo['id']);
        $this->assign('logout',ROOT_URL.U('/Index/logout').'?redirect='.ROOT_URL.U('/Index/index'));
        //获取浏览对象的信息
        $guestid=$this->_param('guestid');
        if ($guestinfo['id']==$guestid) {
            $this->assign('manage',1);
        }//管理自己的页面
        $search['id']=$guestid;
        $info=$guest->where($search)->find();
        unset($search);
        //修改称谓
        if ($info['id']==$guestinfo['id']) {
            $TA='我';
        } elseif ($info['sex']=='男') {
            $TA='他';
        } else if ($info['sex']=='女') {
            $TA='她';
        } else {
            $TA='TA';
        }
        $this->assign('TA',$TA);
        $this->assign('info',$info);
        //获取上传的歌曲信息
        $search['singerid']=$info['id'];
        $upload_id_list=$upload->where($search)->select();
        unset($search);
        foreach ($upload_id_list as $key => $value) {
            $search['id']=$value['songid'];
            $upload_list[]=$song->where($search)->find();
            //$search['id']被覆盖，无需清空
        }
        unset($search);
        foreach ($upload_list as $key => $value) {
            $search['songid']=$value['id'];
            $upload_list[$key]['mark']=$hotsong->where($search)->getField('mark');
            //$search['songid']被覆盖，无需清空
        }
        unset($search);
        //获取歌手热门度
        $search['singerid']=$info['id'];        
        $this->assign('hotsinger',$hotsinger->where($search)->getField('mark'));
        unset($search);
        $this->assign('upload_list',$upload_list);
        $this->assign('num_upload',count($upload_list));
        //获取收藏的歌曲
        $search['guestid']=$info['id'];
        $favorite_id_list=$favorite->where($search)->select();
        unset($search);
        foreach ($favorite_id_list as $key => $value) {
            $search['id']=$value['songid'];
            $favorite_list[]=$song->where($search)->find();
            //$search['id']被覆盖，无需清空
        }
        $this->assign('favorite_list',$favorite_list);
        $this->assign('num_favorite',count($favorite_list));
        $this->display();
    }
    public function view_submit(){
    //公有函数，用作提交修改个人资料
        //连接数据库
        $guest=M('guest');
        $song=M('song');
        //验证是否登陆，若未登陆则返回首页
        $a=$this->check_session();
        if ($a==0) {
            $this->redirect('/Index/index');
            die();
        }
        //上传文件
        import("ORG.Net.UploadFile");
        $uploadfile=new UploadFile;
        $uploadfile->savePath='./Upload/portraits/';
        $uploadfile->saveRule='uniqid';
        $uploadfile->maxSize=20971520;
        $uploadfile->allowExts=array('jpg','png','gif','jpeg');
        if ($uploadfile->upload()) {
            $uploadinfo=$uploadfile->getUploadFileInfo();
        } elseif ($uploadfile->getErrormsg()!='没有选择上传文件') {
            $this->error($uploadfile->getErrormsg());
        }
        //更新数据库记录
        $search['stuid']=$a['stuid'];
        $guestinfo=$guest->where($search)->find();
        unset($search);
        if ($this->_param('nickname')) {
            $guestinfo['nickname']=$this->_param('nickname');
        }
        if ($this->_param('sex')) {
            $guestinfo['sex']=$this->_param('sex');
        }
        if ($this->_param('constellation')) {
            $guestinfo['constellation']=$this->_param('constellation');
        }
        if ($uploadinfo[0]['savename']) {
            $guestinfo['portraits']=$uploadinfo[0]['savename'];
        }
        $guestinfo['email']=$this->_param('email');
        $guestinfo['signature']=$this->_param('signature');
        if ($guest->save($guestinfo)) {
            //修改歌曲中的昵称
            $search['singerid']=$guestinfo['id'];
            $singername=$song->where($search)->select();
            unset($search);
            foreach ($singername as $key => $value) {
                $value['singer']=$guestinfo['nickname'];
            }
            $song->save($singername);
            $this->redirect('/Index/index');
        } else {
            $this->error('修改失败');
        }
    }
    public function search(){
    //公有函数，用作搜索页面信息展示
        //连接数据库
        $guest=M('guest');
        //验证是否登陆，若未登陆则返回首页
        $a=$this->check_session();
        if ($a==0) {
            $this->redirect('/Index/index');
            die();
        }
        //清除cookie
        setcookie('list','');
        setcookie('mode','');
        setcookie('nowplaying','');
        //获取用户数据
        $search['stuid']=$a['stuid'];
        $guestinfo=$guest->where($search)->find();
        $this->assign('name',$guestinfo['nickname']);
        $this->assign('view',ROOT_URL.U('/Index/view').'?guestid='.$guestinfo['id']);
        $this->assign('logout',ROOT_URL.U('/Index/logout').'?redirect='.ROOT_URL.U('/Index/index'));
        $this->display();
    }
    public function top(){
    //公有函数，用作排行榜信息展示
        //数据库连接
        $guest=M('guest');
        $song=M('song');
        $hotsong=M('hotsong');
        $hotsinger=M('hotsinger');
        //清除cookie
        setcookie('list','');
        setcookie('mode','');
        setcookie('nowplaying','');
        //验证是否登陆
        $a=$this->check_session();
        if ($a!=0) {
            $search['stuid']=$a['stuid'];
            $guestinfo=$guest->where($search)->find();
            unset($search);
            $this->assign('name',$guestinfo['nickname']);
            $this->assign('logout',ROOT_URL.U('Index/logout').'?redirect='.ROOT_URL.$_SERVER['REQUEST_URI']);
            $this->assign('view',ROOT_URL.U('/Index/view').'?guestid='.$guestinfo['id']);
        } else {
            $this->assign('passport',PP_RD.ROOT_URL.U('/Index/passport?listen=1'));
            $this->assign('notlogin',1);
        }
        //获取热门歌曲信息
        $hotsong_id_list=$hotsong->order('mark desc,id asc')->select();
        foreach ($hotsong_id_list as $key => $value) {
            $search['id']=$value['songid'];
            $hotsong_list[]=$song->where($search)->find();
            //$search['id']被覆盖，无需清空
        }
        unset($search);
        foreach ($hotsong_list as $key => $value) {
            $search['songid']=$value['id'];
            $hotsong_list[$key]['mark']=$hotsong->where($search)->getField('mark');
            //$search['songid']被覆盖，无需清空
        }
        unset($search);
        $this->assign('hotsong_list',$hotsong_list);
        //获取热门歌手信息
        $hotsinger_id_list=$hotsinger->order('mark desc,id asc')->select();
        foreach ($hotsinger_id_list as $key => $value) {
            $search['id']=$value['singerid'];
            $hotsinger_list[]=$guest->where($search)->find();
            //$search['id']被覆盖，无需清空
        }
        unset($search);
        foreach ($hotsinger_list as $key => $value) {
            $search['singerid']=$value['id'];
            $hotsinger_list[$key]['mark']=$hotsinger->where($search)->getField('mark');
            //$search['songid']被覆盖，无需清空
        }
        unset($search);
        $this->assign('hotsinger_list',$hotsinger_list);
        $this->display();
    }
    public function help(){
    //公有函数，用作帮助页面信息展示
        //数据库连接
        $guest=M('guest');
        //验证是否登陆
        $a=$this->check_session();
        if ($a!=0) {
            $search['stuid']=$a['stuid'];
            $guestinfo=$guest->where($search)->find();
            unset($search);
            $this->assign('name',$guestinfo['nickname']);
            $this->assign('view',ROOT_URL.U('/Index/view').'?guestid='.$guestinfo['id']);
            $this->assign('logout',ROOT_URL.U('/Index/logout').'?redirect='.ROOT_URL.$_SERVER['REQUEST_URI']);
        } else {
            $this->assign('passport',PP_RD.ROOT_URL.U('/Index/passport'));
            $this->assign('notlogin',1);
        }
        $this->display();
    }
    public function feedback(){
    //公有函数，用作反馈页面信息展示
        //数据库连接
        $guest=M('guest');
        //验证是否登陆
        $a=$this->check_session();
        if ($a==0) {
            $this->redirect('/Index/index');
            die();
        }
        if ($a!=0) {
            $search['stuid']=$a['stuid'];
            $guestinfo=$guest->where($search)->find();
            unset($search);
            $this->assign('name',$guestinfo['nickname']);
            $this->assign('view',ROOT_URL.U('/Index/view').'?guestid='.$guestinfo['id']);
            $this->assign('logout',ROOT_URL.U('/Index/logout').'?redirect='.ROOT_URL.$_SERVER['REQUEST_URI']);
        } else {
            $this->assign('passport',PP_RD.ROOT_URL.U('/Index/passport'));
            $this->assign('notlogin',1);
        }
        $this->display();
    }
    public function feedback_submit(){
    //公有函数，用作反馈信息提交
        //数据库连接
        $feedback=M('feedback');
        //验证是否登陆，若未登陆则返回首页
        $a=$this->check_session();
        if ($a==0) {
            $this->redirect('/Index/index');
            die();
        }
        $content=$this->_param('content');
        if ($content) {
            $data['stuid']=$a['stuid'];
            $data['username']=$a['username'];
            $data['content']=$content;
            $data['phone']=$this->_param('phone');
            $data['email']=$this->_param('email');
            if ($feedback->add($data)) {
                mail('523234748@qq.com','voice_feedback',$content);
                $this->redirect('/Index/index');
            } else {
                $this->error('提交失败');
            }
        } else {
            $this->error('enter message please');
        }        
    }
}
?>