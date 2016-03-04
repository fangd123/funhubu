<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
	<title>后台</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1.0">
	<link href="<?php echo (C("bootstrap_css")); ?>" rel="stylesheet">
	<script src="<?php echo (C("jquery_js")); ?>"></script>
	<script src="<?php echo (C("bootstrap_js")); ?>"></script>
	<style>
.list-group{
	margin-bottom:0px;
}
	</style>
</head>
<body>
	<nav class="navbar navbar-inverse" role="navigation">
        <div class="container">
			<div class="navbar-header">
				<a class="navbar-brand" href="<?php echo U('Index/index');?>">后台管理</a>
            </div>
			<a class="navbar-right navbar-btn" href="<?php echo U('Index/logout');?>" id="logout" style="font-size:x-large"><span class="glyphicon glyphicon-log-out"></span></a>
        </div>
    </nav>
    <div id="side-bar" class="row col-md-2">
		<div class="list-group">
			<a href="<?php echo U('Index/index');?>" class="list-group-item">Home</a>
			<a href="#boardOptions" class="list-group-item collapsed" data-toggle="collapse">表白墙</a>
			<ul id="boardOptions" class="list-group collapse">
				<li class="list-group-item">1</li>
				<li class="list-group-item">2</li>
			</ul>
			<a href="<?php echo U('Wiki/index');?>" class="list-group-item">湖大维基</a>
		</div>
    </div>
	<div id="main" class="row col-md-10">
		<ul class="nav nav-tabs" id="tab">
			<li role="presentation"><a href="#todo">待审阅</a></li>
			<li role="presentation"><a href="#passed">已通过</a></li>
			<li role="presentation"><a href="#masked">已屏蔽</a></li>
		</ul>
		<div class="tab-content">
			<div class="tab-pane" id="todo">
				<div class="row col-md-offset-2 col-md-10">
					<table class="table" id="todo-content">
						<thead>
							<tr><th><input type="checkbox">全/反选</th><th>序号</th><th>表白者</th><th>被表白者</th><th>内容</th><th>时间</th></tr>
						</thead>
						<tbody>
						</tbody>
					</table>
					<button class="btn btn-primary btn-lg pull-right" id="pass">通过</button>
					<button class="btn btn-danger btn-lg pull-right" id="mask">屏蔽</button>
				</div>
			</div>
			<div class="tab-pane" id="passed">
				<div class="row col-md-offset-2 col-md-10">
					<table class="table" id="pass-content">
					</table>
					<nav class="pull-right">
						<ul class="pagination">
							<li>
								<a href="#" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a>
							</li>
							<li><a href="#">1</a></li>
							<li><a href="#">2</a></li>
							<li><a href="#">3</a></li>
							<li><a href="#">4</a></li>
							<li><a href="#">5</a></li>
							<li>
								<a href="#" aria-label="Next"><span aria-hidden="true">&raquo;</span></a>
							</li>
						</ul>
					</nav>
				</div>
			</div>
			<div class="tab-pane" id="masked">
				<div class="row col-md-offset-2 col-md-10">
					<table class="table" id="pass-content">
					</table>
					<nav class="pull-right">
						<ul class="pagination">
							<li>
								<a href="#" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a>
							</li>
							<li><a href="#">1</a></li>
							<li><a href="#">2</a></li>
							<li><a href="#">3</a></li>
							<li><a href="#">4</a></li>
							<li><a href="#">5</a></li>
							<li>
								<a href="#" aria-label="Next"><span aria-hidden="true">&raquo;</span></a>
							</li>
						</ul>
					</nav>
				</div>
			</div>
	</div>
</body>
<script>
$(function(){
	$('#tab a:first').tab('show');
	$('#tab a').click(function(e){
		e.preventDefault();
		$(this).tab('show');
	});
				
});

$(document).ready(function(){
});
$node = $('#todo-content tbody');

function todoList(){
	alert('aaa');
}
</script>
</html>