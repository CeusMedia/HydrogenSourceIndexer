<?php
namespace CeusMedia\HydrogenModules\Index;

use DomainException;
use function file_exists;
use function getCwd;

trait CustomFileTrait
{
	public function getCustomFile( string $fileName ): string
	{
		$fileLocal		= getCwd().'/'.$fileName;
		$fileIndexer	= __DIR__.'/'.$fileName;

		if( file_exists( $fileLocal ) )
			$filePath	= $fileLocal;
		else if( file_exists( $fileIndexer ) )
			$filePath	= $fileIndexer;
		else
			throw new DomainException( 'File \''.$fileName.'\' is missing.' );
		return $filePath;
	}
}
