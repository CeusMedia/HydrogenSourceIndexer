<?php
namespace CeusMedia\HydrogenSourceIndexer;

class JsonRenderer
{
	/**	@var	array		$modules */
	protected $modules	= [];

	/**	@var	IniReader	$settings */
	protected $settings;

	/**	@var	boolean		$printPretty */
	protected $printPretty	= FALSE;

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
		return json_encode( $data, $options );
	}

	public function setPrettyPrint( bool $printPretty ): self
	{
		$this->printPretty	= $printPretty;
		return $this;
	}

	public function setModules( array $modules ): self
	{
		$this->modules	= $modules;
		return $this;
	}

	public function setSettings( IniReader $settings ): self
	{
		$this->settings	= $settings;
		return $this;
	}
}
