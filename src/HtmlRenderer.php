<?php /** @noinspection PhpMultipleClassDeclarationsInspection */

/**
 *	@author		Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright	2021 Ceus Media
 */
namespace CeusMedia\HydrogenSourceIndexer;

use CeusMedia\Common\FS\File\Reader as FileReader;
use CeusMedia\Common\UI\HTML\PageFrame as HtmlPage;
use CeusMedia\Common\UI\HTML\Tag as HtmlTag;

use DomainException;
use RuntimeException;

/**
 *	@author		Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright	2021 Ceus Media
 */
class HtmlRenderer
{
	use CustomFileTrait;

	/** @var array $modules */
	protected array $modules		= [];

	/** @var ?IniReader $settings */
	protected ?IniReader $settings	= NULL;

	/**
	 *	@access		public
	 *	@return		string
	 */
	public function render(): string
	{
		if( $this->settings === NULL )
			throw new RuntimeException( 'No settings set' );

		try{
			$template	= FileReader::load( $this->getCustomFile( '.index.html' ) );
		}
		catch( DomainException $e ){
			$page		= new HtmlPage();
			$page->addJavaScript( 'https://cdn.ceusmedia.de/js/jquery/1.10.2.min.js' );
			$page->addJavaScript( 'https://cdn.ceusmedia.de/js/bootstrap/2.3.2/bootstrap.min.js' );
			$page->addStylesheet( 'https://cdn.ceusmedia.de/css/bootstrap/2.3.2/bootstrap.min.css' );
//			$page->addStylesheet( 'html.css' );
			$page->addBody( '<div class="container"><div class="hero-unit"><h2>{{ title }}</h2>{{ description }}</div>{{ modules }}</div>' );
			$template	= $page->build();
		}

		if( count( $this->modules ) === 0 )
			throw new RuntimeException( 'No modules given or available' );
		$modules	= $this->renderModules();

		$data	= [
			'id'			=> $this->settings->get( 'id' ),
			'title'			=> $this->settings->get( 'title' ),
			'version'		=> $this->settings->get( 'version' ),
			'url'			=> $this->settings->get( 'url' ),
			'description'	=> $this->settings->get( 'description' ),
			'date'			=> $this->settings->get( 'id' ),
			'modules'		=> $modules,
		];
		/**
		 * @var string $placeholder
		 * @var string $content
		 */
		foreach( $data as $placeholder => $content )
			$template	= str_replace( '{{ '.$placeholder.' }}', $content, $template );
		return $template;
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

	/**
	 *	@access		protected
	 *	@return		string
	 */
	protected function renderModules(): string
	{
		$descriptionRenderer	= new ModuleDescriptionRenderer();
		$list	= [];
		foreach( $this->modules as $moduleId => $module ){
			$descriptionRenderer->setContent( (string) $module->description );
			$description	= $descriptionRenderer->render();
			$id				= preg_replace( '@[^a-z0-9]@i', '-', $moduleId );
			$list[]	= HtmlTag::create( 'div', [
				HtmlTag::create( 'div', [
					HtmlTag::create( 'a', [
						HtmlTag::create( 'span', $module->title, ['class' => 'module-title'] ),
						'&nbsp;',
						HtmlTag::create( 'small', 'v'.$module->version, ['class' => 'module-version muted'] ),
					], [
						'class'		=> 'accordion-toggle',
						'href'		=> '#collapse-'.$id,
					], [
						'toggle'	=> 'collapse',
						'parent'	=> '#accordion-modules',
					] ),
				], ['class' => 'accordion-heading'] ),
				HtmlTag::create( 'div', [
					HtmlTag::create( 'div', [
						HtmlTag::create( 'div', $description ),
					], ['class' => 'accordion-inner'] ),
				], [
					'class'		=> 'accordion-body collapse',
					'id'		=> 'collapse-'.$id,
				] ),
			], ['class' => 'accordion-group'] );
		}
		return HtmlTag::create( 'div', $list, [
			'class'	=> 'accordion',
			'id'	=> 'accordion-modules',
		] );
	}
}
