<?php
include("../includes/common.php");
$title='支付订单';
include './head.php';
if($islogin==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");
?>
  <div class="container" style="padding-top:70px;">
    <div class="col-xs-12 col-md-10 center-block" style="float: none;">
	<div id="searchToolbar">
	    <form onsubmit="return searchSubmit()" method="GET" class="form-inline">
	        <div class="form-group">
          	<label>搜索</label>
		  	<select name="column" class="form-control"><option value="trade_no">订单号</option><option value="api_trade_no">接口订单号</option><option value="name">商品名称</option><option value="money">金额</option><option value="addtime">创建时间</option></select>
		    </div>
			<div class="form-group" id="searchword">
			  <input type="text" class="form-control" name="value" placeholder="搜索内容">
			</div>
			<div class="form-group" id="searchword">
			  <input type="text" class="form-control" name="uid" placeholder="用户UID">
			</div>
			 <div class="form-group">
				<button class="btn btn-primary" type="submit"><i class="fa fa-search"></i>搜索</button>&nbsp;
				<a href="javascript:searchClear()" class="btn btn-default"><i class="fa fa-repeat"></i>重置</a>
			 </div>
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
$(document).ready(function(){
	updateToolbar();
	const defaultPageSize = 30;
	const pageNumber = typeof window.$_GET['pageNumber'] != 'undefined' ? parseInt(window.$_GET['pageNumber']) : 1;
	const pageSize = typeof window.$_GET['pageSize'] != 'undefined' ? parseInt(window.$_GET['pageSize']) : defaultPageSize;

	$("#listTable").bootstrapTable({
		url: 'ajax_user.php?act=orderList',
		pageNumber: pageNumber,
		pageSize: pageSize,
		classes: 'table table-striped table-hover table-bottom-border',
		columns: [
			{
				field: 'trade_no',
				title: '订单号',
				formatter: function(value, row, index) {
					return '<b>'+value+'</b>';
				}
			},
			{
				field: 'uid',
				title: 'UID',
				formatter: function(value, row, index) {
					return '<a href="ulist.php?column=uid&value='+value+'" target="_blank">'+value+'</a>';
				}
			},
			{
				field: 'name',
				title: '商品名称'
			},
			{
				field: 'money',
				title: '订单金额'
			},
			{
				field: 'type',
				title: '支付方式'
			},
			{
				field: 'addtime',
				title: '创建时间'
			},
			{
				field: 'endtime',
				title: '完成时间'
			},
			{
				field: 'status',
				title: '支付状态',
				formatter: function(value, row, index) {
					return value==1?'<font color=green>已支付</font>':'<font color=blue>未支付</font>';
				}
			},
		],
	})
})
</script>