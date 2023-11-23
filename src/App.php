<?php /** @noinspection PhpMultipleClassDeclarationsInspection */

/**
 *	@author		Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright	2021 Ceus Media
 */
namespace CeusMedia\HydrogenSourceIndexer;

use CeusMedia\Common\CLI;
use CeusMedia\Common\CLI\ArgumentParser as CliArgumentParser;
use CeusMedia\Common\FS\File\Writer as FileWriter;

/**
 *	@author		Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright	2021 Ceus Media
 */
class App
{
	/**	@var	ComposerSupport	$composerSupport		Component to read composer information */
	protected ComposerSupport $composerSupport;

	/**	@var	array			$neededComposerPackages	List of needed composer packages */
	protected array $neededComposerPackages		= [
		'ceus-media/common',
		'ceus-media/hydrogen-framework',
	];

	/**	@var	ModuleIndex		$moduleIndex	Component to list modules */
	protected ModuleIndex $moduleIndex;

	/**	@var	IniReader		$settings */
	protected IniReader $settings;

	/**	@var	string			$pathSource */
	protected string $pathSource;

	/**
	 *	@access		public
	 *	@return		void
	 */
	public function __construct( string $pathSource )
	{
		if( !CLI::checkIsCli( FALSE ) )
			die( 'This application is for CLI use, only.' );

		$this->pathSource		= $pathSource;
		$this->composerSupport	= new ComposerSupport();
		$this->checkComposerPackages();
		$this->moduleIndex		= new ModuleIndex( $pathSource.'src/' );
		$this->moduleIndex->setMode( ModuleIndex::MODE_FULL );
		$this->settings			= new IniReader( $pathSource );
		$p	= new CliArgumentParser();
		$p->parseArguments();
		$command	= current( (array) $p->get( 'commands' ) );
		switch( $command ){
			case 'serial':
				$renderer	= new SerialRenderer();
				$renderer->setSettings( $this->settings );
				$renderer->setModules( $this->moduleIndex->index() );
				FileWriter::save( $this->pathSource.'index.serial', $renderer->render() );
				echo 'Created index.serial.'.PHP_EOL;
				break;
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
				print( '  serial - creates serial index'.PHP_EOL );
				print( '  json   - creates JSON index'.PHP_EOL );
				print( '  html   - creates HTML index'.PHP_EOL );
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
