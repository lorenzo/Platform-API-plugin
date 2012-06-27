<?php
if (!isset($success)) {
	$success = null;
}
if (!isset($data)) {
	$data = null;
}

if (isset($this->Paginator) && $this->Paginator->defaultModel()) {
	$_pagination = $this->Paginator->request->paging;
	$_pagination = $_pagination[$this->Paginator->defaultModel()];

	$extra_paginator_url_params = empty($extra_paginator_url_params) ? array() : $extra_paginator_url_params;

	$pagination = array(
		'pageCount' => $_pagination['pageCount'],
		'current' => $_pagination['page'],
		'count' => $_pagination['count']
	);

	if ($this->Paginator->hasPrev()) {
		$pagination['prev'] = Router::url($this->Paginator->url($extra_paginator_url_params + array('page' => $_pagination['page'] - 1, 'ext' => 'json', '?' => array('token' => $apiAccessToken)), true), true);
	} else {
		$pagination['prev'] = false;
	}

	if ($this->Paginator->hasNext()) {
		$pagination['next'] = Router::url($this->Paginator->url($extra_paginator_url_params + array('page' => $_pagination['page'] + 1, 'ext' => 'json', '?' => array('token' => $apiAccessToken)), true), true);
	} else {
		$pagination['next'] = false;
	}
}

if (class_exists('ConnectionManager') && Configure::read('debug') > 1) {
	$sources = ConnectionManager::sourceList();

	$queryLog = array();
	foreach ($sources as $source) {
		$db = ConnectionManager::getDataSource($source);
		if (!method_exists($db, 'getLog')) {
			continue;
		}
		$queryLog[$source] = $db->getLog(false, false);
	}
}

$out = json_encode(compact('success', 'data', 'pagination', 'queryLog'));

if (Configure::read('debug')) {
	$out = $this->JsonFormat->format($out);
}

if ($allowJsonp && !empty($this->params->query['callback'])) {
	printf('%s(%s)', $this->params->query['callback'], $out);
} else {
	echo $out;
}
