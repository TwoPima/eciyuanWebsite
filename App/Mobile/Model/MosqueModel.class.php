<?php
/**
*
* 版权所有：亿次元科技（www.eciyuan.net）
* 作    者：马晓成<857773627@qq.com>
* 日    期：2016-06-21
* 版    本：1.0.0
* 功能说明：微信端-清真寺控制器演示。
*
**/
namespace Mobile\Model;
use Think\Model;
class MosqueModel extends Model{  
    protected $_validate = array(   
        array('verify','require','清真寺名称必须填写！'), //默认情况下用正则进行验证 
        array('linkman','require','联系人必须填写！'), //默认情况下用正则进行验证 
        array('address','require','清真寺地址必须！'), //默认情况下用正则进行验证 
        array('s_province','require','省份也要选择呢！'), //默认情况下用正则进行验证 
        array('s_county','require','市也要选择哦！'), //默认情况下用正则进行验证 
        array('password','require','密码必须填写'), //默认情况下用正则进行验证 
        array('mobile','require','手机号码必须填写！'), //默认情况下用正则进行验证 
        array('name','','帐号名称已经存在！',0,'unique',1), // 在新增的时候验证name字段是否唯一 
       // array('value',array(1,2,3),'值的范围不正确！',2,'in'), // 当值不为空的时候判断是否在一个范围内    
        //array('repassword','password','确认密码不正确',0,'confirm'), // 验证确认密码是否和密码一致    
      //  array('password','checkPwd','密码格式不正确',0,'function'), // 自定义函数验证密码格式  
        );
    protected $_auto = array (      
        array('status','1'),  // 新增的时候把status字段设置为1   
        array('type','2'),  // 2为清真寺类型  
        //array('password','md5',3,'function') , // 对password字段在新增和编辑的时候使md5函数处理       
       // array('name','getName',3,'callback'), // 对name字段在新增和编辑的时候回调getName方法        
        array('update_time','time',2,'function'), // 对update_time字段在更新的时候写入当前时间戳  
        array('create_time','time',2,'function'), // 对update_time字段在更新的时候写入当前时间戳  
        //   array('update_time','time',date("Y-m-d G:i:s"),'function'),
        );
}