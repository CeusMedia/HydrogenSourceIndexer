<?php
/**
 *	@author		Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright	2021 Ceus Media
 */
namespace CeusMedia\HydrogenSourceIndexer;

use CLI;
use CLI_ArgumentParser as CliArgumentParser;
use FS_File_Writer as FileWriter;

/**
 *	@author		Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright	2021 Ceus Media
 */
class App
{
	/**	@var	ComposerSupport	$composerSupport		Component to read composer information */
	protected $composerSupport;

	/**	@var	array			$neededComposerPackages	List of needed composer packages */
	protected $neededComposerPackages		= [
		'ceus-media/common',
		'ceus-media/hydrogen-framework',
	];

	/**	@var	ModuleIndex		$moduleIndex	Component to list modules */
	protected $moduleIndex;

	/**	@var	IniReader		$settings */
	protected $settings;

	/**	@var	string			$pathSource */
	protected $pathSource;

	/**
	 *	@access		public
	 *	@return		void
	 */
	public function __construct( string $pathSource )
	{
		if( !CLI::checkIsCLi( FALSE ) )
			die( 'This application is for CLI use, only.' );

		$this->pathSource		= $pathSource;
		$this->composerSupport	= new ComposerSupport();
		$this->checkComposerPackages();
		$this->moduleIndex	= new ModuleIndex( $pathSource );
		$this->settings		= new IniReader( $pathSource );
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
				FileWriter::save( $this->pathSource.'index.json', $renderer->render() );
				echo 'Created index.json.'.PHP_EOL;
				break;
			case 'html':
				$renderer	= new HtmlRenderer();
				$renderer->setSourcePath( $this->pathSource );
				$renderer->setSettings( $this->settings );
				$renderer->setModules( $this->moduleIndex->index() );
				FileWriter::save( $this->pathSource.'index.html', $renderer->render() );
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

	/**
	 *	@access		protected
	 *	@return		void
	 */
	protected function checkComposerPackages()
	{
		foreach( $this->neededComposerPackages as $neededPackage )
			if( !$this->composerSupport->isInstalledPackage( $neededPackage ) )
				die( 'Package "'.$neededPackage.'" needs to be installed (using composer).' );
	}
}
