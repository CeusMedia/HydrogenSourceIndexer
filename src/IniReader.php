<?php /** @noinspection PhpMultipleClassDeclarationsInspection */

/**
 *	@author		Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright	2021 Ceus Media
 */
namespace CeusMedia\HydrogenSourceIndexer;

use CeusMedia\Common\ADT\Collection\Dictionary as Dictionary;
use CeusMedia\Common\FS\File\INI\Reader as IniFileReader;

use DomainException;

/**
 *	@author		Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright	2021 Ceus Media
 */
class IniReader extends Dictionary
{
	use CustomFileTrait;

	/**	@var	string		$fileName	Name of settings file */
	protected string $fileName	= '.index.ini';

	/**
	 *	@access		public
	 *	@param		string		$pathSource		Path to module source root
	 *	@return		void
	 */
	public function __construct( string $pathSource )
	{
		$this->setSourcePath( $pathSource );
		try{
			$filePath	= $this->getCustomFile( $this->fileName );
		}
		catch( DomainException $e ){
			die( $e->getMessage() );
		}
		parent::__construct( array_merge( [
			'id'				=> NULL,
			'title'				=> NULL,
			'url'				=> NULL,
			'description'		=> NULL,
		], IniFileReader::loadArray( $filePath ) ) );
	}
}
