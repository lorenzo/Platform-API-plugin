<?php
class ApiUtility {
	protected function __construct() {

	}

	/**
	* Tries to find the AccessToken from a long range of entry points
	*
	* @param CakeRequest $request
	*/
	public static function getRequestToken(CakeRequest $request) {
		// Check if the token is posted to the action
		if (!empty($request->data['token'])) {
			return $request->data['token'];
		}

		// Check if the token is passed as a query argument (?token=$token)
		if (!empty($request->query['token'])) {
			return $request->query['token'];
		}

		// Check if the token is passed as named argument (/token:$token)
		if (!empty($request->params['named']['token'])) {
			return $request->params['named']['token'];
		}

		// Check if the token is passed as a HTTP header
		if (!empty($_SERVER['HTTP_TOKEN'])) {
			return $_SERVER['HTTP_TOKEN'];
		}

		return;
	}
}