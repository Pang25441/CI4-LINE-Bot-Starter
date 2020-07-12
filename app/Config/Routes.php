<?php namespace Config;

use Exception;
use CodeIgniter\Exceptions\PageNotFoundException;
use LINE\LINEBot\SignatureValidator;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php'))
{
	require SYSTEMPATH . 'Config/Routes.php';
}

/**
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

/**
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::index');

$routes->add('linecontroller(:any)', function () {
	throw PageNotFoundException::forPageNotFound();
});

$routes->group('line-webhook', function($routes) {
	
	$request = \Config\Services::request();
	$line = new \Config\Line();
	$lineroute = new \Config\LineRoute();

	try {
		$payload = file_get_contents('php://input');
		if(!$payload) {
			return false;
		}
	} catch(Exception $e) {
		return false;
	}
	
	$decoded_payload = json_decode($payload);

	// Validate Signature
	$line_signature = $request->getServer('HTTP_X_LINE_SIGNATURE');
	if(!isset($line_signature) || !SignatureValidator::validateSignature($payload, $line->channelSecret, $line_signature)) 
	{
		// Signature Invalid
		return false;
	}

	if(is_array($decoded_payload->events)){
		$event = $decoded_payload->events[0];
		$trigger = '';

		if(!in_array($event->source->type, $lineroute->allowSource)  ) {
			return false;
		}

		if(isset($lineroute->map[$event->type])){
			if($event->type == 'message') {
				$trigger = isset($lineroute->map[$event->type][$event->message->type]) ? $lineroute->map[$event->type][$event->message->type] : null;
			} else {
				$trigger = $lineroute->map[$event->type];
			}
		} else {
			return false;
		}
		
		if($trigger) {
			log_message('info','Trigger: '.$trigger);
			$routes->add('/', 'LINE/'.$trigger.'::index');
		} else {
			log_message('info','Trigger: None');
			return false;
		}
	}
	else 
	{
		return false;
	}

});

/**
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need to it be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php'))
{
	require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
