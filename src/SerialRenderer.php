<?php /** @noinspection PhpMultipleClassDeclarationsInspection */

/**
 *	@author		Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright	2021 Ceus Media
 */
namespace CeusMedia\HydrogenSourceIndexer;

use RuntimeException;
use function date;

/**
 *	@author		Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright	2021 Ceus Media
 */
class SerialRenderer
{
	/**	@var	array		$modules */
	protected array $modules			= [];

	/**	@var	?IniReader	$settings */
	protected ?IniReader $settings		= NULL;

	/**
	 *	@access		public
	 *	@return		string
	 *	@throws		RuntimeException		if not settings are set
	 */
	public function render(): string
	{
		if( $this->settings === NULL )
			throw new RuntimeException( 'No settings set' );

		$data	= (object) [
			'id'			=> $this->settings->get( 'id' ),
			'title'			=> $this->settings->get( 'title' ),
//			'version'		=> $this->settings->get( 'version' ),
			'url'			=> $this->settings->get( 'url' ),
			'description'	=> $this->settings->get( 'description' ),
			'date'			=> date( 'Y-m-d' ),
			'modules'		=> $this->modules,
		];
		return serialize( $data );
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
