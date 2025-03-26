const ui_alert = (function(){
    let alertBackground=null; 
    let alertDiv = null;
    return {
        show: function(message) {
            if (!alertBackground) {
                alertBackground = document.createElement("div");
                alertBackground.className = 'ui-dialog-mask';
            }
            if (!alertDiv) {
                alertDiv = document.createElement('div');
                alertDiv.className = 'ui-dialog-box';
            }
            alertDiv.innerHTML = '<div class="ui-dialog-content">' + message + '</div><div class="ui-dialog-bottom" onclick="ui_alert.hide()">关闭</div>';
            document.body.appendChild(alertBackground);
            document.body.appendChild(alertDiv);
        },
        hide: function() {
            document.body.removeChild(alertBackground);
            document.body.removeChild(alertDiv);
        }
    }
})();
const ui_load = (function(){
	let loadingDiv=null;
    let isLoading=false;
	return {
		isLoading: isLoading,
		startLoading: function() {
			if (!loadingDiv) {
				loadingDiv = document.createElement("div");
				loadingDiv.className = 'ui-loading-mask';
			}
			document.body.appendChild(loadingDiv);
			isloading = true;
		},
		endLoading: function() {
			document.body.removeChild(loadingDiv);
			isloading = false;
		}
	}
})();
const error_tips = function(message){
    $("#error_message").html(message);
    $("#error_tips").removeClass('hide');
    clearTimeout(window.errTipClock);
	window.errTipClock = setTimeout(function(){ $("#error_tips").addClass('hide');}, 3000);
}