var uid=0;//标记读当前读取记录的id
var reset = 1;//避免一次滑动多次加载数据
var firstTop =true;//第一次在顶部
$(document).ready(function(){
    loadMsg();
    $(document).on('scroll',function(){
        firstTop = false;
        if(isLoad()&& uid>0 && reset) loadMsg(uid);
        showToolBar();
        if(!firstTop && isTop()){
            location.reload();
        }
    });
    $('#submit').on('click',function(){
        sendMsg();
    });
});

(function($){  
    $.getUrlParam = function(name)  
    {  
           var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");  
        var r = window.location.search.substr(1).match(reg);  
        if (r!=null) return unescape(r[2]); return null;  
    }  
})(jQuery);
/**
 *从服务器获取消息数据
 */
function loadMsg(){
    reset = 0;
    var source="/Addon/Board/read";
    $.ajax({url:source,data:{id:uid},dataType:"json",success: function(data){
		uid = data.id;//标记最后一条记录的id
        addMsg(data.data);//读取到消息之后插入到文本中
    }});
}

function addMsg(data){//将数据插入到dom树中
	var $parentNode = $('#content');
	var length = data.length;
	if(uid != -1){//如果数据未读完则留最后一条做为下次读取的标记
		length -= 1;
	}
	for(var i=0; i < length; i++){//返回的n条记录只将前n-1条插入文档，最后一条作为下一次读取的标记
		var $node = $('<div class="panel panel-default">');
		var $head = $('<div class="panel-heading">');
		var $from = $('<span>').html('From:').append($('<span class="text-info">').html(data[i].from));
		var $to = $('<span>').html('To:').append($('<span class="text-info">').html(data[i].to));
		var $close = $('<div class="pull-right text-warning">').html('&nbsp;').append($('<span class="glyphicon glyphicon-remove">'));
		$node.append($head.append($from).append($to).append($close));
		$body = $('<div class="panel-body">');
		$content = $('<p>').html(data[i].content);
		$time = $('<div class="pull-right">').html(data[i].time);
		$node.append($body.append($content).append($time));
		$parentNode.append($node);
		$close.on('click',function(){
			$(this).parent().parent().hide();
		});

	}
    reset = 1;//reset置1，可以触发下一次loadMsg（）
}
function isLoad(){//判断滚动条是否向下滚动
	var height = 800;
	return ($(document).scrollTop() + $(window).height() > $(document).height() - height);
}

function isTop(){
    return $(document).scrollTop()?false:true;
}

function iniToolBar(){
	firstTop = true;
}

function showToolBar(){//显示回到顶部按钮
	var height = 20;
	var top = $('#ToolBar');
	var st = $(window).scrollTop();   
    if(st > height){
        top.show();   
    }else{   
        top.hide();   
    } 
}

/**
 *将信息及url地址发送到后台
 * @param url
 */
function sendMsg(){
	var msg = $.trim($('#inputMsg').val());
	var openId = $.getUrlParam('open_id');
	if(openId == '' || openId == undefined || openId == null){
		alert('请关注后再提交');
		return;
	}
	if(msg == '' || msg == null || msg == undefined){
		alert('请填写所有必填项');
		return;
	}
    var target = '/Addon/Board/write?open_id='+openId;
    var $form = $('#inputForm');
    $.post(target,$form.serialize(),function(data){
        if(data.status == 'success'){
            alert('表白成功');
			$('#myModal').fadeOut();
            location.reload();
        }else{
            alert(data.code);
			return;
        }
    });
}
