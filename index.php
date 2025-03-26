<?php
if (version_compare(PHP_VERSION, '5.4.0', '<')) {
    die('require PHP >= 5.4 !');
}
include("./includes/common.php");

if($conf['homepage']==2){
	echo '<html><frameset framespacing="0" border="0" rows="0" frameborder="0">
	<frame name="main" src="'.$conf['homepage_url'].'" scrolling="auto" noresize>
  </frameset></html>';
  exit;
}elseif($conf['homepage']==1){
	exit("<script language='javascript'>window.location.href='./user/';</script>");
}
?><!DOCTYPE html>
<html lang="cn">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<link rel="shortcut icon" href="favicon.ico">
		
		<meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport">
		<meta content="yes" name="apple-mobile-web-app-capable">
		<meta content="black" name="apple-mobile-web-app-status-bar-style">
		<meta content="telephone=no" name="format-detection">
		<meta content="email=no" name="format-detection">
		
		<title><?php echo $conf['title']?></title>
		<meta name="keywords" content="<?php echo $conf['keywords']?>">
		<meta name="description" content="<?php echo $conf['description']?>" />
		
		<link href="<?php echo $cdnpublic?>twitter-bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet">
		<link href="<?php echo $cdnpublic?>font-awesome/5.14.0/css/all.min.css" rel="stylesheet">
		
		<link href="./assets/css/style.css" rel="stylesheet">
	</head>
	<body>
		<header class="clogin-header">
			<div class="container">
				<nav class="navbar navbar-expand-lg navbar-light">
					<a class="navbar-brand" href="#">
						<img src="./assets/img/logo.png"> <?php echo $conf['sitename']?>
					</a>
					<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
						<span class="navbar-toggler-icon"></span>
					</button>

					<div class="collapse navbar-collapse" id="navbarSupportedContent">
						<ul class="navbar-nav ml-auto">
							<li class="nav-item active">
								<a class="nav-link" href="#">首页</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="./user/">用户中心</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="./doc.php">开发文档</a>
							</li>
							<?php if($conf['test_open']){?><li class="nav-item">
								<a class="nav-link" href="./user/test.php">接口测试</a>
							</li><?php }?>
							<li class="nav-item">
								<a class="nav-link" href="https://wpa.qq.com/msgrd?v=3&uin=<?php echo $conf['kfqq']?>&site=qq&menu=yes" target="_blank">联系我们</a>
							</li>
						</ul>
					</div>
				</nav>
			</div>
		</header>
		
		<section class="clogin-banner text-center">
			<div class="row" style="width:100%">
				<div class="col-sm-6 col-xs-12 banner-title">
					<h1><?php echo $conf['sitename']?></h1>
					<p>社会化账号聚合登录系统</p>
					<div class="clogin-banner-buttons">
						<a class="btn bg-white" href="./user/"><i class="fa fa-user fa-fw"></i> 用户中心</a>　
						<a class="btn bg-white" href="./doc.php"><i class="fa fa-book fa-fw"></i> 开发文档</a>
					</div>
				</div>
				<div class="col-sm-6 d-none d-sm-block">
					<img src="//img.alicdn.com/tfs/TB14WC0uAL0gK0jSZFAXXcA9pXa-1001-800.png">
				</div>
			</div>
		</section>
		
		<section class="clogin-section clogin-info text-center">
			<div class="container">
				<div class="clogin-section-title">
					<h2>什么是『<?php echo $conf['sitename']?>』？</h2>
				</div>
				
				<p class="text-left">
					<?php echo $conf['sitename']?> 是社会化账号聚合登录系统，让网站的最终用户可以一站式选择使用包括微信、微博、QQ、百度等多种社会化帐号登录该站点。简化用户注册登录过程、改善用户浏览站点的体验、迅速提高网站注册量和用户数据量。有完善的开发文档与SDK，方便开发者快速接入。
				</p>
			</div>
		</section>
		
		<section class="clogin-section clogin-functions text-center">
			<div class="clogin-section-title">
				<h2>它有什么特点？</h2>
			</div>
      <div class="container">
	  <div class="row">
      <div class="um-uapp-insight-card l-pic-r-word">
        <div class="um-uapp-insight-card-imgwrap">
          <img class="um-uapp-insight-card-img" src="//img.alicdn.com/tfs/TB1RDFDurr1gK0jSZFDXXb9yVXa-1120-460.jpg">
        </div>
        <div class="um-uapp-insight-card-content">
          <div class="um-uapp-insight-card-title">全面覆盖国内主流互联网平台</div>
          
          <div class="um-uapp-insight-card-desc">
            
            
            
            <img style="width: 19px;height: 13px;" src="//img.alicdn.com/tfs/TB16YZfr.T1gK0jSZFhXXaAtVXa-40-28.png" alt="">
            覆盖国内主流互联网平台登录，包括：微信账号登录、QQ账号登录、Alipay账号登录、微博账号登录、百度账号登录
          </div>
          
        </div>
      </div>
      
      
      
      
      <div class="um-uapp-insight-card r-pic-l-word">
        <div class="um-uapp-insight-card-imgwrap">
          <img class="um-uapp-insight-card-img" src="//img.alicdn.com/tfs/TB1L31Zurr1gK0jSZR0XXbP8XXa-4672-1914.jpg">
        </div>
        <div class="um-uapp-insight-card-content">
          <div class="um-uapp-insight-card-title">集成成本低、速度快</div>
          
          <div class="um-uapp-insight-card-desc">
            
            
            
            <img style="width: 19px;height: 13px;" src="//img.alicdn.com/tfs/TB16YZfr.T1gK0jSZFhXXaAtVXa-40-28.png" alt="">
            规避平台差异性，统一封装极简接口，多个平台一次搞定。
          </div>
          
          
        </div>
      </div>
      
      
      
      
      <div class="um-uapp-insight-card l-pic-r-word">
        <div class="um-uapp-insight-card-imgwrap">
          <img class="um-uapp-insight-card-img" src="//img.alicdn.com/tfs/TB1cx4DuET1gK0jSZFrXXcNCXXa-1120-460.jpg">
        </div>
        <div class="um-uapp-insight-card-content">
          <div class="um-uapp-insight-card-title">无需备案、无需审核</div>
          
          <div class="um-uapp-insight-card-desc">
            
            
            
            <img style="width: 19px;height: 13px;" src="//img.alicdn.com/tfs/TB16YZfr.T1gK0jSZFhXXaAtVXa-40-28.png" alt="">
            不需要备案域名与繁琐的人工审核，只需在本站注册即可直接使用。
          </div>
          
          
        </div>
      </div>
      
      
      
      
      <div class="um-uapp-insight-card r-pic-l-word">
        <div class="um-uapp-insight-card-imgwrap">
          <img class="um-uapp-insight-card-img" src="//img.alicdn.com/tfs/TB1RPO2urY1gK0jSZTEXXXDQVXa-4673-1918.jpg">
        </div>
        <div class="um-uapp-insight-card-content">
          <div class="um-uapp-insight-card-title">完善的控制面板与统计</div>

          <div class="um-uapp-insight-card-desc">

            <img style="width: 19px;height: 13px;" src="//img.alicdn.com/tfs/TB16YZfr.T1gK0jSZFhXXaAtVXa-40-28.png" alt="">
            通过控制面板，可以实时掌握网站用户登录情况，包含各项统计数据。
          </div>
          
          
        </div>
      </div>
</div>
</div>
		</section>
		
		<section class="clogin-section clogin-links" <?php if(!$conf['links']){echo 'style="display:none;"';}?>>
			<div class="container">
友情链接：<?php echo $conf['links']?>
			</div>
		</section>
		<footer class="clogin-footer text-center">
			<p>Copyright &copy;<?php echo date("Y")?> <?php echo $conf['sitename']?>&nbsp;&nbsp;<?php echo $conf['footer']?></p>
		</footer>
		
		<script src="<?php echo $cdnpublic?>jquery/3.4.1/jquery.min.js"></script>
		<script src="<?php echo $cdnpublic?>twitter-bootstrap/4.4.1/js/bootstrap.min.js"></script>
		
		<script>
		$(function(){
			 $(document).scroll(function() {
				var t = $('.clogin-info').offset().top - $(document).scrollTop() - $('.clogin-header').height();
				
				if(t <= 0){
					$('.clogin-header').addClass('clogin-header-white');
				}else{
					$('.clogin-header').removeClass('clogin-header-white');
				}
			});
		});
		</script>
	</body>
</html>