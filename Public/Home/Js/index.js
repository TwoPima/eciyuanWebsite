/**
 * 带下拉菜单的导航在点击时只会浮出下拉菜单，它本身的href属性会失效，也就是失去了超链接功能，如果希望菜单栏的链接可以正常打开
 */
<script>
$(document).on("click",".dropdown-toggle",function(){
	if( $(window).width() > 767 )
	if($(this).attr(‘href’)) window.location = $(this).attr(‘href’);
	});
</script>
/*
(function(){
	 function $(id){
		return typeof id==='string'?document.getElementById(id):id;
	}
	window.onload=function(){
		//获取鼠标滑过，点击的标签和要切换的内容要素
		var titles=$('footer_main_friend_link_title').getElementsByTagName('li'),
		divs=$('footer_main_friend_link_con').getElementsByTagName('div');
		if(titles.length!=divs.length)
			return;
		//遍历titles下的所有li
		for(var i=0;i<titles.length;i++){
			titles[i].id=i;
			titles[i].onmouseover=function(){

				//清除所有li上的class
				for(var j=0;j<titles.length;j++){
					titles[j].className="";
					divs[j].style.display='none';
				}
						//设置当前为高亮显示
				this.className='select';
				divs[this.id].style.display='block';
			}
		}
	}
	})()*/

