<?php
$success	= false;
$data		= array();

if (!empty($error)) {
	$data['exception'] = array(
		'class' 	=> get_class($error),
		'code'		=> $error->getCode(),
		'message'	=> $error->getMessage(),
	);

	if (Configure::read('debug')) {
		$data['exception']['trace'] = preg_split('@\n@', $error->getTraceAsString());
		$previous = $error->getPrevious();
		if ($previous) {
			$data['previous'] = array(
				'class' => get_class($previous),
				'code' => $previous->getCode(),
				'message' => $previous->getMessage(),
				'trace' => preg_split('@\n@', $previous->getTraceAsString())
			);
		}

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
}

foreach ($_serialize as $key) {
	$data[$key] = $$key;
}

$out = json_encode(compact('success', 'data'));
if (Configure::read('debug')) {
	echo $this->JsonFormat->format($out);
} else {
	echo $out;
}
