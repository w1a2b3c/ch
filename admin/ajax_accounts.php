<?php
include("../includes/common.php");
if($islogin==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");
$act=isset($_GET['act'])?daddslashes($_GET['act']):null;

if(!checkRefererHost())exit('{"code":403}');

@header('Content-Type: application/json; charset=UTF-8');

switch($act){
case 'accountList': //第三方账号列表
	$logintype = [];
	$rs = $DB->getAll("SELECT name,showname FROM pre_type ORDER BY sort ASC");
	foreach($rs as $row){
		$logintype[$row['name']] = $row['showname'];
	}
	unset($rs);

	$sql=" 1=1";
	if(isset($_POST['uid']) && !empty($_POST['uid'])) {
		$uid = intval($_POST['uid']);
		$sql.=" AND `uid`='$uid'";
	}
	if(isset($_POST['appid']) && !empty($_POST['appid'])) {
		$appid = intval($_POST['appid']);
		$sql.=" AND `appid`='$appid'";
	}
	if(isset($_POST['type']) && !empty($_POST['type']) && $_POST['type']!='0') {
		$type = $_POST['type'];
		$sql.=" AND `type`='$type'";
	}
	if(isset($_POST['value']) && !empty($_POST['value'])) {
		if($_POST['column']=='nickname'){
			$sql.=" AND `{$_POST['column']}` like '%{$_POST['value']}%'";
		}else{
			$sql.=" AND `{$_POST['column']}`='{$_POST['value']}'";
		}
	}
	$offset = intval($_POST['offset']);
	$limit = intval($_POST['limit']);
	$total = $DB->getColumn("SELECT count(*) from pre_accounts WHERE{$sql}");
	$list = $DB->getAll("SELECT * FROM pre_accounts WHERE{$sql} order by id desc limit $offset,$limit");
	$list2 = [];
	foreach($list as $row){
		$row['typename'] = $logintype[$row['type']];
		$list2[] = $row;
	}

	exit(json_encode(['total'=>$total, 'rows'=>$list2]));
break;

case 'logList': //登录记录列表
	$logintype = [];
	$rs = $DB->getAll("SELECT name,showname FROM pre_type ORDER BY sort ASC");
	foreach($rs as $row){
		$logintype[$row['name']] = $row['showname'];
	}
	unset($rs);

	$sql=" status=1";
	if(isset($_POST['uid']) && !empty($_POST['uid'])) {
		$uid = intval($_POST['uid']);
		$sql.=" AND `uid`='$uid'";
	}
	if(isset($_POST['appid']) && !empty($_POST['appid'])) {
		$appid = intval($_POST['appid']);
		$sql.=" AND `appid`='$appid'";
	}
	if(isset($_POST['type']) && !empty($_POST['type']) && $_POST['type']!='0') {
		$type = $_POST['type'];
		$sql.=" AND `type`='$type'";
	}
	if(isset($_POST['value']) && !empty($_POST['value'])) {
		$sql.=" AND `{$_POST['column']}`='{$_POST['value']}'";
	}
	$offset = intval($_POST['offset']);
	$limit = intval($_POST['limit']);
	$total = $DB->getColumn("SELECT count(*) from pre_logs WHERE{$sql}");
	$list = $DB->getAll("SELECT * FROM pre_logs WHERE{$sql} order by id desc limit $offset,$limit");
	$list2 = [];
	foreach($list as $row){
		$row['typename'] = $logintype[$row['type']];
		$list2[] = $row;
	}

	exit(json_encode(['total'=>$total, 'rows'=>$list2]));
break;


default:
	exit('{"code":-4,"msg":"No Act"}');
break;
}