<?php
/* It's my personal policy to not obfuscate the overall flow of the application
 * by burying it inside a god app object which has a "run" function invoked 
 * here, this only serves to slow things down a bit and make it harder for a 
 * developer to understand the high level flow.
 *
 * Each segment of the application flow is clearly outlined below. If a 
 * problem is identified, this approach will allow you to significantly reduce 
 * the size of a stack trace and arrive at the heart of the problem sooner,
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Loading App Environment

$documentRoot = __DIR__;
$serverRoot   = dirname(__DIR__);
$viewRoot     = $serverRoot . '/src/views/';
$vendorRoot   = $serverRoot . '/vendor';

require_once($serverRoot . '/app/autoload.php');
$config       = require_once($serverRoot . '/app/appEnv.php');
$context      = array(
	'debug'        => $config['debug'],
	'documentRoot' => $documentRoot,
	'serverRoot'   => $serverRoot
);

// Interpreting Requests
/* At the moment, there's only one page associated with the application so 
 * there's not much need for processing requests that isn't already done on 
 * the webserver side (rewrite all to index.php). However, since we should 
 * be able to handle a 404 response for any requests that wouldn't make sense,
 * we are still going to interpret the request.
 */
$requestPath  = $_SERVER['REQUEST_URI'];
$path         = explode('/', str_replace($documentRoot, '', $requestPath));
$path         = array_values(array_filter($path));
$queryFields  = isset($_GET) ? $_GET : array();
$postFields   = isset($_POST) ? $_POST : array();

// Routing Requests
/* Again, only one page to be served so it doesn't make sense to implement a 
 * router just yet. The policy is that the router returns three values for 
 * dispatching upon, $previous, $obj, and $isEndpoint. $obj is traditionally 
 * an application controller instance. $previous is a spliced portion of the 
 * request path to be used as a reference to a static value or method 
 * available on the controller. For example, a request made to 
 * https://example.com/users/head using GET (this is functionally equivalent
 * to a HEAD request to /users for API clients unable to use other methods 
 * than GET/POST in my typical API implementations), this would translate 
 * into using the UsersController and invoking the "head" method on it to 
 * return the headers one would receive in a normal GET request.
 */

// Default 404 Response
/* This is just a basic 404 response to cover any requests that cannot resolve
 * normally. These values are overridden from the dispatch step if applicable.
 */
$response = [
	'HTTPStatusCode' => '404',
	'view'           => 'error/noResource',
	'title'          => 'Not Found'
];

// Dispatch
/* This is the activation of the translated "intended action" from the
 * routing process. This is explicitly separated from routing as the router
 * may be deprecated, underperformant or otherwise unusable in the future, 
 * allowing future developers to update the router component without needing
 * to completely dismantle and rebuild the application to use the new one.
 */
$dispatchResponse = [];

if (empty($path)) {
	// Default dispatch response generated for valid HTTP requests.
	$defaultController = new \Http\Controllers\RootController($context);
	$dispatchResponse = $defaultController();
	/* This interaction here is an example of the final steps in dispatching
	 * a response (defaultController is $obj, null is $previous, $isEndpoint
	 * is true).
	 */

	$response = array_merge($response, $dispatchResponse);
}
// Preparing For Template Usage
/* Like anything else, the desire here is to allow for parts to be swapped 
 * freely without significantly impacting the rest of the application. We 
 * may not always be using this particular templating engine in the future.
 * At this point, the only thing we should have is the data for the template 
 * engine to consume.
 */
$layoutFile = ':master';

if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
	/* If it's an AJAX request, we can skip the layout file and simply render 
	 * the individual view as the "chrome" found in the master file would 
	 * already be present on the page. This behavior allows for treating 
	 * a regular web page as a single page application would. Additionally 
	 * if you have a sub-component on the page, such as a dashboard statistic,
	 * this would prevent having to define a separate rendering process just 
	 * for sub-components
	 */
	$layoutFile = null;
}

// Content Negotiation
/* At the moment, we're only expecting standard text/html requests but this 
 * would be the place to make use of the HTTP_ACCEPT header to present the 
 * content differently, for example, rendering the data as json, XML or other 
 * common formats.
 */

// Applying Template Engine
/* Since we're not using content negotiation right now, this was unwrapped
 * from its original if-statement. If content negotiaton is used, this section 
 * should be inside the appropriate conditional for text/html purposes
 */
require_once $vendorRoot . '/Templating/phptenjin-0.0.2/lib/Tenjin.php';

$properties = array('postfix'=>'.phtml', 'prefix'=>$viewRoot, 'layout'=>$layoutFile, 'cache'=>false, 'preprocess'=>true);

$engine = new \Tenjin_Engine($properties);

$visualResponse = $engine->render(":" . $response['view'], $response);



echo $visualResponse;