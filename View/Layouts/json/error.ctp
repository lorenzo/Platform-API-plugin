<?php
$success	= false;

if (Configure::read('debug') && !empty($error)) {
	$data['exception'] = array(
		'class' => get_class($error),
		'trace'	=> $error->getTraceAsString()
	);
}

if (Configure::read('debug') && !empty($_serialize)) {
	foreach ($_serialize as $key) {
		$data[$key] = $$key;
	}
}

echo json_encode(compact('success', 'data'));