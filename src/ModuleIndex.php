<?php
namespace CeusMedia\HydrogenModules\Index;

use Exception;
use FS_File_RecursiveNameFilter as RecursiveFileNameIndex;
use CMF_Hydrogen_Environment_Resource_Module_Reader as HydrogenModuleReader;
use RangeException;

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

	protected $mode	= self::MODE_REDUCED;

	public function index( ?int $mode = NULL ): array
	{
		$mode ?? $this->mode;
		$list	= array();
		$index	= new RecursiveFileNameIndex( './', 'module.xml' );
		foreach( $index as $entry ){
			$id		= preg_replace( '@^./@', '', $entry->getPath() );
			if( !preg_match( '@^[A-Z]@', $id ) )
				continue;
			$id		= str_replace( '/', '_', $id );
			try{
				$module	= HydrogenModuleReader::load( $entry->getPathname(), $id );
				switch( $mode ){
					case self::MODE_MINIMAL:
						$module	= (object) array(
							'title'			=> $module->title,
							'description'	=> $module->description,
							'version'		=> $module->version,
						);
						break;
					case self::MODE_REDUCED:
						$module->path	= preg_replace( '@^\./@', '', dirname( $module->file ) );
						unset( $module->config );
						unset( $module->files );
						unset( $module->hooks );
						unset( $module->links );
						unset( $module->install );
						unset( $module->sql );
						unset( $module->versionInstalled );
						unset( $module->isInstalled );
						unset( $module->versionAvailable );
						unset( $module->jobs );
						unset( $module->file );
						unset( $module->uri );
						break;
				}
				$list[$id]	= $module;
			}
			catch( Exception $e ){
			}
		}
		ksort( $list );
		return $list;
	}

	public function setMode( array $mode ): self
	{
		if( !in_array( $mode, self::MODES ) )
			throw new RangeException( 'Invalid module index mode' );
		$this->mode	= $mode;
		return $this;
	}
}
