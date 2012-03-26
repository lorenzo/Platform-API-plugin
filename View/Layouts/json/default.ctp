<?php
if (!isset($success)) {
	$success = null;
}
if (!isset($data)) {
	$data = null;
}

if (isset($this->Paginator) && $this->Paginator->defaultModel()) {
	$this->Paginator->options(array(
		'convertKeys' 	=> array('token'),
		'url' 			=> array('token' => Configure::read('Platform.AccessToken'))
	));

	$_pagination = $this->Paginator->request->paging;
	$_pagination = $_pagination[$this->Paginator->defaultModel()];

	$pagination = array(
		'pageCount' => $_pagination['pageCount'],
		'current'   => $_pagination['current'],
		'count' 	=> $_pagination['count']
	);

	if ($this->Paginator->hasPrev()) {
		$pagination['prev'] = Router::url($this->Paginator->url(array('page' => $_pagination['page'] - 1, 'ext' => 'json', 'token' => Configure::read('Platform.AccessToken'))), true);
	} else {
		$pagination['prev'] = false;
	}

	if ($this->Paginator->hasNext()) {
		$pagination['next'] = Router::url($this->Paginator->url(array('page' => $_pagination['page'] + 1, 'ext' => 'json', 'token' => Configure::read('Platform.AccessToken'))), true);
	} else {
		$pagination['next'] = false;
	}
}

echo json_encode(compact('success', 'data', 'pagination'));