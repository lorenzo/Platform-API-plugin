<?php
$success	= false;
$data		= array();

if (Configure::read('debug') && !empty($error)) {
	$data['exception'] = array(
		'class' => get_class($error),
		'trace'	=> $error->getTraceAsString()
	);

	if (class_exists('ConnectionManager') && Configure::read('debug') > 1) {
		$sources = ConnectionManager::sourceList();

		$queryLog = array();
		foreach ($sources as $source) {
			$db = ConnectionManager::getDataSource($source);
			if (!method_exists($db, 'getLog')) {
				continue;
			}
			$data['queryLog'][$source] = $db->getLog();
		}
	}

}

foreach ($_serialize as $key) {
	$data[$key] = $$key;
}

echo json_encode(compact('success', 'data'));