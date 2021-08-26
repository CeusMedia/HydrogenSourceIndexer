<?php
namespace CeusMedia\HydrogenModules\Index;

use CLI_ArgumentParser as CliArgumentParser;
use FS_File_Writer as FileWriter;

class CliApp
{
	/**	@var	App				$parentApp		Main (=calling) app instance */
	protected $parentApp;

	/**	@var	ModuleIndex		$moduleIndex	Component to list modules */
	protected $moduleIndex;

	/**	@var	IniReader		$settings */
	protected $settings;

	public function __construct( App $parentApp )
	{
		$this->parentApp	= $parentApp;
		$this->moduleIndex	= new ModuleIndex();
		$this->settings		= new IniReader();
		print_m( $this->settings->getAll() );
		$p = new CliArgumentParser();
		$p->parseArguments();
		$command	= current($p->get('commands'));
		switch( $command ){
			case 'json':
			case 'json-dev':
				$renderer	= new JsonRenderer();
				$renderer->setSettings( $this->settings );
				$renderer->setModules( $this->moduleIndex->index() );
				$renderer->setPrettyPrint( $command === 'json-dev' );
				FileWriter::save( 'index.json', $renderer->render() );
				echo 'Created index.json.'.PHP_EOL;
				break;
			case 'html':
				$renderer	= new HtmlRenderer();
				$renderer->setSettings( $this->settings );
				$renderer->setModules( $this->moduleIndex->index() );
				FileWriter::save( 'index.html', $renderer->render() );
				echo 'Created index.html.'.PHP_EOL;
				break;
			default:
				print( 'Usage: php index.php COMMAND'.PHP_EOL );
				print( 'Commands:'.PHP_EOL );
				print( '  json - creates JSON index'.PHP_EOL );
				print( '  html - creates HTML index'.PHP_EOL );
				die();
		}
		exit;
	}
}
