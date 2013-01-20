<?php
include('./Config/parameters.php');

ob_start();
try {
	/**
	 * View sequence, ob_start() is used to manage the localisation keys
	 */
	include(ROOT_PATH.'site/layout.php');
	$send = ob_get_contents();
	ob_end_clean();

	echo Localise::run($send);

	if(DEBUG) {
		\He\Trace::dump();
	}
} catch (Exception $exc) {
	if(DEBUG) {
		\He\Trace::addTrace('CRASH : '.$exc->getMessage(), 'GLOBAL', -2);
		\He\Trace::addTrace('DETAIL : '.$exc->getTraceAsString(), 'GLOBAL', -2);
		
		$send = ob_get_contents();
		ob_end_clean();
		
		include(ROOT_PATH.'site/layout_top.php');
		echo Localise::run($send);
		include(ROOT_PATH.'site/layout_bottom.php');
		
		\He\Trace::dump(true);
		exit(300);
	} else {
		exit(500);
	}
}