<?php /** @noinspection PhpMultipleClassDeclarationsInspection */
declare(strict_types=1);

namespace CeusMedia\HydrogenSourceIndexer;

use CeusMedia\Common\UI\HTML\Elements;
use CeusMedia\Common\UI\HTML\Tag;
use CeusMedia\HydrogenFramework\Environment\Resource\Module\Definition as ModuleDefinition;

class ModuleLogRenderer
{
	protected ModuleDefinition $module;

	public function render(): string
	{
		$list	= [];
		/** @var object{version: string, note: string} $version */
		foreach( $this->module->version->log as $version ){
			$list[]	= Tag::create( 'tr', [
				Tag::create( 'th', $version->version ),
				Tag::create( 'td', $version->note ),
			] );
		}
		$colgroup	= Elements::ColumnGroup( '70px', '' );
		$thead		= Tag::create( 'thead', $list );
		$heading	= Tag::create( 'h3', 'Version Log' );
		$table		= Tag::create( 'table', $colgroup.$thead, ['class' => 'table table-striped table-bordered table-condensed'] );
		return Tag::create( 'div', $heading.$table );
	}

	public function setModule( ModuleDefinition $module ): static
	{
		$this->module	= $module;
		return $this;
	}
}