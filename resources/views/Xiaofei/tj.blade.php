<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Layui</title>
  <meta name="renderer" content="webkit">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
  <link rel="stylesheet" href="/layui/css/layui.css"  media="all">

  <!-- 注意：如果你直接复制所有代码到本地，上述css路径需要改成你本地的 -->
</head>
<body>
<ul class="layui-nav">
  <li class="layui-nav-item"><a href="/xf/index.html">消费记录</a></li>
  <li class="layui-nav-item layui-this"><a href="/Home/Xiaofei/constat">消费总计</a></li>
</ul>
<br>

<div class="demoTable">
  &nbsp;&nbsp;&nbsp;&nbsp;
  <div class="layui-inline">
    <input class="layui-input" name="project" id="project" autocomplete="off" placeholder="消费备注">
  </div>
  <button class="layui-btn search" data-type="submit" id="search" lay-filter="search">确定</button>
  <button data-method="setTop" class="layui-btn" onclick="openopen()" style="position:absolute;right: 0px;">新增记录</button>
</div>
<table class="layui-hide" id="test"></table>
              
          
<script src="/layui/layui.js" charset="utf-8"></script>
<script src="/js/jquery-1.8.3.min.js" charset="utf-8"></script>
<link rel="stylesheet" type="text/css" href="/layui/css/layui.css">
<link rel="stylesheet" type="text/css" href="/layui/css/layui.mobile.css">

<!-- 注意：如果你直接复制所有代码到本地，上述js路径需要改成你本地的 -->
 
<script>
layui.use(['form', 'layer', 'laydate', 'table', 'laytpl'], function(){
  var table = layui.table;
  var form = layui.form;
  var layer = layui.layer;
  var laydate = layui.laydate;
  var laytpl = layui.laytpl;
  
  table.render({
    elem: '#test'
    ,url:'/xf/index_data'
    ,cellMinWidth: 80 //全局定义常规单元格的最小宽度，layui 2.2.1 新增
    ,cols: [[
      {field:'id', title: 'ID', sort: true}
      ,{field:'name', title: '姓名'}
      ,{field:'project', title: '用途'}
      ,{field:'price',  title: '金额', sort: true}
      ,{field:'type', title: '类型',  minWidth: 100}
      ,{field:'time', title: '日期',  minWidth: 100}
    ]]
    ,page: true
  });
  $(".search").on('click',function(){
            table.reload("test",{
                page:{
                    curr : 1
                },
                where:{
                    project:$("input[name='project']").val().trim()
                }
            })
        });

  
});

layui.use('element', function(){
  var element = layui.element; //导航的hover效果、二级菜单等功能，需要依赖element模块
  
  //监听导航点击
  element.on('nav(demo)', function(elem){
    //console.log(elem)
    layer.msg(elem.text());
  });
});

function openopen(){
    layer.open({
      type: 2,
      title: '新增记录',
      shadeClose: true,
      shade: false,
      maxmin: false, //开启最大化最小化按钮
       area: screen() < 2 ? ['90%', '90%'] : ['500px', '730px'],
      content: '/xf/addDisoplay'
    });
}
function screen() {
            //获取当前窗口的宽度
            var width = $(window).width();
            if (width > 1200) {
                return 3;   //大屏幕
            } else if (width > 992) {
                return 2;   //中屏幕
            } else if (width > 768) {
                return 1;   //小屏幕
            } else {
                return 0;   //超小屏幕
            }
        }




layui.use('element', function(){
  var element = layui.element; //导航的hover效果、二级菜单等功能，需要依赖element模块
  
  //监听导航点击
  element.on('nav(demo)', function(elem){
    //console.log(elem)
    layer.msg(elem.text());
  });
});
layui.use('laydate', function(){
  var laydate = layui.laydate;
  
  //常规用法
  laydate.render({
    elem: '#test1'
  });
});

</script>

</body>
</html>