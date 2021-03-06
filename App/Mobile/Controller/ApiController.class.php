<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 伊媒微时代 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Mobile\Controller;

use Think\Controller;
use Mobile\Com\Wechat;
use Mobile\Com\WechatAuth;

class ApiController extends Controller{
    /**
     * 微信消息接口入口
     * 所有发送到微信的消息都会推送到该操作
     * 所以，微信公众平台后台填写的api地址则为该操作的访问地址
     */
    public function index($id = ''){
        //调试
        try{
            $appid =C('APPID'); //AppID(应用ID)
            $token = C('TOKEN'); //微信后台填写的TOKEN
            $crypt = C('AESKEY'); //消息加密KEY（EncodingAESKey）
            
            /* 加载微信SDK */
            $wechat = new Wechat($token, $appid, $crypt);
            
            /* 获取请求信息 */
            $data = $wechat->request();

            if($data && is_array($data)){
                /**
                 * 你可以在这里分析数据，决定要返回给用户什么样的信息
                 * 接受到的信息类型有10种，分别使用下面10个常量标识
                 * Wechat::MSG_TYPE_TEXT       //文本消息
                 * Wechat::MSG_TYPE_IMAGE      //图片消息
                 * Wechat::MSG_TYPE_VOICE      //音频消息
                 * Wechat::MSG_TYPE_VIDEO      //视频消息
                 * Wechat::MSG_TYPE_SHORTVIDEO //视频消息
                 * Wechat::MSG_TYPE_MUSIC      //音乐消息
                 * Wechat::MSG_TYPE_NEWS       //图文消息（推送过来的应该不存在这种类型，但是可以给用户回复该类型消息）
                 * Wechat::MSG_TYPE_LOCATION   //位置消息
                 * Wechat::MSG_TYPE_LINK       //连接消息
                 * Wechat::MSG_TYPE_EVENT      //事件消息
                 *
                 * 事件消息又分为下面五种
                 * Wechat::MSG_EVENT_SUBSCRIBE    //订阅
                 * Wechat::MSG_EVENT_UNSUBSCRIBE  //取消订阅
                 * Wechat::MSG_EVENT_SCAN         //二维码扫描
                 * Wechat::MSG_EVENT_LOCATION     //报告位置
                 * Wechat::MSG_EVENT_CLICK        //菜单点击
                 */

                //记录微信推送过来的数据
                file_put_contents('./Public/Mobile/Data/data.json', json_encode($data));

                /* 响应当前请求(自动回复) */
                //$wechat->response($content, $type);

                /**
                 * 响应当前请求还有以下方法可以使用
                 * 具体参数格式说明请参考文档
                 * 
                 * $wechat->replyText($text); //回复文本消息
                 * $wechat->replyImage($media_id); //回复图片消息
                 * $wechat->replyVoice($media_id); //回复音频消息
                 * $wechat->replyVideo($media_id, $title, $discription); //回复视频消息
                 * $wechat->replyMusic($title, $discription, $musicurl, $hqmusicurl, $thumb_media_id); //回复音乐消息
                 * $wechat->replyNews($news, $news1, $news2, $news3); //回复多条图文消息
                 * $wechat->replyNewsOnce($title, $discription, $url, $picurl); //回复单条图文消息
                 * 
                 */
                
                //执行Demo
                $this->demo($wechat, $data);
            }
        } catch(\Exception $e){
            file_put_contents('./Public/Mobile/Data/error.json', json_encode($e->getMessage()));
        }
        
    }

    /**
     * DEMO
     * @param  Object $wechat Wechat对象
     * @param  array  $data   接受到微信推送的消息
     */
    private function demo($wechat, $data){
        switch ($data['MsgType']) {
            //事件
            case Wechat::MSG_TYPE_EVENT:
                switch ($data['Event']) {
                    case Wechat::MSG_EVENT_SUBSCRIBE:
                        //关注
                    	$contentStr = "感谢您关注【伊媒微时代】"."\n"."微信号：selanyimei"."\n"."一家专注于做穆斯林服务的公众平台！".
                    	"\n"."目前平台功能如下："."\n"."【1】 查天气，如输入：银川天气"."\n"."【2】 听音乐，如输入：听歌兰花花"."\n".
                    	"【3】 翻译，如输入：翻译I love you"."\n".
                    	"【4】 看新闻，如输入：新闻"."\n"."更多内容，敬请期待...";
                        $wechat->replyText($contentStr);//."【2】 查公交，如输入：银川公交101"."\n"
                        break;

                    case Wechat::MSG_EVENT_UNSUBSCRIBE:
                        //取消关注，记录日志
                    	addlog('取消关注：'.$aids);
                    	$replyMsg = "再见！朋友！";
                    	$replyXml = "<xml>
								<ToUserName><![CDATA[%s]]></ToUserName>
								<FromUserName><![CDATA[%s]]></FromUserName>
								<CreateTime>%s</CreateTime>
								<MsgType><![CDATA[text]]></MsgType>
								<Content><![CDATA[%s]]></Content>
								</xml>";
                        break;

                    case Wechat::MSG_EVENT_SCAN:
                        //扫描二维码
                    	$replyMsg = "再见！朋友！";
                    	$replyXml = "<xml>
								<ToUserName><![CDATA[%s]]></ToUserName>
								<FromUserName><![CDATA[%s]]></FromUserName>
								<CreateTime>%s</CreateTime>
								<MsgType><![CDATA[text]]></MsgType>
								<Content><![CDATA[%s]]></Content>
								</xml>";
                        break;

                    default:
                    	$contentStr = "欢迎访问伊媒微时代公众平台"."\n"."微信号：selanyimei"."\n"."一家专注于做穆斯林服务的公众平台！".
                    			"\n"."可体验以下功能："."\n"."【1】 查天气，如输入：银川天气"."\n"."【2】 听音乐，如输入：听歌兰花花"."\n".
                    			"【3】 翻译，如输入：翻译I love you"."\n".
                    			"【4】看新闻，如输入：新闻"."\n"."更多内容，敬请期待...";
                        $wechat->replyText($contentStr);
                        break;
                }
                break;
			//10种消息类型
            case Wechat::MSG_TYPE_TEXT:
                //回复信息
                switch ($data['Content']) {
                    case '入驻清真寺':
                    	session_start();
                        //模板调用常量{$Think.CONFIG.WEIXIN_LUNTAN}   
                        $url = C('ADD_MOSQUE_URL');
                        //请求获取用户基本信息接口的地址
                        $postData = $GLOBALS[HTTP_RAW_POST_DATA];
                        if(!$postData)
                        {
                        	echo  "error";
                        	exit();
                        }
                        $object = simplexml_load_string($postData,"SimpleXMLElement",LIBXML_NOCDATA);
						$openid = $object->FromUserName;
                        session('openID',$openid);
                        session('Name',"woshizhong");
                        $id=session('openID');
                        $content = '<a href="'.$url.'">点击入驻清真寺</a>';
                        $wechat->replyText('欢迎访问伊媒微时代公众平台，'."$content".$id);
                        break;
                    case '快递查询':
                         $content = kuaidi($data['Content']);
              			 $type = 'text';
                        break;

                    case '图片':
                        //$media_id = $this->upload('image');
                        $media_id = '1J03FqvqN_jWX6xe8F-VJr7QHVTQsJBS6x4uwKuzyLE';
                        $wechat->replyImage($media_id);
                        break;

                    case '语音':
                        //$media_id = $this->upload('voice');
                        $media_id = '1J03FqvqN_jWX6xe8F-VJgisW3vE28MpNljNnUeD3Pc';
                        $wechat->replyVoice($media_id);
                        break;

                    case '视频':
                        //$media_id = $this->upload('video');
                        $media_id = '1J03FqvqN_jWX6xe8F-VJn9Qv0O96rcQgITYPxEIXiQ';
                        $wechat->replyVideo($media_id, '视频标题', '视频描述信息。。。');
                        break;

                    case '音乐':
                        //$thumb_media_id = $this->upload('thumb');
                        $thumb_media_id = '1J03FqvqN_jWX6xe8F-VJrjYzcBAhhglm48EhwNoBLA';
                        $wechat->replyMusic(
                            'Wakawaka!', 
                            'Shakira - Waka Waka, MaxRNB - Your first R/Hiphop source', 
                            'http://wechat.zjzit.cn/Public/music.mp3', 
                            'http://wechat.zjzit.cn/Public/music.mp3', 
                            $thumb_media_id
                        ); //回复音乐消息
                        break;

                    case '图文':
                        
                        $wechat->replyNewsOnce(
                            "全民创业蒙的就是你，来一盆冷水吧！",
                            "全民创业已经如火如荼，然而创业是一个非常自我的过程，它是一种生活方式的选择。从外部的推动有助于提高创业的存活率，但是未必能够提高创新的成功率。第一次创业的人，至少90%以上都会以失败而告终。创业成功者大部分年龄在30岁到38岁之间，而且创业成功最高的概率是第三次创业。", 
                            "http://www.topthink.com/topic/11991.html",
                            "http://yun.topthink.com/Uploads/Editor/2015-07-30/55b991cad4c48.jpg"
                        ); //回复单条图文消息
                        break;

                    case '多图文':
                        $news = array(
                            "全民创业蒙的就是你，来一盆冷水吧！",
                            "全民创业已经如火如荼，然而创业是一个非常自我的过程，它是一种生活方式的选择。从外部的推动有助于提高创业的存活率，但是未必能够提高创新的成功率。第一次创业的人，至少90%以上都会以失败而告终。创业成功者大部分年龄在30岁到38岁之间，而且创业成功最高的概率是第三次创业。", 
                            "http://www.topthink.com/topic/11991.html",
                            "http://yun.topthink.com/Uploads/Editor/2015-07-30/55b991cad4c48.jpg"
                        ); //回复单条图文消息

                        $wechat->replyNews($news, $news, $news, $news, $news);
                        break;
                    
                    default:
                    	$contentStr = "欢迎访问伊媒微时代公众平台"."\n"."微信号：selanyimei"."\n"."一家专注于做穆斯林服务的公众平台！".
                    			"\n"."可体验以下功能："."\n"."【1】 查天气，如输入：银川天气"."\n"."【2】 听音乐，如输入：听歌兰花花"."\n".
                    			"【3】 翻译，如输入：翻译I love you"."\n".
                    			"【4】 看新闻，如输入：新闻"."\n"."更多内容，敬请期待...";
                        $wechat->replyText($contentStr);
                        break;
                }
                break;
            
            default:
                # code...
                break;
        }
    }

    /**
     * 资源文件上传方法
     * @param  string $type 上传的资源类型
     * @return string       媒体资源ID
     */
    private function upload($type){
        $appid     = 'wx58aebef2023e68cd';
        $appsecret = 'bf818ec2fb49c20a478bbefe9dc88c60';

        $token = session("token");

        if($token){
            $auth = new WechatAuth($appid, $appsecret, $token);
        } else {
            $auth  = new WechatAuth($appid, $appsecret);
            $token = $auth->getAccessToken();

            session(array('expire' => $token['expires_in']));
            session("token", $token['access_token']);
        }

        switch ($type) {
            case 'image':
                $filename = './Public/image.jpg';
                $media    = $auth->materialAddMaterial($filename, $type);
                break;

            case 'voice':
                $filename = './Public/voice.mp3';
                $media    = $auth->materialAddMaterial($filename, $type);
                break;

            case 'video':
                $filename    = './Public/video.mp4';
                $discription = array('title' => '视频标题', 'introduction' => '视频描述');
                $media       = $auth->materialAddMaterial($filename, $type, $discription);
                break;

            case 'thumb':
                $filename = './Public/music.jpg';
                $media    = $auth->materialAddMaterial($filename, $type);
                break;
            
            default:
                return '';
        }
        if($media["errcode"] == 42001){ //access_token expired
            session("token", null);
            $this->upload($type);
        }

        return $media['media_id'];
    }
}
