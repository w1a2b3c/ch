<?php
include("../includes/common.php");
$title='彩虹聚合登录管理中心';
include './head.php';
if($islogin==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");
?>
<?php
$mysqlversion=$DB->getColumn("select VERSION()");
$checkupdate = getCheckString();
?>
<div class="container" style="padding-top:70px;">
<div class="col-xs-12 col-lg-9 center-block" style="float: none;">
<div id="browser-notice"></div>
<div class="row">
    <div class="col-xs-12 col-lg-8">
      <div class="panel panel-info">
        <div class="panel-heading"><h3 class="panel-title" id="title">后台管理首页</h3></div>
          <ul class="list-group">
            <li class="list-group-item"><span class="glyphicon glyphicon-user"></span> <b>用户数量：</b><span id="count1"></span></li>
			<li class="list-group-item"><span class="glyphicon glyphicon-globe"></span> <b>应用数量：</b><span id="count2"></span></li>
			<li class="list-group-item"><span class="glyphicon glyphicon-stats"></span> <b>第三方账号数量：</b><span id="count3"></span></li>
            <li class="list-group-item"><span class="glyphicon glyphicon-time"></span> <b>现在时间：</b> <?=$date?></li>
			</li>
          </ul>
      </div>
	  <div class="panel panel-success">
        <div class="panel-heading"><h3 class="panel-title" id="title">环境信息</h3></div>
          <ul class="list-group">
            <li class="list-group-item">
				<b>PHP 版本：</b><?php echo phpversion() ?>
			</li>
			<li class="list-group-item">
				<b>MySQL 版本：</b><?php echo $mysqlversion ?>
			</li>
			<li class="list-group-item">
				<b>服务器软件：</b><?php echo $_SERVER['SERVER_SOFTWARE'] ?>
			</li>
			<li class="list-group-item">
				<b>服务器时间：</b><?php echo $date ?>
			</li>
			</li>
          </ul>
      </div>
	</div>
	<div class="col-xs-12 col-lg-4">
      <div class="panel panel-default">
        <div class="panel-heading"><h3 class="panel-title" id="title">管理员信息</h3></div>
          <ul class="list-group text-center">
            <li class="list-group-item">
			<img src="<?php echo ($conf['kfqq'])?'//q2.qlogo.cn/headimg_dl?bs=qq&dst_uin='.$conf['kfqq'].'&src_uin='.$conf['kfqq'].'&fid='.$conf['kfqq'].'&spec=100&url_enc=0&referer=bu_interface&term_type=PC':'../assets/img/user.png'?>" alt="avatar" class="img-circle img-thumbnail"></br>
			<span class="text-muted"><strong>用户名：</strong><font color="blue"><?php echo $conf['admin_user']?></font></span><br/><span class="text-muted"><strong>用户权限：</strong><font color="orange">管理员</font></span></li>
			<li class="list-group-item"><a href="../" class="btn btn-xs btn-default">返回首页</a>&nbsp;<a href="./set.php?mod=account" class="btn btn-xs btn-info">修改密码</a>&nbsp;<a href="./login.php?logout" class="btn btn-xs btn-danger">退出登录</a>
			</li>
          </ul>
      </div>
	  <div class="panel panel-default">
        <div class="panel-heading"><h3 class="panel-title" id="title">检测更新</h3></div>
		<ul class="list-group text-dark" id="checkupdate"></ul>
      </div>
	</div>
</div>

    </div>
  </div>
<script>
$(document).ready(function(){
	$('#title').html('正在加载数据中...');
	$.ajax({
		type : "GET",
		url : "ajax.php?act=getcount",
		dataType : 'json',
		async: true,
		success : function(data) {
			$('#title').html('后台管理首页');
			$('#count1').html(data.count1);
			$('#count2').html(data.count2);
			$('#count3').html(data.count3);
			$.ajax({
				url: '<?php echo $checkupdate?>',
				type: 'get',
				dataType: 'jsonp',
				async: true,
				jsonpCallback: 'callback'
			}).done(function(data){
				$("#checkupdate").html(data.msg);
			})
		}
	});
})
</script>
<script>
function speedModeNotice(){
	var ua = window.navigator.userAgent;
	if(ua.indexOf('Windows NT')>-1 && ua.indexOf('Trident/')>-1){
		var html = "<div class=\"panel panel-default\"><div class=\"panel-body\">当前浏览器是兼容模式，为确保后台功能正常使用，请切换到<b style='color:#51b72f'>极速模式</b>！<br>操作方法：点击浏览器地址栏右侧的IE符号<b style='color:#51b72f;'><i class='fa fa-internet-explorer fa-fw'></i></b>→选择“<b style='color:#51b72f;'><i class='fa fa-flash fa-fw'></i></b><b style='color:#51b72f;'>极速模式</b>”</div></div>";
		$("#browser-notice").html(html)
	}
}
speedModeNotice();
</script>