<?php
/**
 * 系统数据清理
**/
include("../includes/common.php");
$title='系统数据清理';
include './head.php';
if($islogin==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");
?>
  <div class="container" style="padding-top:70px;">
    <div class="col-xs-12 col-sm-10 col-lg-8 center-block" style="float: none;">
<?php
$mod=isset($_GET['mod'])?$_GET['mod']:null;
if($mod=='cleancache'){
$CACHE->clear();
if(function_exists("opcache_reset"))@opcache_reset();
showmsg('清理系统设置缓存成功！',1);
}elseif($mod=='cleanorder'){
$DB->exec("DELETE FROM `pre_order` WHERE addtime<'".date("Y-m-d H:i:s",strtotime("-30 days"))."'");
$DB->exec("OPTIMIZE TABLE `pre_order`");
showmsg('删除30天前支付订单记录成功！',1);
}elseif($mod=='cleanlogs'){
$DB->exec("DELETE FROM `pre_logs` WHERE addtime<'".date("Y-m-d H:i:s",strtotime("-30 days"))."'");
$DB->exec("OPTIMIZE TABLE `pre_logs`");
showmsg('删除30天前聚合登录记录成功！',1);
}elseif($mod=='cleanorderi' && $_POST['do']=='submit'){
$days = intval($_POST['days']);
if($days<=0)showmsg('请确保每项不能为空',3);
$DB->exec("DELETE FROM `pre_order` WHERE addtime<'".date("Y-m-d H:i:s",strtotime("-{$days} days"))."'");
$DB->exec("OPTIMIZE TABLE `pre_order`");
showmsg('删除订单记录成功！',1);
}elseif($mod=='cleanlogsi' && $_POST['do']=='submit'){
$days = intval($_POST['days']);
if($days<=0)showmsg('请确保每项不能为空',3);
$DB->exec("DELETE FROM `pre_logs` WHERE addtime<'".date("Y-m-d H:i:s",strtotime("-{$days} days"))."'");
$DB->exec("OPTIMIZE TABLE `pre_logs`");
showmsg('删除登录记录成功！',1);
}else{
?>
<div class="panel panel-primary">
<div class="panel-heading"><h3 class="panel-title">系统数据清理</h3></div>
<div class="panel-body">
<a href="./clean.php?mod=cleancache" class="btn btn-block btn-default">清理设置缓存</a><br/>
<a href="./clean.php?mod=cleanlogs" onclick="return confirm('你确实要删除30天前的登录记录吗？');" class="btn btn-block btn-default">删除30天前聚合登录记录</a><br/>
<a href="./clean.php?mod=cleanorder" onclick="return confirm('你确实要删除30天前的订单记录吗？');" class="btn btn-block btn-default">删除30天前支付订单记录</a><br/>
<h4>自定义清理：</h4>
<form action="./clean.php?mod=cleansettlei" method="post" role="form"><input type="hidden" name="do" value="submit"/>
<b>聚合登录记录</b>：<input type="text" name="days" value="" placeholder="天数"/>天前的结算记录&nbsp;<input type="submit" name="submit" value="立即删除" class="btn btn-sm btn-danger" onclick="return confirm('删除后无法恢复，确定继续吗？');"/>
</form><br/>
<form action="./clean.php?mod=cleanlogsi" method="post" role="form"><input type="hidden" name="do" value="submit"/>
<b>支付订单记录</b>：<input type="text" name="days" value="" placeholder="天数"/>天前的订单记录&nbsp;<input type="submit" name="submit" value="立即删除" class="btn btn-sm btn-danger" onclick="return confirm('删除后无法恢复，确定继续吗？');"/>
</form><br/>
</div>
<div class="panel-footer">
<span class="glyphicon glyphicon-info-sign"></span>
定期清理数据有助于提升网站访问速度
</div>
</div>
<?php }?>
 </div>
</div>