<?php
function throw_403($message){
	header('HTTP/1.1 403 Forbidden');
	print($message);
}

function throw_404($message){
	header('HTTP/1.1 404 Not Found');
	print($message);
}