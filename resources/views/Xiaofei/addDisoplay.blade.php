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
  <form class="layui-form" action="" lay-filter="example">
    <div class="layui-form-item" >
      <label class="layui-form-label">消费内容</label>
      <div class="layui-input-block">
        <input type="text" name="project" id="project" lay-verify="title" autocomplete="off" placeholder="消费内容" class="layui-input" style="width: 300px">
      </div>
    </div>
    <div class="layui-form-item">
      <label class="layui-form-label">消费金额</label>
        <div class="layui-input-block">
          <input type="text" name="price" id="price" placeholder="消费金额" autocomplete="off" class="layui-input" style="width: 300px">
        </div>
    </div>
    <div class="layui-form-item">
      <div class="layui-inline">
        <label class="layui-form-label">消费时间</label>
          <div class="layui-input-inline">
            <input type="text" class="layui-input" id="time" placeholder="yyyy-MM-dd" name="time">
          </div>
      </div>
    </div>
    <div class="layui-form-item">
      <label class="layui-form-label">消费类型</label>
      <div class="layui-input-block" style="width: 300px">
        <select name="type" id="type" lay-filter="aihao" >
          <option value=""></option>
          @if(!empty($type))
                                @foreach($type as $k=>$v)
                                    <option value="{{$v['id']}}">{{$v['type_name']}}</option>
                                @endforeach
                            @endif
        </select>
      </div>
    </div>
    <div class="layui-form-item">
      <label class="layui-form-label">消费人</label>
        <div class="layui-input-block">
          @if(!empty($name))
                                @foreach($name as $k=>$v)
                                    <input type="radio" name="name" value="{{$v['id']}}" title="{{$v['name']}}">
                                @endforeach
                            @endif
        </div>
    </div>
    <div class="layui-form-item layui-form-text">
      <label class="layui-form-label">消费备注</label>
      <div class="layui-input-block" style="width: 300px">
        <textarea placeholder="消费备注" class="layui-textarea" name="desc" id="desc" lay-verify="content"></textarea>
      </div>
    </div>
    <div class="layui-form-item">
      <div class="layui-input-block">
        <button type="submit" class="layui-btn" lay-submit="" lay-filter="demo1">立即提交</button>
      </div>
    </div>
  </form>             
          
<script src="/layui/layui.js" charset="utf-8"></script>
<!-- 注意：如果你直接复制所有代码到本地，上述js路径需要改成你本地的 -->
 
<script>
layui.use(['form','jquery',['layedit']], function(){
  var form = layui.form;
  var $ = layui.jquery;
  var layedit  = layui.layedit;


  var editIndex = layedit.build('desc');
  //监听提交
  form.on('submit(demo1)', function(data){
            var obj = $(this);
            var project = $('#project').val();
            var type  = $('select[name="type"]').val();
            var price = $('#price').val();
            var time = $('#time').val();
            var name = $("input[name='name']:checked").val(); 
            if(!project){
              layer.msg('请输入消费内容！',{icon:2});
              return false;
            }
            if(!type){
              layer.msg('请选择消费类型！',{icon:2});
              return false;
            }
            if(!price){
              layer.msg('请输入消费金额！',{icon:2});
              return false;
            }
            if(!time){
              layer.msg('请选择消费时间！',{icon:2});
              return false;
            }
            if(!name){
              layer.msg('请选择消费人！',{icon:2});
              return false;
            }
            $.ajax({
                type:"POST",
                url:'/xf/ajaxaddxf',
                data:{'_token':'{{csrf_token()}}',project:project,type:type,price:price,time:time,name:name},
                dataType:"JSON",
                success:function(data){
                    if(data.code == 200)
                    {
                        layer.msg('添加成功',{icon:1,time: 2000},function(){
                            window.parent.location.reload();//刷新父页面
                            parent.layer.closeAll();
                        });
                    }
                    else{
                        obj.attr('disabled',false);
                        layer.msg(data.message,{icon:2}, function(){
                            layer.closeAll();
                        });
                    }
                }
            });
    return false;
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
    elem: '#time'
  });
});
</script>

</body>
</html>