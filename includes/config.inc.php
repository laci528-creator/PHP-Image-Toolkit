<?php 
define("TESTBETRIEB",false);

if(TESTBETRIEB) {
	error_reporting(E_ALL);
	ini_set("display_errors",1);
}
else {
	error_reporting(E_ALL);
	ini_set("display_errors",0);
}

define("MAX_RESIZE_FILES", 20);
define("MAX_CONVERT_FILES", 6);
define("MAX_WATERMARK_FILES", 10);
?>