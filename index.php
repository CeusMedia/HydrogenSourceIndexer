<?php
use CeusMedia\HydrogenModules\Index\App;

use UI_DevOutput as DevOutput;
use Net_CURL as cURL;

( include_once __DIR__.'/vendor/autoload.php' ) or die( 'Install packages using composer, first!' );

error_reporting( E_ALL );
ini_set( 'display_errors', TRUE );

require_once '.index/CustomFileTrait.php';
require_once '.index/ComposerSupport.php';
require_once '.index/HtmlRenderer.php';
require_once '.index/JsonRenderer.php';
require_once '.index/ModuleIndex.php';
require_once '.index/IniReader.php';
require_once '.index/CliApp.php';
require_once '.index/WebApp.php';
require_once '.index/App.php';
require_once '.index/ModuleDescriptionRenderer.php';

new DevOutput;

/*	To test JSON request handling within a normal browser you can extend the index URL by ?json
 *	This will activate a JSON emulator, which will use cURL to request a JSON response.
 *	To allow the JSON emulator to be used, enable $jsonAllow.
 *	Originally, a JSON response is not pretty, since only servers/applications request JSON data.
 *	To prettify the JSON response, add &pretty to the request URL by enabling $jsonPretty.
 */
$jsonAllow	= !TRUE;
$jsonPretty	= !TRUE;

if( $jsonAllow && isset( $_GET['json'] ) ){
	$baseUrl	= vsprintf( '%s://%s/%s/?', [
		$_SERVER['REQUEST_SCHEME'],
		$_SERVER['HTTP_HOST'],
		dirname( $_SERVER['REQUEST_URI'] ),
	] );
	if( $jsonPretty )
		$baseUrl	.= '&pretty';
	if( isset( $_GET['list'] ) )
		$baseUrl	.= '&do=list';

	$c = new cURL( $baseUrl );
	$c->setOption( CURLOPT_HTTPHEADER, [ 'accept: application/json' ] );
	$response	= $c->exec();

	header( 'Content-Type: application/json' );
	print( $response );
	die;
}

new App();
