// confirm
function confWriteCsv(){
	if(window.confirm('ダウンロードしますか？')){
	document.forms[0].submit();
	}else{
	window.alert('キャンセルされました。');
	}
}

function alt(){
	window.alert("TOPページに戻ります\nこれは警告です");
}