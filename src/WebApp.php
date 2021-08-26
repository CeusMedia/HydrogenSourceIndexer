<?php
namespace CeusMedia\HydrogenModules\Index;

use Exception;
use Net_HTTP_Header_Field as HttpHeader;
use Net_HTTP_Request_Receiver as HttpRequest;
use Net_HTTP_Response as HttpResponse;
use Net_HTTP_Response_Sender as HttpResponseSender;
use UI_HTML_Exception_Page as ExceptionHtmlPage;

class WebApp
{
	/**	@var	HttpRequest			$request			HTTP request object */
	protected $request;

	/**	@var	HttpResponse		$response			HTTP response object */
	protected $response;

	/**	@var	ModuleIndexApp		$parentApp			Main (=calling) app instance */
	protected $parentApp;

	/**	@var	ModuleIndex			$moduleIndex		Component to list modules */
	protected $moduleIndex;

	/**	@var	IniReader			$settings */
	protected $settings;

	/**	@var	array				$acceptedMimeTypes	Component to MIME types accepted by client */
	protected $acceptedMimeTypes	= [];

	/**	@var	array				$supportedMimeTypes	Component to MIME types supported by server */
	protected $supportedMimeTypes	= [
		'text/html',
		'application/json',
	];

	/**	@var	string				$negotiatedMimeType	Negiotated MIME type accepted by client and supported by server */
	protected $negotiatedMimeType	= '';

	public function __construct( App $parentApp )
	{
		$this->parentApp		= $parentApp;
		$this->request			= new HttpRequest();
		$this->response			= new HttpResponse();
		$this->moduleIndex		= new ModuleIndex();
		$this->settings			= new IniReader();

		$this->negotiateMimeType();
		try{
			$this->dispatch();
			$sender	= new HttpResponseSender( $this->response );
			$sender->send();
		}
		catch( Exception $e ){
			die( ExceptionHtmlPage::render( $e ) );
		}
		exit;
	}

	protected function dispatch()
	{
		switch( $this->negotiatedMimeType ){
			case 'application/json':
				$this->response->setBody( $this->dispatchJson() );
//				$this->response->addHeaderPair( 'Content-type', $accept );
				break;
			case 'text/html':
				$renderer	= new HtmlRenderer();
				$renderer->setSettings( $this->settings );
				$renderer->setModules( $this->moduleIndex->index() );
				$this->response->setBody( $renderer->render() );
				break;
		}
	}

	protected function dispatchJson(): string
	{
		$options	= [];
		if( $this->request->has( 'pretty' ) )
			$options	|= JSON_PRETTY_PRINT;
		switch( $this->request->get( 'do' ) ){
			case 'list':
				return json_encode( $this->moduleIndex->index(), $options );
			case 'index':
			default:
				return json_encode( $this->moduleIndex->index(), $options );
		}
	}

	protected function ensureAcceptHeader()
	{
		$accept = $this->request->getHeadersByName( 'accept' );
		if( count( $accept ) === 0 ){
			$accept = array( new HttpHeader( 'accept', 'text/html;q=1' ) );
			$this->request->getHeaders()->addField( $accept );
		}
	}

	protected function negotiateMimeType()
	{
		$this->ensureAcceptHeader();
		$acceptedMimeTypes	= array_keys( $this->qualifyAcceptedMimeTypes() );
		$commonMimeTypes	= array_intersect( $acceptedMimeTypes, $this->supportedMimeTypes );
		$mimeType			= current( $commonMimeTypes );
		if( $mimeType === FALSE )
			die( 'Requested MIME type is not supported' );
		$this->negotiatedMimeType	= $mimeType;
		$this->response->addHeaderPair( 'Content-type', $mimeType );
	}

	protected function qualifyAcceptedMimeTypes(): array
	{
		$accepts	= [];
		foreach( $this->request->getHeadersByName( 'accept' ) as $accept )
			$accepts	= array_merge( $accepts, $accept->getValue( TRUE ) );
		return $accepts;
	}
}
