<?php /** @noinspection PhpMultipleClassDeclarationsInspection */
declare(strict_types=1);

namespace CeusMedia\HydrogenSourceIndexer;

use CeusMedia\Common\UI\HTML\Elements;
use CeusMedia\Common\UI\HTML\Tag;
use CeusMedia\HydrogenFramework\Environment\Resource\Module\Definition as ModuleDefinition;

class ModuleConfigRenderer
{
	protected ModuleDefinition $module;

	public function render(): string
	{
		$rows		= [];
		foreach( $this->module->config as $config ){
			$label	= $config->key;
			if( '' !== trim( $config->title ?? '' ) )
				$label	= Tag::create( 'acronym', $config->key, ['title' => $config->title] );
			$additional	= '';
			if( $config->mandatory )
				$additional	.= 'mandatory ';
			if( is_bool( $config->protected ) || ( isset( $config->protected ) && 'yes' === $config->protected ) )
				$additional	.= 'protected ';
			else if( is_string( $config->protected ) )
				$additional	.= 'protected:'.$config->protected.' ';
			$value	 = $config->value;
			if( is_bool( $value ) )
				$value	= $value ? 'true' : 'false';
			$rows[]	= Tag::create( 'tr', [
				Tag::create( 'td', $label ),
				Tag::create( 'td', $config->type ),
				Tag::create( 'td', $value ),
				Tag::create( 'td', $additional, ['class' => 'muted', 'style' => 'font-size: 85%'] ),
			] );
		}
		$heading	= Tag::create( 'h3', 'Module Configuration' );
		$colgroup	= Elements::ColumnGroup( ['25%', '70px', '', '20%'] );
		$tbody		= Tag::create( 'tbody', $rows );
		$list		= Tag::create( 'table', $colgroup.$tbody, ['class' => 'table table-striped table-bordered table-condensed'] );
		return Tag::create( 'div', $heading.$list, ['class' => ''] );
	}

	public function setModule( ModuleDefinition $module ): static
	{
		$this->module = $module;
		return $this;
	}
}