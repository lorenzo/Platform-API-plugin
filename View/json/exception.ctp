<?php
$this->set('success', false);
$code = Configure::read('ResponseObject')->statusCode();

if (Configure::read('debug') > 0 ) {
	$trace = Common::stripRealPaths($error->getTraceAsString());
}

$this->set('data', compact('code', 'name', 'trace'));