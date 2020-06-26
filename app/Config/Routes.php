<?php namespace Config;

use CodeIgniter\Exceptions\PageNotFoundException;
use Exception;

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

	try {
		$payload = file_get_contents('php://input');
		if(!$payload) {
			throw PageNotFoundException::forPageNotFound();
		}
	} catch(Exception $e) {
		throw PageNotFoundException::forPageNotFound();
	}

	$decoded_payload = json_decode($payload);

	if(is_array($decoded_payload->events)){
		$event = $decoded_payload->events[0];
		$trigger = '';
		switch($event->type) {
			case 'message':
				switch($event->message->type) {
					case 'text':
						log_message('info','TextTrigger');
						$trigger = 'OnText';
						break;

					case 'image':
						log_message('info','ImageTrigger');
						$trigger = 'OnImage';
						break;

					case 'video':
						log_message('info','VideoTrigger');
						$trigger = 'OnVideo';
						break;
						
					case 'audio':
						log_message('info','AudioTrigger');
						$trigger = 'OnAudio';
						break;
					
					case 'file':
						log_message('info','FileTrigger');
						$trigger = 'OnFile';
						break;

					case 'location':
						log_message('info','LocationTrigger');
						$trigger = 'OnLocation';
						break;
						
					case 'sticker':
						log_message('info','StickerTrigger');
						$trigger = 'OnSticker';
						break;
				}
				break;
			case 'follow':
				log_message('info','FollowTrigger');
				$trigger = 'OnFollow';
				break;

			case 'unfollow':
				log_message('info','UnfollowTrigger');
				$trigger = 'OnUnfollow';
				break;

			case 'postback':
				log_message('info','PostbackTrigger');
				$trigger = 'OnPostback';
				break;

			default:
				throw PageNotFoundException::forPageNotFound();
		}

		if($trigger) {
			$routes->add('/', 'LINE/'.$trigger.'::index');
		}
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
