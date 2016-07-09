<?php
/**
*
* 版权所有：亿次元科技<www.eciyuan.net>
* 作    者：马晓成<857773627@qq.com>
* 日    期：2016-04-21
* 版    本：1.0.0
* 功能说明：微信控制器。
*
**/
namespace Home\Controller;
use Think\Controller;
use Vendor\Page;
class WechatController extends ComController {
//微信首页方法
    public function index(){
        /* 加载微信SDK */
        import('@.ORG.ThinkWechat');
        $weixin = new ThinkWechat('yangyifan');
        /* 获取请求信息 */
        $data = $weixin->request();
        
        /* 获取回复信息 */
        
        
        
        list($content, $type) = $this->reply($data);
        
        M('info')->data($data)->add();
        
        //天气
        if(substr($data['Content'],0,6) == '天气'){
            $content = _getWeather($data['Content']);
            $type = 'text';
        
        //翻译    
        }else if(substr($data['Content'],0,6) == '翻译'){
            $content = _fanyi($data['Content']);
            $type = 'text';
        
        //快递    
        }else if(substr($data['Content'],0,6) == '快递'){
            $content = _getDindan($data['Content']);
            $type = 'text';
            
        //小黄鸡    
        }else if(substr($data['Content'],0,1) == '@'){
            $content = _xiaohuangji($data['Content']);
            $type = 'text';
            
        //帮助
        }else{
            $content = '';
            }
            
        $weixin->response($content, $type);
        
    }
        
    }