<?php
/**
 *	@author		Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright	2021 Ceus Media
 */
namespace CeusMedia\HydrogenSourceIndexer;

use Composer\InstalledVersions as Composer;

/**
 *	@author		Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright	2021 Ceus Media
 */
class ComposerSupport
{
	/**	@var	array		$installedComposerPackages	List of installed composer packages */
	protected $installedComposerPackages	= [];

	/**
	 *	@access		public
	 *	@return		void
	 */
	public function __construct()
	{
		$this->checkComposer2();
		$this->installedComposerPackages	= Composer::getInstalledPackages();
	}

	/**
	 *	@access		public
	 *	@return		array
	 */
	public function getInstalledPackages(): array
	{
		return $this->installedComposerPackages;
	}

	/**
	 *	@access		public
	 *	@param		string		$packageName		Name of package to check
	 *	@return		boolean
	 */
	public function isInstalledPackage( string $packageName ): bool
	{
		return in_array( $packageName, $this->installedComposerPackages, TRUE );
	}

	/**
	 *	@access		protected
	 *	@return		void
	 */
	protected function checkComposer2()
	{
		if( !class_exists( Composer::class ) )
			die( 'Use composer 2, please!' );
	}
}
