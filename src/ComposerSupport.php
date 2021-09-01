<?php
namespace CeusMedia\HydrogenSourceIndexer;

use Composer\InstalledVersions as Composer;

class ComposerSupport
{
	protected $installedComposerPackages	= [];

	public function __construct()
	{
		$this->checkComposer2();
		$this->installedComposerPackages	= Composer::getInstalledPackages();
	}

	public function getInstalledPackages(): array
	{
		return $this->installedComposerPackages;
	}

	public function isInstalledPackage( string $packageName ): bool
	{
		return in_array( $packageName, $this->installedComposerPackages, TRUE );
	}

	protected function checkComposer2()
	{
		if( !class_exists( Composer::class ) )
			die( 'Use composer 2, please!' );
	}

}
