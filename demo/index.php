<?php

use CeusMedia\HydrogenFramework\Environment\Resource\Module\Reader as HydrogenModuleReader;
use CeusMedia\HydrogenSourceIndexer\HtmlRenderer;
use CeusMedia\HydrogenSourceIndexer\IniReader as SourceIniReader;
use CeusMedia\HydrogenSourceIndexer\ModuleIndex;

require_once '../vendor/autoload.php';

error_reporting( E_ALL );
ini_set( 'display_errors', "On" );

$module	= HydrogenModuleReader::load( 'Info_Newsletter.xml', 'Info:Newsletter' );
//print_m( $module );

$pathSource	= 'demo-module-source/';

$renderer	= new HtmlRenderer();
$renderer->setSourcePath( $pathSource );
$renderer->setSettings( new SourceIniReader( $pathSource ) );
$renderer->setModules( [$module->id => $module] );
$renderer->setMode( ModuleIndex::MODE_FULL );

$html	= $renderer->render();

print( $html );
