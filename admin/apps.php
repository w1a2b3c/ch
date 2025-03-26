<?php
include("../includes/common.php");
$title='应用列表';
include './head.php';
if($islogin==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");
?>
  <div class="container" style="padding-top:70px;">
	<div class="col-xs-12 center-block" style="float: none;">

<div id="searchToolbar">
<form onsubmit="return searchSubmit()" method="GET" class="form-inline">
  <div class="form-group">
	<label>搜索</label>
	<select name="column" class="form-control"><option value="appid">APPID</option><option value="name">应用名称</option><option value="url">应用网址</option></select>
  </div>
  <div class="form-group">
	<input type="text" class="form-control" name="value" placeholder="搜索内容">
  </div>
  <div class="form-group">
	<input type="text" class="form-control" name="uid" style="width: 100px;" placeholder="UID" value="">
  </div>
  <div class="form-group">
	<select name="dstatus" class="form-control"><option value="-1">全部状态</option><option value="1">状态正常</option><option value="0">状态关闭</option><option value="2">状态待审核</option></select>
  </div>
  <div class="form-group">
	<button class="btn btn-primary" type="submit"><i class="fa fa-search"></i>搜索</button>&nbsp;
	<a href="javascript:searchClear()" class="btn btn-default"><i class="fa fa-repeat"></i>重置</a>
  </div>
  &nbsp;<a href="./edit.php?my=add" class="btn btn-success"><i class="fa fa-plus"></i>添加应用</a>
</form>
</div>

      <table id="listTable">
	  </table>
	</div>
  </div>
<script src="<?php echo $cdnpublic?>layer/3.1.1/layer.min.js"></script>
<script src="<?php echo $cdnpublic?>bootstrap-table/1.20.2/bootstrap-table.min.js"></script>
<script src="<?php echo $cdnpublic?>bootstrap-table/1.20.2/extensions/page-jump-to/bootstrap-table-page-jump-to.min.js"></script>
<script src="../assets/js/custom.js"></script>
<script>
function setStatus(appid,status) {
	$.ajax({
		type : 'GET',
		url : 'ajax_apps.php?act=setApp&appid='+appid+'&status='+status,
		dataType : 'json',
		success : function(data) {
			if(data.code == 0){
				searchSubmit();
			}else{
				layer.msg(data.msg, {icon:2, time:1500});
			}
		},
		error:function(data){
			layer.msg('服务器错误');
			return false;
		}
	});
}
function auditApp(appid) {
	$.ajax({
		type : 'GET',
		url : 'ajax_apps.php?act=getAppInfo&appid='+appid,
		dataType : 'json',
		success : function(data) {
			if(data.code == 0){
				layer.open({
					area: ['360px'],
					title: '应用审核操作',
					content: '<div class="form-group"><select class="form-control" name="status"><option value="2" '+(data.status==2?'selected':null)+'>待审核</option><option value="1">审核通过</option><option value="3" '+(data.status==3?'selected':null)+'>审核不通过</option></select></div><div class="form-group"><textarea class="form-control" name="note" placeholder="审核不通过原因" rows="2">'+(data.note==null?'':data.note)+'</textarea></div>',
					yes: function(){
						var status = $("select[name='status']").val();
						var note = $("textarea[name='note']").val();
						$.ajax({
							type : 'POST',
							url : 'ajax_apps.php?act=auditApp',
							data : {appid:appid, status:status, note:note},
							dataType : 'json',
							success : function(data) {
								if(data.code == 0){
									searchSubmit();
									layer.msg(data.msg, {icon:1});
								}else{
									layer.alert(data.msg);
								}
							},
							error:function(data){
								layer.msg('服务器错误');
								return false;
							}
						});
					}
				});
			}else{
				layer.alert(data.msg);
			}
		},
		error:function(data){
			layer.msg('服务器错误');
			return false;
		}
	});
}
function showDomains(appid) {
	var ii = layer.load(2, {shade:[0.1,'#fff']});
	$.ajax({
		type : 'GET',
		url : 'ajax_apps.php?act=showDomains&appid='+appid,
		dataType : 'json',
		success : function(data) {
			layer.close(ii);
			if(data.code == 0){
				var item = '<table class="table table-condensed table-hover">';
				$.each(data.data, function(k,v){
					item += '<tr><td>'+v+'</td></tr>';
				});
				item += '<tr><td class="info"><a href="./edit.php?my=editdomain&appid='+appid+'">修改域名白名单</a></td></tr>';
				item += '</table>';
				layer.open({
				  type: 1,
				  shadeClose: true,
				  title: '查看域名白名单',
				  skin: 'layui-layer-rim',
				  content: item
				});
			}else{
				layer.msg(data.msg, {icon:2, time:1500});
			}
		},
		error:function(data){
			layer.msg('服务器错误');
			return false;
		}
	});
}

$(document).ready(function(){
	updateToolbar();
	const defaultPageSize = 20;
	const pageNumber = typeof window.$_GET['pageNumber'] != 'undefined' ? parseInt(window.$_GET['pageNumber']) : 1;
	const pageSize = typeof window.$_GET['pageSize'] != 'undefined' ? parseInt(window.$_GET['pageSize']) : defaultPageSize;

	$("#listTable").bootstrapTable({
		url: 'ajax_apps.php?act=appList',
		pageNumber: pageNumber,
		pageSize: pageSize,
		classes: 'table table-striped table-hover table-bottom-border',
		columns: [
			{
				field: 'appid',
				title: 'APPID',
				formatter: function(value, row, index) {
					return '<b>'+value+'</b>';
				}
			},
			{
				field: 'name',
				title: '应用名称'
			},
			{
				field: 'url',
				title: '应用网址',
				formatter: function(value, row, index) {
					return '<a href="'+value+'" target="_blank" rel="noopener noreferrer">'+value+'</a>';
				}
			},
			{
				field: 'uid',
				title: '所属用户',
				formatter: function(value, row, index) {
					return '<a href="./ulist.php?column=uid&value='+value+'" target="_blank">'+row.user+'['+value+']</a>';
				}
			},
			{
				field: 'addtime',
				title: '添加时间'
			},
			{
				field: 'status',
				title: '状态',
				formatter: function(value, row, index) {
					var appid = row.appid;
					if(value == 4){
						return '<a href="javascript:"><font color=red><i class="fa fa-trash-o"></i>已删除</font></a>';
					}else if(value == 3){
						return '<a href="javascript:auditApp('+appid+')"><font color=red><i class="fa fa-exclamation-circle"></i>未通过</font></a>';
					}else if(value == 2){
						return '<a href="javascript:auditApp('+appid+')"><font color=orange><i class="fa fa-asterisk"></i>待审核</font></a>';
					}else if(value == 1){
						return '<a href="javascript:setStatus('+appid+',0)"><font color=green><i class="fa fa-check-circle"></i>开启</font></a>';
					}else{
						return '<a href="javascript:setStatus('+appid+',1)"><font color=red><i class="fa fa-times-circle"></i>关闭</font></a>';
					}
				}
			},
			{
				field: '',
				title: '操作',
				formatter: function(value, row, index) {
					var appid = row.appid;
					return '<a href="./accounts.php?appid='+appid+'" class="btn btn-xs btn-default">账号</a>&nbsp;<a href="./logs.php?appid='+appid+'" class="btn btn-xs btn-default">记录</a>&nbsp;<a href="javascript:showDomains('+appid+')" class="btn btn-xs btn-default">域名</a>&nbsp;<a href="./edit.php?my=edit&appid='+appid+'" class="btn btn-xs btn-info">编辑</a>&nbsp;<a href="./edit.php?my=del&appid='+appid+'" class="btn btn-xs btn-danger" onclick="return confirm(\'你确实要删除此记录吗？\');">删除</a>';
				},
				
			},
		],
	})
})
</script>