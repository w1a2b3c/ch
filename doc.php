<?php
include("./includes/common.php");

$logintype = $DB->getAll("SELECT * FROM pre_type WHERE status=1 ORDER BY sort ASC");
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
		
		<title>开发文档 - <?php echo $conf['title']?></title>
		<meta name="keywords" content="<?php echo $conf['keywords']?>">
		<meta name="description" content="<?php echo $conf['description']?>" />
		
		<link href="<?php echo $cdnpublic?>twitter-bootstrap/4.4.1/css/bootstrap.min.css" rel="stylesheet">
		<link href="<?php echo $cdnpublic?>font-awesome/5.14.0/css/all.min.css" rel="stylesheet">
		
		<link href="./assets/css/style.css" rel="stylesheet">
<style>
body{padding-top: 0;}
.clogin-header{position: unset;}
.card{margin-bottom: 10px;}
</style>
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
								<a class="nav-link" href="./">首页</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="./user/">用户中心</a>
							</li>
							<li class="nav-item">
								<a class="nav-link" href="#">开发文档</a>
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
		
		<section class="clogin-section clogin-info">
			<div class="container">
				<div class="clogin-section-title text-center">
					<h2>开发文档</h2>
				</div>
<div class="row">
<div class="col-md-3">
<div class="list-group">
  <a href="#doc1" class="list-group-item list-group-item-action">聚合登录介绍</a>
  <a href="#doc2" class="list-group-item list-group-item-action">接口协议规则</a>
  <a href="#doc3" class="list-group-item list-group-item-action">聚合登录流程</a>
  <a href="#doc4" class="list-group-item list-group-item-action">获取用户信息</a>
  <a href="#doc5" class="list-group-item list-group-item-action">SDK下载</a>
</div>
</div>
<div class="col-md-9">

<div class="card" id="doc1" style="">
<div class="card-header">
聚合登录介绍
</div>
<div class="card-body">
<p>
聚合登录，就是利用用户在第三方平台上已有的账号来快速完成自己应用的登录流程。这里的第三方平台，是指QQ、微信、微博、百度等平台。通过本站的聚合登录接口，你的网站可以登录获取相应的用户信息和授权信息，例如uid、token、用户昵称、头像等。本站的聚合登录完全符合OAuth2.0身份鉴权机制。
</p>
</div>
</div>

<div class="card" id="doc2" style="">
<div class="card-header">
接口协议规则
</div>
<div class="card-body">
<p>传输方式：HTTP</p>
<p>数据格式：JSON</p>
<p>字符编码：UTF-8</p>
</div>
</div>

<div class="card" id="doc3" style="">
<div class="card-header">
聚合登录流程
</div>
<div class="card-body">
<strong>Step1：获取跳转登录地址</strong><br/>
请求URL：<br/>
<code><?php echo $siteurl?>connect.php?act=login&amp;appid={你的appid}&amp;appkey={你的appkey}&amp;type={登录方式}&amp;redirect_uri={返回地址}</code><br/>
其中登录方式对应值：<br/>
<table class="table table-hover table-striped table-bordered table-sm">
  <thead>
    <tr>
      <th scope="col">对应值</th>
      <th scope="col">登录方式名称</th>
    </tr>
  </thead>
  <tbody>
<?php foreach($logintype as $row){
echo '<tr>
      <td>'.$row['name'].'</td>
      <td>'.$row['showname'].'</td>
    </tr>';
}
?>
  </tbody>
</table>
返回格式：
<pre>{
  "code": 0,
  "msg": "succ",
  "type": "qq",
  "url": "https://graph.qq.com/oauth2.0/XXXXXXXXXX"
}</pre>
返回参数说明：
<table class="table table-hover table-striped table-bordered table-sm">
  <thead>
    <tr>
      <th scope="col">参数名</th>
	  <th scope="col">参数类型</th>
      <th scope="col">参数说明</th>
	  <th scope="col">参数示例</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>code</td>
      <td>int</td>
	  <td>返回状态码</td>
	  <td>0为成功，其它值为失败</td>
    </tr>
	<tr>
      <td>msg</td>
      <td>string</td>
	  <td>返回信息</td>
	  <td>返回错误时的说明</td>
    </tr>
	<tr>
      <td>type</td>
      <td>string</td>
	  <td>登录方式</td>
	  <td>qq</td>
    </tr>
	<tr>
      <td>url</td>
      <td>string</td>
	  <td>登录跳转地址</td>
	  <td>https://graph.qq.com/oauth2.0/XXXXXXXXXX</td>
    </tr>
    <tr>
      <td>qrcode</td>
      <td>string</td>
	  <td>登录扫码地址</td>
	  <td>此地址仅微信和支付宝返回</td>
    </tr>
  </tbody>
</table>

<br/>
<strong>Step2：跳转到登录地址</strong><br/>
登录地址为上一步返回的url的值。
<br/>
<br/>
<strong>Step3：登录成功会自动跳转到指定的redirect_uri，并跟上Authorization Code</strong><br/>
例如回调地址是：www.qq.com/my.php，则会跳转到：<br/>
<code>http://www.qq.com/my.php?type=qq&amp;code=520DD95263C1CFEA0870FBB66E******</code>
<br/>
<br/>
<strong>Step4：通过Authorization Code获取用户信息</strong><br/>
请求URL：
<code><?php echo $siteurl?>connect.php?act=callback&amp;appid={appid}&amp;appkey={appkey}&amp;type={登录方式}&amp;code={code}</code><br/>
返回格式：
<pre>{
  "code": 0,
  "msg": "succ",
  "type": "qq",
  "access_token": "89DC9691E274D6B596FFCB8D43368234",
  "social_uid": "AD3F5033279C8187CBCBB29235D5F827",
  "faceimg": "https://thirdqq.qlogo.cn/g?b=oidb&amp;k=3WrWp3peBxlW4MFxDgDJEQ&amp;s=100&amp;t=1596856919",
  "nickname": "大白",
  "location": "XXXXX市",
  "gender": "男",
  "ip": "1.12.3.40"
}</pre>
返回参数说明：
<table class="table table-hover table-striped table-bordered table-sm">
  <thead>
    <tr>
      <th scope="col">参数名</th>
	  <th scope="col">参数类型</th>
      <th scope="col">参数说明</th>
	  <th scope="col">参数示例</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>code</td>
      <td>int</td>
	  <td>返回状态码</td>
	  <td>0为成功，2为未完成登录，其它值为失败</td>
    </tr>
	<tr>
      <td>msg</td>
      <td>string</td>
	  <td>返回信息</td>
	  <td>返回错误时的说明</td>
    </tr>
	<tr>
      <td>type</td>
      <td>string</td>
	  <td>登录方式</td>
	  <td>qq</td>
    </tr>
	<tr>
      <td>social_uid</td>
      <td>string</td>
	  <td>第三方登录UID</td>
	  <td>AD3F5033279C8187CBCBB29235D5F827</td>
    </tr>
	<tr>
      <td>access_token</td>
      <td>string</td>
	  <td>第三方登录token</td>
	  <td>89DC9691E274D6B596FFCB8D43368234</td>
    </tr>
	<tr>
      <td>faceimg</td>
      <td>string</td>
	  <td>用户头像</td>
	  <td>https://thirdqq.qlogo.cn/g?......</td>
    </tr>
	<tr>
      <td>nickname</td>
      <td>string</td>
	  <td>用户昵称</td>
	  <td>消失的彩虹海</td>
    </tr>
	<tr>
      <td>gender</td>
      <td>string</td>
	  <td>用户性别</td>
	  <td>男</td>
    </tr>
	<tr>
      <td>location</td>
      <td>string</td>
	  <td>用户所在地</td>
	  <td>XXXXX市(仅限支付宝/微信返回)</td>
    </tr>
    <tr>
      <td>ip</td>
      <td>string</td>
	  <td>用户登录IP</td>
	  <td>1.12.3.40</td>
    </tr>
  </tbody>
</table>
</div>
</div>


<div class="card" id="doc4">
<div class="card-header">
获取用户信息接口
</div>
<div class="card-body">
在用户登录后的任意时间，可以请求以下接口再次查询用户的详细信息。<br/><br/>
请求URL：
<code><?php echo $siteurl?>connect.php?act=query&amp;appid={appid}&amp;appkey={appkey}&amp;type={登录方式}&amp;social_uid={social_uid}</code><br/>
social_uid就是用户的第三方登录UID，用于识别用户的唯一字段。<br/><br/>
返回格式：
<pre>{
  "code": 0,
  "msg": "succ",
  "type": "qq",
  "social_uid": "AD3F5033279C8187CBCBB29235D5F827",
  "access_token": "89DC9691E274D6B596FFCB8D43368234",
  "nickname": "大白",
  "faceimg": "https://thirdqq.qlogo.cn/g?b=oidb&amp;k=ianyRGEnPZlMV2aQvvzg2uA&amp;s=100&amp;t=1599703185",
  "location": "XXXXX市",
  "gender": "男",
  "ip": "1.12.3.40"
}</pre>
返回参数说明：
<table class="table table-hover table-striped table-bordered table-sm">
  <thead>
    <tr>
      <th scope="col">参数名</th>
	  <th scope="col">参数类型</th>
      <th scope="col">参数说明</th>
	  <th scope="col">参数示例</th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td>code</td>
      <td>int</td>
	  <td>返回状态码</td>
	  <td>0为成功，其它值为失败</td>
    </tr>
	<tr>
      <td>msg</td>
      <td>string</td>
	  <td>返回信息</td>
	  <td>返回错误时的说明</td>
    </tr>
	<tr>
      <td>type</td>
      <td>string</td>
	  <td>登录方式</td>
	  <td>qq</td>
    </tr>
	<tr>
      <td>social_uid</td>
      <td>string</td>
	  <td>第三方登录UID</td>
	  <td>AD3F5033279C8187CBCBB29235D5F827</td>
    </tr>
	<tr>
      <td>access_token</td>
      <td>string</td>
	  <td>第三方登录token</td>
	  <td>89DC9691E274D6B596FFCB8D43368234</td>
    </tr>
	<tr>
      <td>faceimg</td>
      <td>string</td>
	  <td>用户头像</td>
	  <td>https://thirdqq.qlogo.cn/g?......</td>
    </tr>
	<tr>
      <td>nickname</td>
      <td>string</td>
	  <td>用户昵称</td>
	  <td>消失的彩虹海</td>
    </tr>
	<tr>
      <td>gender</td>
      <td>string</td>
	  <td>用户性别</td>
	  <td>男</td>
    </tr>
	<tr>
      <td>location</td>
      <td>string</td>
	  <td>用户所在地</td>
	  <td>XXXXX市(仅限支付宝/微信返回)</td>
    </tr>
	<tr>
      <td>ip</td>
      <td>string</td>
	  <td>用户登录IP</td>
	  <td>1.12.3.40</td>
    </tr>
  </tbody>
</table>
</div>
</div>

<div class="card" id="doc5" style="">
<div class="card-header">
SDK下载
</div>
<div class="card-body">
<p>SDK版本：1.0</p>
<p><a href="./assets/files/SDK.zip">点击下载</a></p>
</div>
</div>

</div>
</div>

			</div>
		</section>

		<footer class="clogin-footer text-center">
			<p>Copyright &copy;<?php echo date("Y")?> <?php echo $conf['sitename']?>&nbsp;&nbsp;<?php echo $conf['footer']?></p>
		</footer>
		
		<script src="<?php echo $cdnpublic?>jquery/3.4.1/jquery.min.js"></script>
		<script src="<?php echo $cdnpublic?>twitter-bootstrap/4.4.1/js/bootstrap.min.js"></script>
		
	</body>
</html>