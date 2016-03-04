<?php
include 'simple_html_dom.php';

function checkConfigure($configure){
	return ture;
}

function memcahce_init(){
	$mem = new Memcache;
	$mem->connect("127.0.0.1", 11111);
	return $mem;
}

?>
