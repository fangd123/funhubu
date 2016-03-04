<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
	<title>后台</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1.0">
	<link href="<?php echo (C("bootstrap_css")); ?>" rel="stylesheet">
	<script src="<?php echo (C("jquery_js")); ?>"></script>
	<script src="<?php echo (C("bootstrap_js")); ?>"></script>
</head>
<body>
	<nav class="navbar navbar-inverse" role="navigation">
        <div class="container">
			<div class="navbar-header">
				<a class="navbar-brand" href="#">后台管理</a>
            </div>
			<a class="navbar-right navbar-btn" href="<?php echo U('Index/logout');?>" id="logout" style="font-size:x-large"><span class="glyphicon glyphicon-log-out"></span></a>
        </div>
    </nav>
    <div id="side-bar" class="row col-md-2">
		<div class="list-group">
			<a href="#" class="list-group-item active">Home</a>
			<a href="<?php echo U('Board/index');?>" class="list-group-item">表白墙</a>
			<a href="<?php echo U('Wiki/index');?>" class="list-group-item">湖大维基</a>
		</div>
    </div>
	<div id="main" class="row col-md-10">
		<p>&nbsp;</p>
	</div>
</body>
</html>