<?php
$this->set('success', false);
$code = Configure::read('ResponseObject')->statusCode();
$this->set('data', compact('code', 'name'));