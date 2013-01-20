<?php
include('./Config/parameters.php');
\He\Trace::mute();

/**
 * Search for the file required in the get var "file"
 */
if(file_exists(ROOT_PATH.'site/page/'.$_GET['file'].'.php')) {
	/**
	 * View sequence, ob_start() is used to manage the localisation keys
	 */
	ob_start();
	include(ROOT_PATH.'site/page/'.$_GET['file'].'.php');
	$send = ob_get_contents();
	ob_end_clean();

	echo Localise::run($send);
}