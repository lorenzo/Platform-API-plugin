# Introduction

__todo__

## Requirements

* CakePHP 2.1
* PHP 5.3
* The Crud plugin ( https://github.com/nodesagency/Platform-Crud-Plugin )
* An Auth handler that will validate and login a user by an access_token (included in Controller/Auth/TokenAuthentication.php)

## Cloning and loading

### With a simple git clone

```
git clone git://github.com/nodesagency/Platform-API-Plugin.git app/Plugin/Api
```

### As a git submodule

```
git submodule add git://github.com/nodesagency/Platform-API-Plugin.git app/Plugin/Api
```

# Loading
Add the following to your __app/Config/bootstrap.php__

```php
<?php
CakePlugin::load('Api', array('bootstrap' => true, 'routes' => true));
?>
```

In your (app) controller load the Crud component

```php
<?php
/**
 * Application wide controller
 *
 * @abstract
 * @package App.Controller
 */
abstract class AppController extends Controller {
	/**
	* List of global controller components
	*
	* @cakephp
	* @var array
	*/
	public $components = array(
		// Enable Api component
		'Api.Api',

		// -- Make sure Crud component is loaded *after* Api --
	);
}
?>
```

When the component is loaded the following new CakeRequest detectors will be available

### Test if the request is an API call

```php
<?php
$this->request->is('api');
?>
```

### Test if the current request prefers JSON or has .json extension

```php
<?php
$this->request->is('json');
?>
```

### Make a controller API action public (doesn't require access token)

```php
<?php
$this->Api->allowPublic('add');
?>
```

### Make a controller API action protected (require access token)

```php
<?php
$this->Api->denyPublic('add');
?>
```

If the current request is deemed to be an API request, the component will automatically switch the View object in the Controller to Api.ApiView
The plugin will automatically enforce basic access control

* If the current request is an API call
* There isn't a already active Session
* Throws ForbiddenException if no active session is in place and an access token is missing
* Throws ForbiddenException if Auth Component fails to authenticate a user based on the token
* The component also handles redirects in a more API friendly way

If the redirect code is 404 the 404 header will be sent without any body

If the redirect code is 301 or 302 the Header location is sent, as well as JSON body with the response code and url

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
* views/$Controller/$format/$action.ctp

__By default all our models and controllers should be callable in a REST style manner__

Basically you need to ensure two variables is always present in the layout in API calls

* $success
* $data

If you don't pass these variables from your controller, you can set the variables in your API views like this

```php
<?php
$validationErrors = $this->Form->validationErrors;
$validationErrors = array_filter($validationErrors);

$this->set('success', empty($validationErrors));
$this->set('data', $validationErrors);
?>
````

### Allow jsonp in your beforeFilter()

```php
<?php
$this->Api->allowJsonp(); // Allow jsonp
$this->Api->allowJsonp(true); // Allow jsonp (Same as line above)
$this->Api->allowJsonp(false); // Deny jsonp
?>
```

### Sample App Controller class with Crud and Api

Your controller (With Authentication, Crud and API loaded)

```php
<?php
abstract class AppController extends Controller {
	/**
	* List of global controller components
	*
	* @cakephp
	* @var array
	*/
	public $components = array(
		// Enable Sessions (optional)
		'Session',

		// Enable authentication
		'Auth' => array(
			'authorize' => array(
				'Controller'
			),
			'authenticate' => array(
				// Allow authentication by user / password
				'Form',

				// Allow authentication by access token
				'Api.Token',
			)
		),

		// Enable API views (make sure its before Crud)
		'Api.Api',

		// Enable CRUD actions
		'Crud.Crud' => array(
			'actions' => array('index', 'add', 'edit', 'view', 'delete')
		),
	);
}
?>
```