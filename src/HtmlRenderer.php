<?php /** @noinspection PhpMultipleClassDeclarationsInspection */

/**
 *	@author		Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright	2021 Ceus Media
 */
namespace CeusMedia\HydrogenSourceIndexer;

use CeusMedia\Common\FS\File\Reader;
use CeusMedia\Common\FS\File\Reader as FileReader;
use CeusMedia\Common\UI\HTML\Elements as HtmlElements;
use CeusMedia\Common\UI\HTML\PageFrame as HtmlPage;
use CeusMedia\Common\UI\HTML\Tag;
use CeusMedia\Common\UI\HTML\Tag as HtmlTag;

use CeusMedia\HydrogenFramework\Environment\Resource\Module\Definition as ModuleDefinition;
use DomainException;
use RuntimeException;

/**
 *	@author		Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright	2021 Ceus Media
 */
class HtmlRenderer
{
	use CustomFileTrait;

	/** @var array<string,ModuleDefinition> $modules */
	protected array $modules		= [];

	/** @var ?IniReader $settings */
	protected ?IniReader $settings	= NULL;

	protected ModuleDescriptionRenderer $descriptionRenderer;
	protected ModuleFilesRenderer $filesRenderer;
	protected ModuleConfigRenderer $configRenderer;
	protected ModuleLogRenderer $logRenderer;

	protected int $mode		= ModuleIndex::MODE_REDUCED;

	public function __construct()
	{
		$this->descriptionRenderer	= new ModuleDescriptionRenderer();
		$this->filesRenderer		= new ModuleFilesRenderer();
		$this->configRenderer		= new ModuleConfigRenderer();
		$this->logRenderer			= new ModuleLogRenderer();
	}

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

		if( [] === $this->modules )
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
			'styles'		=> Tag::create( 'style', Reader::load( __DIR__.'/style.css' ) ),
		];
		/**
		 * @var string $placeholder
		 * @var string $content
		 */
		foreach( $data as $placeholder => $content )
			$template	= str_replace( '{{ '.$placeholder.' }}', $content ?? '', $template );
		return $template;
	}

	public function setMode( int $mode ): static
	{
		$this->mode	= $mode;
		return $this;
	}

	/**
	 *	@access		public
	 *	@param		array		$modules		...
	 *	@return		static
	 */
	public function setModules( array $modules ): static
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
		$list	= [];
		foreach( $this->modules as $moduleId => $module ){
			$list[]	= $this->renderModule( $module, $moduleId );
		}
		return HtmlTag::create( 'div', $list, [
			'class'	=> 'accordion',
			'id'	=> 'accordion-modules',
		] );
	}

	protected function renderModule( ModuleDefinition $module, string $moduleId ): string
	{
		$description	= $this->descriptionRenderer->setContent( $module->description )->render();
		$files			= '';
		$config			= '';
		$log			= '';
		if( ModuleIndex::MODE_FULL === $this->mode ){
			$files		= $this->filesRenderer->setModule( $module )->render();
			$config		= $this->configRenderer->setModule( $module )->render();
			$log		= $this->logRenderer->setModule( $module )->render();
		}

		$id	= preg_replace( '@[^a-z0-9]@i', '-', $moduleId );
		return HtmlTag::create( 'div', [
			HtmlTag::create( 'div', [
				HtmlTag::create( 'a', [
					HtmlTag::create( 'span', $module->title, ['class' => 'module-title'] ),
					'&nbsp;',
					HtmlTag::create( 'small', 'v'.$module->version->current, ['class' => 'module-version muted'] ),
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
					HtmlTag::create( 'div', $description.$files.$config.$log ),
				], ['class' => 'accordion-inner'] ),
			], [
				'class'		=> 'accordion-body collapse',
				'id'		=> 'collapse-'.$id,
			] ),
		], ['class' => 'accordion-group'] );
	}
}
