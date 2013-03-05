// confirm
function confWriteCsv(){
	if(window.confirm('CSVデータを書き出しますか？')){
	document.forms[0].submit();
	}else{
	window.alert('キャンセルされました。');
	}
}

function alt(){
	window.alert("TOPページに戻ります\nこれは警告です");
}