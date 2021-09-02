<?php
/**
 *	@author		Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright	2021 Ceus Media
 */
namespace CeusMedia\HydrogenSourceIndexer;

use DomainException;
use RuntimeException;
use function file_exists;
use function is_null;

/**
 *	@author		Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright	2021 Ceus Media
 */
trait CustomFileTrait
{
	/**	@var	string|NULL		$pathSource */
	protected $pathSource;

	/**
	 *	...
	 *	@access		public
	 *	@param		string		$fileName
	 *	@return		string
	 */
	public function getCustomFile( string $fileName ): string
	{
		if( is_null( $this->pathSource ) )
			throw new RuntimeException( 'Path to source is not set' );
		$fileLocal		= $this->pathSource.$fileName;
		$fileIndexer	= __DIR__.'/'.$fileName;

		if( file_exists( $fileLocal ) )
			$filePath	= $fileLocal;
		else if( file_exists( $fileIndexer ) )
			$filePath	= $fileIndexer;
		else
			throw new DomainException( 'File \''.$fileName.'\' is missing.' );
		return $filePath;
	}

	/**
	 *	Set path to module source.
	 *	@access		public
	 *	@param		string		$pathSource
	 *	@return		self
	 */
	public function setSourcePath( string $pathSource ): self
	{
		$this->pathSource	= $pathSource;
		return $this;
	}
}
