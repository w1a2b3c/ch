<?php
include("../includes/common.php");
if($islogin==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");
$act=isset($_GET['act'])?daddslashes($_GET['act']):null;

if(!checkRefererHost())exit('{"code":403}');

@header('Content-Type: application/json; charset=UTF-8');

switch($act){
case 'getcount':
	$plugincount=$DB->getColumn("SELECT count(*) from pre_type");
	if($plugincount<5){
		\lib\Plugin::updateAll();
	}
	maintain_daily();
	$thtime=date("Y-m-d").' 00:00:00';
	$count1=$DB->getColumn("SELECT count(*) from pre_user");
	$count2=$DB->getColumn("SELECT count(*) from pre_apps");
	$count3=$DB->getColumn("SELECT count(*) from pre_accounts");
	$result=["code"=>0,"type"=>"online","count1"=>$count1,"count2"=>$count2,"count3"=>$count3];
	exit(json_encode($result));
break;

case 'set':
	foreach($_POST as $k=>$v){
		saveSetting($k, $v);
	}
	$ad=$CACHE->clear();
	if($ad)exit('{"code":0,"msg":"succ"}');
	else exit('{"code":-1,"msg":"修改设置失败['.$DB->error().']"}');
break;
case 'setGonggao':
	$id=intval($_GET['id']);
	$status=intval($_GET['status']);
	$sql = "UPDATE pre_anounce SET status='$status' WHERE id='$id'";
	if($DB->exec($sql))exit('{"code":0,"msg":"修改状态成功！"}');
	else exit('{"code":-1,"msg":"修改状态失败['.$DB->error().']"}');
break;
case 'delGonggao':
	$id=intval($_GET['id']);
	$sql = "DELETE FROM pre_anounce WHERE id='$id'";
	if($DB->exec($sql))exit('{"code":0,"msg":"删除公告成功！"}');
	else exit('{"code":-1,"msg":"删除公告失败['.$DB->error().']"}');
break;
case 'iptype':
	$result = [
	['name'=>'0_X_FORWARDED_FOR', 'ip'=>real_ip(0), 'city'=>get_ip_city(real_ip(0))],
	['name'=>'1_X_REAL_IP', 'ip'=>real_ip(1), 'city'=>get_ip_city(real_ip(1))],
	['name'=>'2_REMOTE_ADDR', 'ip'=>real_ip(2), 'city'=>get_ip_city(real_ip(2))]
	];
	exit(json_encode($result));
break;
case 'epayurl':
	$id = intval($_GET['id']);
	$conf['payapi']=$id;
	if($id>0 && $url = pay_api(false)){
		exit('{"code":0,"url":"'.$url.'"}');
	}else{
		exit('{"code":-1}');
	}
break;
case 'getServerIp':
	$ip = getServerIp();
	exit('{"code":0,"ip":"'.$ip.'"}');
break;


case 'getType':
	$name=trim($_GET['name']);
	$row=$DB->getRow("select * from pre_type where name='$name' limit 1");
	if(!$row)
		exit('{"code":-1,"msg":"当前登录方式不存在！"}');
	$result = ['code'=>0,'msg'=>'succ','data'=>$row];
	exit(json_encode($result));
break;
case 'saveType':
	$name=trim($_POST['name']);
	$third=intval($_POST['third']);
	$row=$DB->getRow("select * from pre_type where name='$name' limit 1");
	if(!$row)
		exit('{"code":-1,"msg":"当前登录方式不存在！"}');
	$sql = "UPDATE pre_type SET third='$third' WHERE name='$name'";
	if($DB->exec($sql)!==false)exit('{"code":0,"msg":"修改登录接口成功！"}');
	else exit('{"code":-1,"msg":"修改登录接口失败['.$DB->error().']"}');
break;
case 'setType':
	$name=trim($_GET['name']);
	$status=intval($_GET['status']);
	$row=$DB->getRow("select * from pre_type where name='$name' limit 1");
	if(!$row)
		exit('{"code":-1,"msg":"当前登录方式不存在！"}');
	if($status==1 && empty($row['config'])){
		exit('{"code":-1,"msg":"请先配置好密钥后再开启"}');
	}
	$sql = "UPDATE pre_type SET status='$status' WHERE name='$name'";
	if($DB->exec($sql)!==false)exit('{"code":0,"msg":"修改登录方式成功！"}');
	else exit('{"code":-1,"msg":"修改登录方式失败['.$DB->error().']"}');
break;
case 'typeInfo':
	$name=trim($_GET['name']);
	$row=$DB->getRow("select * from pre_type where name='$name' limit 1");
	if(!$row)
		exit('{"code":-1,"msg":"当前登录方式不存在！"}');
	if($row['third'] == 1){
		$info = \lib\Third::$info;
	}else{
		$info = \lib\Plugin::getConfig($name);
		if(!$info)exit('{"code":-1,"msg":"当前登录插件不存在！"}');
	}

	$config = json_decode($row['config'], true);

	$data = '<div class="modal-body"><form class="form" id="form-info">';

	foreach($info['input'] as $key=>$input){
		if($input['type'] == 'textarea'){
			$data .= '<div class="form-group"><label>'.$input['name'].'：</label><br/><textarea id="'.$key.'" name="'.$key.'" rows="2" class="form-control" placeholder="'.$input['note'].'">'.$config[$key].'</textarea></div>';
		}elseif($input['type'] == 'select'){
			$addOptions = '';
			foreach($input['options'] as $k=>$v){
				$addOptions.='<option value="'.$k.'" '.($config[$key]==$k?'selected':'').'>'.$v.'</option>';
			}
			$data .= '<div class="form-group"><label>'.$input['name'].'：</label><br/><select class="form-control" name="'.$key.'" default="'.$config[$key].'">'.$addOptions.'</select></div>';
		}else{
			$data .= '<div class="form-group"><label>'.$input['name'].'：</label><br/><input type="text" id="'.$key.'" name="'.$key.'" value="'.$config[$key].'" class="form-control" placeholder="'.$input['note'].'"/></div>';
		}
	}

	$help = $row['third'] == 1 ? \lib\Third::help() : api_call($name, $config, 'help');

	$data .= '<button type="button" id="save" onclick="saveInfo(\''.$name.'\')" class="btn btn-primary btn-block">保存</button></form><hr/>'.$help.'</div>';
	$result=array("code"=>0,"msg"=>"succ","data"=>$data);
	exit(json_encode($result));
break;
case 'saveTypeInfo':
	$name=trim($_GET['name']);
	$inputs = $_POST;
	foreach($inputs as $key=>$value){
		$inputs[$key] = trim($value);
	}
	$config = json_encode($inputs);
	$sql = "UPDATE pre_type SET config=:config WHERE name=:name";
	$data = [':config'=>$config, ':name'=>$name];
	if($DB->exec($sql, $data)!==false)exit('{"code":0,"msg":"修改对接密钥成功！"}');
	else exit('{"code":-1,"msg":"修改对接密钥失败['.$DB->error().']"}');
break;

case 'test':
	$appkey = $DB->getColumn("select appkey from pre_apps where appid='{$conf['test_appid']}' limit 1");
	if(!$appkey)exit('{"code":-1,"msg":"测试应用APPID不存在"}');
	$type = isset($_POST['type'])?$_POST['type']:exit('{"code":-1,"msg":"type不能为空"}');

	$Oauth_config['appid'] = $conf['test_appid'];
	$Oauth_config['appkey'] = $appkey;
	$Oauth=new \lib\TestLogin($Oauth_config);
	$arr = $Oauth->login($type);
	exit(json_encode($arr));
break;
default:
	exit('{"code":-4,"msg":"No Act"}');
break;
}