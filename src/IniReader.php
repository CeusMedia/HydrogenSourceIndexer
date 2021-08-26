<?php
namespace CeusMedia\HydrogenModules\Index;

use ADT_List_Dictionary as Dictionary;
use FS_File_INI_Reader as IniFileReader;
use DomainException;

class IniReader extends Dictionary
{
	use CustomFileTrait;

	protected $fileName	= '.index.ini';

	public function __construct()
	{
		try{
			$filePath	= $this->getCustomFile( $this->fileName );
		}
		catch( DomainException $e ){
			die( $e->getMessage() );
		}
		parent::__construct( array_merge( [
			'id'			=> NULL,
			'title'			=> NULL,
			'url'			=> NULL,
			'description'	=> NULL,
		], IniFileReader::load( $filePath ) ) );
	}
}
