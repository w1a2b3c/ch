<?php
@header('Content-Type: text/html; charset=UTF-8');

$admin_cdnpublic = 0;
if($admin_cdnpublic==1){
	$cdnpublic = '//lib.baomitu.com/';
}elseif($admin_cdnpublic==2){
	$cdnpublic = 'https://cdn.bootcdn.net/ajax/libs/';
}elseif($admin_cdnpublic==4){
	$cdnpublic = '//lf26-cdn-tos.bytecdntp.com/cdn/expire-1-M/';
}else{
	$cdnpublic = '//cdn.staticfile.org/';
}
?>
<!DOCTYPE html>
<html lang="zh-cn">
<head>
  <meta charset="utf-8"/>
  <meta name="renderer" content="webkit">
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title><?php echo $title ?></title>
  <link href="<?php echo $cdnpublic?>twitter-bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="<?php echo $cdnpublic?>font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet"/>
  <link href="../assets/css/bootstrap-table.css" rel="stylesheet"/>
  <script src="<?php echo $cdnpublic?>modernizr/2.8.3/modernizr.min.js"></script>
  <script src="<?php echo $cdnpublic?>jquery/2.1.4/jquery.min.js"></script>
  <script src="<?php echo $cdnpublic?>twitter-bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <!--[if lt IE 9]>
    <script src="<?php echo $cdnpublic?>html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="<?php echo $cdnpublic?>respond.js/1.4.2/respond.min.js"></script>
  <![endif]-->
</head>
<body>
<?php if($islogin==1){?>
  <nav class="navbar navbar-fixed-top navbar-default">
    <div class="container">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
          <span class="sr-only">导航按钮</span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="./">彩虹聚合登录管理中心</a>
      </div><!-- /.navbar-header -->
      <div id="navbar" class="collapse navbar-collapse">
        <ul class="nav navbar-nav navbar-right">
          <li class="<?php echo checkIfActive('index,')?>">
            <a href="./"><i class="fa fa-home"></i> 平台首页</a>
          </li>
		  <li class="<?php echo checkIfActive('apps,edit')?>">
            <a href="./apps.php"><i class="fa fa-cubes"></i> 应用列表</a>
          </li>
		  <li class="<?php echo checkIfActive('accounts,logs')?>">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-cloud"></i> 聚合登录<b class="caret"></b></a>
            <ul class="dropdown-menu">
			  <li><a href="./accounts.php">第三方账号列表</a></li>
			  <li><a href="./logs.php">登录记录</a></li>
            </ul>
          </li>
		  <li class="<?php echo checkIfActive('ulist,glist,group,order,uset')?>">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i> 用户管理<b class="caret"></b></a>
            <ul class="dropdown-menu">
              <li><a href="./ulist.php">用户列表</a></li>
			  <li><a href="./glist.php">用户组设置</a></li>
			  <li><a href="./group.php">用户组购买</a></li>
			  <li><a href="./order.php">支付订单</a></li>
            </ul>
          </li>
		  <li class="<?php echo checkIfActive('set_login')?>">
            <a href="./set_login.php"><i class="fa fa-certificate"></i> 登录接口</a>
          </li>
		  <li class="<?php echo checkIfActive('set,gonggao,clean')?>">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-cog"></i> 系统设置<b class="caret"></b></a>
            <ul class="dropdown-menu">
              <li><a href="./set.php?mod=site">网站信息配置</a></li>
			  <li><a href="./set.php?mod=oauth">快捷登录配置</a><li>
			  <li><a href="./set.php?mod=pay">支付接口配置</a><li>
			  <li><a href="./gonggao.php">网站公告配置</a></li>
			  <li><a href="./set.php?mod=mail">邮箱与短信配置</a><li>
			  <li><a href="./set.php?mod=upimg">网站Logo上传</a><li>
			  <li><a href="./set.php?mod=iptype">IP地址获取设置</a><li>
			  <li><a href="./set.php?mod=proxy">中转代理设置</a><li>
			  <li><a href="./clean.php">数据清理</a><li>
        <li><a href="./update.php">检测更新</a><li>
            </ul>
          </li>
		  <li class="<?php echo checkIfActive('userlog')?>">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i> 管理员<b class="caret"></b></a>
            <ul class="dropdown-menu">
			  <li><a href="./userlog.php">登录日志</a><li>
			  <li><a href="./set.php?mod=account">修改密码</a><li>
			  <li><a href="./login.php?logout">退出登录</a><li>
            </ul>
          </li>
        </ul>
      </div><!-- /.navbar-collapse -->
    </div><!-- /.container -->
  </nav><!-- /.navbar -->
<?php }?>