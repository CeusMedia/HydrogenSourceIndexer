<?php
/**
 *	@author		Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright	2021 Ceus Media
 */
namespace CeusMedia\HydrogenSourceIndexer;

use RuntimeException;
use function date;
use function json_encode;

/**
 *	@author		Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright	2021 Ceus Media
 */
class JsonRenderer
{
	/**	@var	array		$modules */
	protected $modules	= [];

	/**	@var	IniReader	$settings */
	protected $settings;

	/**	@var	boolean		$printPretty */
	protected $printPretty	= FALSE;

	/**
	 *	@access		public
	 *	@return		string
	 *	@throws		RuntimeException		if not settings are set
	 */
	public function render(): string
	{
		if( $this->settings === NULL )
			throw new RuntimeException( 'No settings set' );

		$data	= [
			'id'			=> $this->settings->get( 'id' ),
			'title'			=> $this->settings->get( 'title' ),
//			'version'		=> $this->settings->get( 'version' ),
			'url'			=> $this->settings->get( 'url' ),
			'description'	=> $this->settings->get( 'description' ),
			'date'			=> date( 'Y-m-d' ),
			'modules'		=> $this->modules,
		];
		$options	= 0;
		if( $this->printPretty )
			$options	|= JSON_PRETTY_PRINT;
		return (string) json_encode( $data, $options );
	}

	/**
	 *	@access		public
	 *	@param		boolean		$printPretty		Flag: use pretty print on JSON encode
	 *	@return		self
	 */
	public function setPrettyPrint( bool $printPretty ): self
	{
		$this->printPretty	= $printPretty;
		return $this;
	}

	/**
	 *	@access		public
	 *	@param		array		$modules		...
	 *	@return		self
	 */
	public function setModules( array $modules ): self
	{
		$this->modules	= $modules;
		return $this;
	}

	/**
	 *	@access		public
	 *	@param		IniReader	$settings		...
	 *	@return		self
	 */
	public function setSettings( IniReader $settings ): self
	{
		$this->settings	= $settings;
		return $this;
	}
}
