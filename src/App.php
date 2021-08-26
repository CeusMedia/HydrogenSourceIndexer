<?php
namespace CeusMedia\HydrogenModules\Index;

use CLI;

class App
{
	protected $composerSupport;
	protected $neededComposerPackages		= [
		'ceus-media/common',
		'ceus-media/hydrogen-modules',
	];

	public function __construct()
	{
		$this->composerSupport	= new ComposerSupport();
		$this->checkComposerPackages();
		$this->isCli()
			? new CliApp( $this )
			: new WebApp( $this );
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

	protected function isCli()
	{
		return CLI::checkIsCLi( FALSE );
	}
}
