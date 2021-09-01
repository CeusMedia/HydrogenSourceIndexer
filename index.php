#!/usr/bin/php
<?php
use CeusMedia\HydrogenSourceIndexer\App;

use UI_DevOutput as DevOutput;
use Net_CURL as cURL;

( include_once __DIR__.'/../../autoload.php' ) or die( 'Install packages using composer, first!' );

error_reporting( E_ALL );
ini_set( 'display_errors', TRUE );

require_once __DIR__.'/src/CustomFileTrait.php';
require_once __DIR__.'/src/ComposerSupport.php';
require_once __DIR__.'/src/HtmlRenderer.php';
require_once __DIR__.'/src/JsonRenderer.php';
require_once __DIR__.'/src/ModuleIndex.php';
require_once __DIR__.'/src/IniReader.php';
require_once __DIR__.'/src/CliApp.php';
require_once __DIR__.'/src/WebApp.php';
require_once __DIR__.'/src/App.php';
require_once __DIR__.'/src/ModuleDescriptionRenderer.php';

$pathSource	= dirname( dirname( dirname( __DIR__ ) ) ).'/';

new DevOutput;
new App( $pathSource );
