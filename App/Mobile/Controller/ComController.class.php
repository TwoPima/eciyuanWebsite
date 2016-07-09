<?php
/**
*
* 版权所有：亿次元科技（www.eciyuan.net）
* 作    者：马晓成<857773627@qq.com>
* 日    期：2016-06-21
* 版    本：1.0.0
* 功能说明：微信端-公共控制器演示。
*
**/

namespace Mobile\Controller;
use Think\Controller;
class ComController extends Controller {

	public function _initialize(){
	    Load('extend');
	    import("ORG.Util.Page"); //载入分页类
		C(setting());
	  		//导航数据组装
		$result = M('Category')->where('topsite=1')->order('o ASC')->select();
		$article_cate_list = array();
		foreach ($result as $val) {
			if ($val['pid']==0) {
				$article_cate_list['parent'][$val['id']] = $val;
			} else {
				$article_cate_list['sub'][$val['pid']][] = $val;
			}
		}
		$this->assign('article_cate_list',$article_cate_list); 
		
		$links = M('links')->limit(10)->order('o ASC')->select();
		$this->assign('links',$links);
		
    }
    
    public function getCity($ip){
        //ip地址转换
        import('ORG.Net.IpLocation');// 导入IpLocation类
        $Ip = new IpLocation(); // 实例化类 参数表示IP地址库文件
        $result = $Ip->getlocation($ip); // 获取域名服务器所在的位置
        $country=$result[0]['country'];
        return $country;
        /*dump($area); 输出array(5) {
         ["ip"] => string(14) "61.135.169.105"
         ["beginip"] => string(12) "61.135.162.0"
         ["endip"] => string(14) "61.135.169.255"
         ["country"] => string(9) "北京市"
         ["area"] => string(12) "百度蜘蛛"
         }   */
    }
    //留言评论信息处理
    public function msgmodify($ary){
        if(is_array($ary)){
            if($ary['adder_id']) $ary['adder_name']=getUserName($ary['adder_id']);
            $ary['adder_email'] = md5($ary['adder_email']);
        }
        return $ary;
    }
    //公共上传图片方法
    public function upload($savePath)
    {
        import("ORG.Net.UploadFile");
        $upload = new UploadFile();
        //设置上传文件大小
        $upload->maxSize = 32922000;
        $upload->allowExts = explode(',', 'jpg,gif,png,jpeg');
        $upload->savePath = ROOT_PATH.'/data/'.$savePath.'/';
        $upload->saveRule = uniqid;
        if(!file_exists($upload->savePath)){
            @mkdir($upload->savePath, 0777);
        }
        if (!$upload->upload()) {
            //捕获上传异常
            $this->error($upload->getErrorMsg());
        } else {
            //取得成功上传的文件信息
            $uploadList = $upload->getUploadFileInfo();
        }
        $uploadList='./data/'.$savePath.'/'.$uploadList['0']['savename'];
        return $uploadList;
    }
  /*   //单页
    public function detail(){
        $where1['aid'] = $_GET['id'];
        $model=ucfirst($_GET['type']);
        $resultArt= M($model)->where($where1)->find();
        //上一篇
        $front=M($model)->where("aid<".$_GET['id'])->order('aid desc')->find();
        //下一篇
        $after=M($model)->where("aid>".$_GET['id'])->order('aid desc')->find();
        $this->assign('detail',$resultArt);
        $this->assign('front',$front);//上一条
        $this->assign('after',$after);//下一条
        $this->display();
    } */
   /*  public function index(){
        $modules = array('Admin');  //模块名称
        $i = 0;
        foreach ($modules as $module) {
            $all_controller = $this->getController($module);
            foreach ($all_controller as $controller) {
                $controller_name = $controller;
                $all_action = $this->getAction($module, $controller_name);
                foreach ($all_action as $action) {
                    $data[$i] = array(
                        'name' => $controller . '_' . $action,
                        'status' => 1
                    );
                    $i++;
                }
            }
        }
        echo '<pre>';
        print_r($data);
    }
     */
    //获取所有控制器名称
    protected function getController($module){
        if(empty($module)) return null;
        $module_path = APP_PATH . '/' . $module . '/Controller/';  //控制器路径
        if(!is_dir($module_path)) return null;
        $module_path .= '/*.class.php';
        $ary_files = glob($module_path);
        foreach ($ary_files as $file) {
            if (is_dir($file)) {
                continue;
            }else {
                $files[] = basename($file, C('DEFAULT_C_LAYER').'.class.php');
            }
        }
        return $files;
    }
    
    
}