<?php /** @noinspection PhpMultipleClassDeclarationsInspection */
declare(strict_types=1);

namespace CeusMedia\HydrogenSourceIndexer;

use CeusMedia\Common\Exception\Data\Missing as DataMissingException;
use CeusMedia\Common\UI\HTML\Tag;
use CeusMedia\HydrogenFramework\Environment\Resource\Module\Definition as ModuleDefinition;
use CeusMedia\HydrogenFramework\Environment\Resource\Module\Definition\File;

class ModuleFilesRenderer
{
	protected ?ModuleDefinition $module		= NULL;

	public function render(): string
	{
		if( NULL === $this->module )
			throw DataMissingException::create( 'No module set' );

		$rowsCategories	= [];

		/**
		 * @var string $category
		 * @var array<File> $files
		 */
		foreach( get_object_vars( $this->module->files ) as $category => $files ){
			if( [] === $files )
				continue;
			$rowsFiles	= [];
			foreach( $files as $file ){
				$rowsFiles[]	= Tag::create( 'li', $file->file );
			}
			$listFiles	= Tag::create( 'ul', $rowsFiles );
			$rowsCategories[]	= Tag::create( 'li', $category.$listFiles );
		}
		$heading	= Tag::create( 'h3', 'Module Files' );
		$listCategories = Tag::create( 'ul', $rowsCategories );
		return Tag::create( 'div', $heading.$listCategories, ['class' => ''] );
	}

	public function setModule( ModuleDefinition $module ): static
	{
		$this->module	= $module;
		return $this;
	}
}