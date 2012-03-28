<?php
$validationErrors = $this->Form->validationErrors;
$validationErrors = array_filter($validationErrors);

$this->set('success', empty($validationErrors));

if (!empty($validationErrors)) {
	$this->set('data', $validationErrors);
}