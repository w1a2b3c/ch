<?php
include("../includes/common.php");
$title='用户信息';
include './head.php';
if($islogin==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");
?>
  <div class="container" style="padding-top:70px;">
    <div class="col-xs-12 col-sm-10 col-lg-8 center-block" style="float: none;">
<?php

$usergroup = [];
$select = '';
$rs = $DB->getAll("SELECT * FROM pre_group");
foreach($rs as $row){
	$usergroup[$row['gid']] = $row['name'];
	$select.='<option value="'.$row['gid'].'">'.$row['name'].'</option>';
}
unset($rs);

$my=isset($_GET['my'])?$_GET['my']:null;

if($my=='add')
{
echo '<div class="panel panel-primary">
<div class="panel-heading"><h3 class="panel-title">添加用户</h3></div>';
echo '<div class="panel-body">';
echo '<form action="./uset.php?my=add_submit" method="POST">
<div class="form-group">
<label>用户名:</label><br>
<input type="text" class="form-control" name="user" value="" required>
</div>
<div class="form-group">
<label>密码:</label><br>
<input type="text" class="form-control" name="pwd" value="" required>
</div>
<div class="form-group">
<label>手机号:</label><br>
<input type="text" class="form-control" name="phone" value="" placeholder="可留空">
</div>
<div class="form-group">
<label>邮箱:</label><br>
<input type="text" class="form-control" name="email" value="" placeholder="可留空">
</div>
<div class="form-group">
<label>ＱＱ:</label><br>
<input type="text" class="form-control" name="qq" value="" placeholder="可留空">
</div>
<div class="form-group">
<label>用户组:</label><br>
<select class="form-control" name="gid">'.$select.'</select>
</div>
<div class="form-group">
<label>用户组有效期:</label><br>
<select class="form-control" name="gexpire_set"><option value="0">永久</option><option value="1">设置日期</option></select>
<input type="date" class="form-control" name="gexpire" value="" id="gexpire_show">
</div>
<div class="form-group">
<label>用户状态:</label><br><select class="form-control" name="status"><option value="1">1_正常</option><option value="0">0_封禁</option></select>
</div>
<input type="submit" class="btn btn-primary btn-block" value="确定添加"></form>';
echo '<br/><a href="./ulist.php">>>返回用户列表</a>';
echo '</div></div>';
}
elseif($my=='edit')
{
$uid=intval($_GET['uid']);
$row=$DB->getRow("select * from pre_user where uid='$uid' limit 1");
if(!$row)showmsg('该用户不存在',4);
echo '<div class="panel panel-primary">
<div class="panel-heading"><h3 class="panel-title">修改用户信息</h3></div>';
echo '<div class="panel-body">';
echo '<form action="./uset.php?my=edit_submit&uid='.$uid.'" method="POST">
<div class="form-group">
<label>用户名:</label><br>
<input type="text" class="form-control" name="user" value="'.$row['user'].'" required>
</div>
<div class="form-group">
<label>用户余额:</label><br>
<input type="text" class="form-control" name="money" value="'.$row['money'].'" required>
</div>
<div class="form-group">
<label>手机号:</label><br>
<input type="text" class="form-control" name="phone" value="'.$row['phone'].'" placeholder="可留空">
</div>
<div class="form-group">
<label>邮箱:</label><br>
<input type="text" class="form-control" name="email" value="'.$row['email'].'" placeholder="可留空">
</div>
<div class="form-group">
<label>ＱＱ:</label><br>
<input type="text" class="form-control" name="qq" value="'.$row['qq'].'" placeholder="可留空">
</div>
<div class="form-group">
<label>用户组:</label><br>
<select class="form-control" name="gid" default="'.$row['gid'].'">'.$select.'</select>
</div>
<div class="form-group">
<label>用户组有效期:</label><br>
<select class="form-control" name="gexpire_set" default="'.($row['gexpire']==null?'0':'1').'"><option value="0">永久</option><option value="1">设置日期</option></select>
<input type="date" class="form-control" name="gexpire" value="'.$row['gexpire'].'" id="gexpire_show">
</div>
<div class="form-group">
<label>用户状态:</label><br><select class="form-control" name="status" default="'.$row['status'].'"><option value="1">1_正常</option><option value="0">0_封禁</option></select>
</div>
<div class="form-group">
<label>重置登录密码:</label><br>
<input type="text" class="form-control" name="pwd" value="" placeholder="不重置密码请留空">
</div>
<input type="submit" class="btn btn-primary btn-block" value="确定修改"></form>
';
echo '<br/><a href="./ulist.php">>>返回用户列表</a>';
echo '</div></div>
<script>
var items = $("select[default]");
for (i = 0; i < items.length; i++) {
	$(items[i]).val($(items[i]).attr("default")||0);
}
</script>';
}
elseif($my=='add_submit')
{
if(!checkRefererHost())exit();
$gid=$_POST['gid'];
$user=$_POST['user'];
$pwd=$_POST['pwd'];
$email=$_POST['email'];
$qq=$_POST['qq'];
$phone=$_POST['phone'];
$status=$_POST['status'];
$gexpire=$_POST['gexpire_set']==1?$_POST['gexpire']:null;
if($user==NULL or $pwd==NULL){
showmsg('保存错误,请确保加*项都不为空!',3);
} else {
$key = random(32);
$sql = "INSERT INTO `pre_user` (`gid`, `gexpire`, `user`, `phone`, `email`, `qq`, `money`, `addtime`, `status`) VALUES (:gid, :gexpire, :user, :phone, :email, :qq, '0.00', NOW(), :status)";
$data = [':gid'=>$gid, ':gexpire'=>$gexpire, ':user'=>$user, ':phone'=>$phone, ':email'=>$email, ':qq'=>$qq, ':status'=>$status];
$sds=$DB->exec($sql, $data);
$uid=$DB->lastInsertId();
if($sds){
	$pwd = getMd5Pwd(trim($pwd), $uid);
	$DB->exec("update `pre_user` set `pwd` ='{$pwd}' where `uid`='$uid'");
	showmsg('添加用户成功！UID:'.$uid.'<br/><a href="./ulist.php">>>返回用户列表</a>',1);
}else
	showmsg('添加用户失败！<br/>错误信息：'.$DB->error(),4);
}
}
elseif($my=='edit_submit')
{
if(!checkRefererHost())exit();
$uid=$_GET['uid'];
$rows=$DB->getRow("select * from pre_user where uid='$uid' limit 1");
if(!$rows)
	showmsg('当前用户不存在！',3);
$gid=$_POST['gid'];
$user=$_POST['user'];
$email=$_POST['email'];
$qq=$_POST['qq'];
$phone=$_POST['phone'];
$status=$_POST['status'];
$gexpire=$_POST['gexpire_set']==1?$_POST['gexpire']:null;
if($user==NULL){
showmsg('保存错误,请确保加*项都不为空!',3);
} else {
$sql = "UPDATE `pre_user` SET `gid`=:gid,`gexpire`=:gexpire,`user`=:user,`email`=:email,`qq`=:qq,`phone`=:phone,`status`=:status WHERE `uid`=:uid";
$data = [':gid'=>$gid, ':gexpire'=>$gexpire, ':user'=>$user, ':phone'=>$phone, ':email'=>$email, ':qq'=>$qq, ':status'=>$status, ':uid'=>$uid];
if(!empty($_POST['pwd'])){
	$pwd = getMd5Pwd(trim($_POST['pwd']), $uid);
	$sqs=$DB->exec("update `pre_user` set `pwd`='{$pwd}' where `uid`='$uid'");
}
if($DB->exec($sql,$data)!==false||$sqs)
	showmsg('修改用户信息成功！<br/><br/><a href="./ulist.php">>>返回用户列表</a>',1);
else
	showmsg('修改用户信息失败！'.$DB->error(),4);
}
}
elseif($my=='delete')
{
if(!checkRefererHost())exit();
$uid=$_GET['uid'];
$sql="DELETE FROM pre_user WHERE uid='$uid'";
if($DB->exec($sql))
	exit("<script language='javascript'>alert('删除用户成功！');history.go(-1);</script>");
else
	exit("<script language='javascript'>alert(''删除用户失败！".$DB->error()."');history.go(-1);</script>");
}
?>
    </div>
  </div>
<script>
$("select[name='gexpire_set']").change(function(){
	if($(this).val() == 1){
		$("#gexpire_show").show();
	}else{
		$("#gexpire_show").hide();
	}
});
$("select[name='gid']").change(function(){
	if($(this).val() == 0){
		$("select[name='gexpire_set']").val(0);
		$("select[name='gexpire_set']").change();
	}
});
$("select[name='gexpire_set']").change();
</script>