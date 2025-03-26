<?php
include("../includes/common.php");
if($islogin==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");
$act=isset($_GET['act'])?daddslashes($_GET['act']):null;

if(!checkRefererHost())exit('{"code":403}');

@header('Content-Type: application/json; charset=UTF-8');

switch($act){
case 'appList': //应用列表
	$sql=" 1=1";
	if(isset($_POST['uid']) && !empty($_POST['uid'])) {
		$uid = intval($_POST['uid']);
		$sql.=" AND A.`uid`='$uid'";
	}
	if(isset($_POST['dstatus']) && $_POST['dstatus']>-1) {
		$dstatus = intval($_POST['dstatus']);
		$sql.=" AND A.status={$dstatus}";
	}
	if(isset($_POST['value']) && !empty($_POST['value'])) {
		if($_POST['column']=='url'||$_POST['column']=='name'){
			$sql.=" AND A.`{$_POST['column']}` like '%{$_POST['value']}%'";
		}else{
			$sql.=" AND A.`{$_POST['column']}`='{$_POST['value']}'";
		}
	}
	$offset = intval($_POST['offset']);
	$limit = intval($_POST['limit']);
	$total = $DB->getColumn("SELECT count(*) from pre_apps A WHERE{$sql}");
	$list = $DB->getAll("SELECT A.*,B.user FROM pre_apps A LEFT JOIN pre_user B ON A.uid=B.uid WHERE{$sql} order by appid desc limit $offset,$limit");

	exit(json_encode(['total'=>$total, 'rows'=>$list]));
break;

case 'setApp': //修改应用状态
	$appid=intval($_GET['appid']);
	$status=intval($_GET['status']);
	$sql = "UPDATE pre_apps SET status='$status' WHERE appid='$appid'";
	if($DB->exec($sql)!==false)exit('{"code":0,"msg":"修改应用成功！"}');
	else exit('{"code":-1,"msg":"修改应用失败['.$DB->error().']"}');
break;
case 'auditApp': //审核应用
	$appid=intval($_POST['appid']);
	$status=intval($_POST['status']);
	$note=$status==3?trim($_POST['note']):null;
	$sql = "UPDATE pre_apps SET status='$status',note=:note WHERE appid='$appid'";
	if($DB->exec($sql, [':note'=>$note])!==false)exit('{"code":0,"msg":"修改应用成功！"}');
	else exit('{"code":-1,"msg":"修改应用失败['.$DB->error().']"}');
break;
case 'getAppInfo': //获取应用信息
	$appid=intval($_GET['appid']);
	$row = $DB->getRow("select * from pre_apps where appid='$appid'");
	if(!$row)exit('{"code":-1,"msg":"应用不存在"}');
	exit(json_encode(['code'=>0, 'status'=>$row['status'], 'note'=>$row['note']]));
break;
case 'showDomains': //获取应用域名白名单
	$appid=intval($_GET['appid']);
	$rows=$DB->getAll("select * from pre_appdomain where appid='$appid'");
	$data = [];
	foreach($rows as $row){
		$data[] = $row['domain'];
	}
	$result = ['code'=>0,'msg'=>'succ','data'=>$data];
	exit(json_encode($result));
break;

default:
	exit('{"code":-4,"msg":"No Act"}');
break;
}