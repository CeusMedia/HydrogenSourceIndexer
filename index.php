#!/usr/bin/php
<?php
use CeusMedia\HydrogenSourceIndexer\App;

use CeusMedia\Common\UI\DevOutput as DevOutput;

( include_once __DIR__.'/../../autoload.php' ) or die( 'Install packages using composer, first!' );

error_reporting( E_ALL );
ini_set( 'display_errors', '1' );

require_once __DIR__.'/src/CustomFileTrait.php';
require_once __DIR__.'/src/ComposerSupport.php';
require_once __DIR__.'/src/HtmlRenderer.php';
require_once __DIR__.'/src/JsonRenderer.php';
require_once __DIR__.'/src/SerialRenderer.php';
require_once __DIR__.'/src/ModuleIndex.php';
require_once __DIR__.'/src/IniReader.php';
require_once __DIR__.'/src/ModuleDescriptionRenderer.php';
require_once __DIR__.'/src/App.php';

$pathSource	= dirname( __DIR__, 3 ) .'/';

new App( $pathSource );
