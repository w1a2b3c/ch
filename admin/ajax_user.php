<?php
include("../includes/common.php");
if($islogin==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");
$act=isset($_GET['act'])?daddslashes($_GET['act']):null;

if(!checkRefererHost())exit('{"code":403}');

@header('Content-Type: application/json; charset=UTF-8');

switch($act){
case 'userList': //用户列表
	$usergroup = [0=>'默认用户组'];
	$rs = $DB->getAll("SELECT * FROM pre_group");
	foreach($rs as $row){
		$usergroup[$row['gid']] = $row['name'];
	}
	unset($rs);

	$sql=" 1=1";
	if(isset($_POST['dstatus']) && $_POST['dstatus']>-1) {
		$dstatus = intval($_POST['dstatus']);
		$sql.=" AND status={$dstatus}";
	}
	if(isset($_POST['value']) && !empty($_POST['value'])) {
		$sql.=" AND `{$_POST['column']}`='{$_POST['value']}'";
	}
	$offset = intval($_POST['offset']);
	$limit = intval($_POST['limit']);
	$total = $DB->getColumn("SELECT count(*) from pre_user WHERE{$sql}");
	$list = $DB->getAll("SELECT * FROM pre_user WHERE{$sql} order by uid desc limit $offset,$limit");
	$list2 = [];
	foreach($list as $row){
		if($row['gexpire']!=null && strtotime($row['gexpire'])<time()){
			$DB->exec("UPDATE pre_user SET gid=0,gexpire=NULL WHERE uid='{$row['uid']}'");
			$row['gid']=0;
		}
		$row['gname'] = $usergroup[$row['gid']];
		$list2[] = $row;
	}

	exit(json_encode(['total'=>$total, 'rows'=>$list2]));
break;

case 'logList': //用户操作日志列表
	$sql=" 1=1";
	if(isset($_POST['value']) && !empty($_POST['value'])) {
		$sql.=" AND `{$_POST['column']}`='{$_POST['value']}'";
	}
	$offset = intval($_POST['offset']);
	$limit = intval($_POST['limit']);
	$total = $DB->getColumn("SELECT count(*) from pre_userlog WHERE{$sql}");
	$list = $DB->getAll("SELECT * FROM pre_userlog WHERE{$sql} order by id desc limit $offset,$limit");

	exit(json_encode(['total'=>$total, 'rows'=>$list]));
break;

case 'orderList': //支付订单列表
	$sql=" 1=1";
	if(isset($_POST['uid']) && !empty($_POST['uid'])) {
		$uid = intval($_POST['uid']);
		$sql.=" AND `uid`='$uid'";
	}
	if(isset($_POST['value']) && !empty($_POST['value'])) {
		$sql.=" AND `{$_POST['column']}`='{$_POST['value']}'";
	}
	$offset = intval($_POST['offset']);
	$limit = intval($_POST['limit']);
	$total = $DB->getColumn("SELECT count(*) from pre_order WHERE{$sql}");
	$list = $DB->getAll("SELECT * FROM pre_order WHERE{$sql} order by trade_no desc limit $offset,$limit");

	exit(json_encode(['total'=>$total, 'rows'=>$list]));
break;

case 'getGroup': //用户组
	$gid=intval($_GET['gid']);
	$row=$DB->getRow("select * from pre_group where gid='$gid' limit 1");
	if(!$row)
		exit('{"code":-1,"msg":"当前用户组不存在！"}');
	$result = ['code'=>0,'msg'=>'succ','gid'=>$gid,'name'=>$row['name'],'info'=>json_decode($row['info']),'ucount'=>$row['ucount']];
	exit(json_encode($result));
break;
case 'delGroup':
	$gid=intval($_GET['gid']);
	$row=$DB->getRow("select * from pre_group where gid='$gid' limit 1");
	if(!$row)
		exit('{"code":-1,"msg":"当前用户组不存在！"}');
	$sql = "DELETE FROM pre_group WHERE gid='$gid'";
	if($DB->exec($sql))exit('{"code":0,"msg":"删除用户组成功！"}');
	else exit('{"code":-1,"msg":"删除用户组失败['.$DB->error().']"}');
break;
case 'saveGroup':
	$logintype = [];
	$rs = $DB->getAll("SELECT * FROM pre_type WHERE status=1 ORDER BY sort ASC");
	foreach($rs as $row){
		$logintype[$row['name']] = $row['showname'];
	}
	unset($rs);
	if($_POST['action'] == 'add'){
		$name=trim($_POST['name']);
		$row=$DB->getRow("select * from pre_group where name='$name' limit 1");
		if($row)
			exit('{"code":-1,"msg":"用户组名称重复"}');
		$info=$_POST['info'];
		foreach($info as $type=>$v){
			unset($logintype[$type]);
		}
		$info = [];
		foreach($logintype as $type=>$typename){
			$info[$type]='0';
		}
		$info=json_encode($info);
		$ucount=intval($_POST['ucount']);
		$sql = "INSERT INTO pre_group (name, info, ucount) VALUES ('{$name}', '{$info}', '{$ucount}')";
		if($DB->exec($sql))exit('{"code":0,"msg":"新增用户组成功！"}');
		else exit('{"code":-1,"msg":"新增用户组失败['.$DB->error().']"}');
	}elseif($_POST['action'] == 'changebuy'){
		$gid=intval($_POST['gid']);
		$status=intval($_POST['status']);
		$sql = "UPDATE pre_group SET isbuy='{$status}' WHERE gid='$gid'";
		if($DB->exec($sql))exit('{"code":0,"msg":"修改上架状态成功！"}');
		else exit('{"code":-1,"msg":"修改上架状态失败['.$DB->error().']"}');
	}else{
		$gid=intval($_POST['gid']);
		$name=trim($_POST['name']);
		$row=$DB->getRow("select * from pre_group where name='$name' and gid<>$gid limit 1");
		if($row)
			exit('{"code":-1,"msg":"用户组名称重复"}');
		$info=$_POST['info'];
		foreach($info as $type=>$v){
			unset($logintype[$type]);
		}
		$info = [];
		foreach($logintype as $type=>$typename){
			$info[$type]='0';
		}
		$info=json_encode($info);
		$ucount=intval($_POST['ucount']);
		$sql = "UPDATE pre_group SET name='{$name}',info='{$info}',ucount='{$ucount}' WHERE gid='$gid'";
		if($DB->exec($sql)!==false)exit('{"code":0,"msg":"修改用户组成功！"}');
		else exit('{"code":-1,"msg":"修改用户组失败['.$DB->error().']"}');
	}
break;
case 'saveGroupPrice':
	$prices = $_POST['price'];
	$expires = $_POST['expire'];
	$sorts = $_POST['sort'];
	foreach($prices as $gid=>$item){
		$price = trim($item);
		$expire = trim($expires[$gid]);
		$sort = trim($sorts[$gid]);
		if(empty($price)||!is_numeric($price))exit('{"code":-1,"msg":"GID:'.$gid.'的售价填写错误"}');
		$DB->exec("UPDATE pre_group SET price='{$price}',sort='{$sort}',expire='{$expire}' WHERE gid='$gid'");
	}
	exit('{"code":0,"msg":"保存成功！"}');
break;

case 'setUser':
	$uid=intval($_GET['uid']);
	$type=trim($_GET['type']);
	$status=intval($_GET['status']);
	if($type=='group')$sql = "UPDATE pre_user SET gid='$status',gexpire=NULL WHERE uid='$uid'";
	else $sql = "UPDATE pre_user SET status='$status' WHERE uid='$uid'";
	if($DB->exec($sql)!==false)exit('{"code":0,"msg":"修改用户成功！"}');
	else exit('{"code":-1,"msg":"修改用户失败['.$DB->error().']"}');
break;
case 'recharge':
	$uid=intval($_POST['uid']);
	$do=$_POST['actdo'];
	$rmb=floatval($_POST['rmb']);
	$row=$DB->getRow("select uid,money from pre_user where uid='$uid' limit 1");
	if(!$row)
		exit('{"code":-1,"msg":"当前用户不存在！"}');
	if($do==1 && $rmb>$row['money'])$rmb=$row['money'];
	if($do==0){
		changeUserMoney($uid, $rmb, true, '后台加款');
	}else{
		changeUserMoney($uid, $rmb, false, '后台扣款');
	}
	exit('{"code":0,"msg":"succ"}');
break;
default:
	exit('{"code":-4,"msg":"No Act"}');
break;
}