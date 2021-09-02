<?php
/**
 *	@author		Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright	2021 Ceus Media
 */
namespace CeusMedia\HydrogenSourceIndexer;

use CMF_Hydrogen_Environment_Resource_Module_Reader as HydrogenModuleReader;
use FS_File_RecursiveNameFilter as RecursiveFileNameIndex;

use Exception;
use RangeException;

/**
 *	@author		Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright	2021 Ceus Media
 */
class ModuleIndex
{
	const MODE_FULL		= 0;
	const MODE_MINIMAL	= 1;
	const MODE_REDUCED	= 2;

	const MODES			= [
		self::MODE_FULL,
		self::MODE_MINIMAL,
		self::MODE_REDUCED,
	];

	/**	@var	integer		$mode			Index mode */
	protected $mode	= self::MODE_REDUCED;

	/**	@var	string		$pathSource		Path to module source root */
	protected $pathSource;

	/**
	 *	@access		public
	 *	@param		string		$pathSource		Path to module source root
	 *	@return		void
	 */
	public function __construct( string $pathSource )
	{
		$this->pathSource	= $pathSource;
	}

	/**
	 *	@access		public
	 *	@param		integer|NULL	$mode		Index mode to set, see constants MODES
	 *	@return		array
	 */
	public function index( ?int $mode = NULL ): array
	{
		$mode	= $mode ?? $this->mode;
		$list	= array();
		$index	= new RecursiveFileNameIndex( $this->pathSource, 'module.xml' );
		$regExp	= '@^'.preg_quote( $this->pathSource, '@' ).'@';
		foreach( $index as $entry ){
			/** @var string $modulePath */
			$modulePath = preg_replace( $regExp, '', $entry->getPath() );
			/** @var string $id */
			$id			= str_replace( '/', '_', $modulePath );
			if( !is_int( preg_match( '@^[A-Z]@', $modulePath ) ) )
				continue;
			try{
				$module	= HydrogenModuleReader::load( (string) $entry->getPathname(), $id );
				switch( $mode ){
					case self::MODE_MINIMAL:
						$item	= (object) array(
							'title'			=> $module->title,
							'description'	=> $module->description,
							'version'		=> $module->version,
						);
						break;
					case self::MODE_REDUCED:
						$item	= $module;
						$item->path	= $modulePath;
						unset( $item->config );
						unset( $item->files );
						unset( $item->hooks );
						unset( $item->links );
						unset( $item->install );
						unset( $item->sql );
						unset( $item->versionInstalled );
						unset( $item->isInstalled );
						unset( $item->versionAvailable );
						unset( $item->jobs );
						unset( $item->file );
						unset( $item->uri );
						break;
					case self::MODE_FULL:
					default:
						$item	= $module;
				}
				$list[$id]	= $item;
			}
			catch( Exception $e ){
			}
		}
		ksort( $list );
		return $list;
	}

	/**
	 *	@access		public
	 *	@param		integer		$mode		Index mode to set, see constants MODES
	 *	@return		self
	 *	@throws		RangeException			if an invalid mode is given
	 */
	public function setMode( int $mode ): self
	{
		if( !in_array( $mode, self::MODES, TRUE ) )
			throw new RangeException( 'Invalid module index mode' );
		$this->mode	= $mode;
		return $this;
	}
}
