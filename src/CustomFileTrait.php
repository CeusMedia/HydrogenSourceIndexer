<?php
namespace CeusMedia\HydrogenSourceIndexer;

use DomainException;
use function file_exists;
use function is_null;

trait CustomFileTrait
{
	protected $pathSource;

	public function getCustomFile( string $fileName ): string
	{
		if( is_null( $pathSource ) )
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

	public function setSourcePath( string $pathSource ): self
	{
		$this->pathSource	= $pathSource;
		return $this;
	}
}
