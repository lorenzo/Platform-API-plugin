Usae:

Add the component to your AppController

	public $components = array(
	    'Api.Api'
	);

It will add the following new features

	// Test if this is an API call
	$this->request->is('api')

	// Test if the current request prefers JSON
	$this->request->is('json')

It will also write the provided accessToken to Configure

	Configure::read('Platform.AccessToken')

The plugin will automatically enforce basic access control

* If the current request is an API call
* There isn't a already active Session
* Throws ForbiddenException if no active session is in place and an access token is missing
* Throws ForbiddenException if Auth Component fails to authenticate a user based on the token

The plugin also handles redirects in a more API friendly way

* If the redirect code is 404 the 404 header will be sent
* IF the redirect code is 301 or 302 the Header location is sent, as well as JSON body with the response code and url

There is baked in default views for the following actions

* index
* add
* edit
* delete
* view
* redirect

If will fall back to these default views if it cannot find your own custom API views

You can put your view files in the following paths - both for app and plugins

* views/$Controller/api/$action.ctp
* views/$Controller/json/$action.ctp

If you use the backed in index, add, edit, delete, view and redirect actions in the platform and backend, it will ensure the correct variables is passed to these views in API mode

Please see https://wiki.ournodes.com/display/platform/API for valid API urls for both platform and backend

BY default all our models and controllers should be callable in a REST style manner - remember to add them to the wiki

Basically you need to ensure two variables is always present in the layout in API calls

	success
	data

If you don't pass these variables from your controller, you can set the variables in your API views like this

	<?php
	$validationErrors = $this->Form->validationErrors;
	$validationErrors = array_filter($validationErrors);

	$this->set('success', empty($validationErrors));
	$this->set('data', $validationErrors);
	?>