<?php
namespace CeusMedia\HydrogenSourceIndexer;

use CLI;
use CLI_ArgumentParser as CliArgumentParser;
use FS_File_Writer as FileWriter;

class App
{
	protected $composerSupport;

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

	public function getInstalledComposerPackages(): array
	{
		return $this->composerSupport->getInstalledPackages();
	}

	public function isInstalledComposerPackage( string $packageName ): bool
	{
		return $this->composerSupport->isInstalledPackage( $packageName );
	}

	protected function checkComposerPackages()
	{
		foreach( $this->neededComposerPackages as $neededPackage )
			if( !$this->composerSupport->isInstalledPackage( $neededPackage ) )
				die( 'Package "'.$neededPackage.'" needs to be installed (using composer).' );
	}
}
