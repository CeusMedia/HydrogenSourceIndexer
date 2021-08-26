<?php
namespace CeusMedia\HydrogenModules\Index;

use UI_HTML_Tag as HtmlTag;
use UI_HTML_Elements as HtmlElements;

class ModuleDescriptionRenderer
{
	public static $linkClass		= 'icon-label';

	public static $linkTarget		= '_self';

	protected static $callbacks		= array();

	public function render(): string
	{
		$content	= $this->content;
		$content	= self::formatLists( $content );
		$content	= self::formatText( $content );
		$content	= self::formatBreaks( $content );
		$content	= self::formatCodeBlocks( $content );
		$content	= self::formatLinks( $content );
		$content	= self::formatImageSearch( $content );
		$content	= self::formatMapSearch( $content );
		$content	= self::formatCurrencies( $content );
		$content	= self::formatWikiLinks( $content );
		$content	= self::formatYoutubeLinks( $content );
		$content	= self::formatMapLinks( $content );
		$content	= self::formatDiscogsLinks( $content );
		$content	= self::formatMyspaceLinks( $content );
		$content	= self::formatImdbLinks( $content );
		return $content;
	}

	public function setContent( string $content ): self
	{
		$this->content	= $content;
		return $this;
	}

	protected static function formatBreaks( string $content ): string
	{
		$content	= preg_replace( "/(----){4,}\r?\n/",' <hr/>', $content );						//  four dashes make a horizontal row
		$content	= preg_replace( "/([^>])\r?\n\r?\n\r?\n/", '\\1<br class="clearfloat">', $content );
		$content	= preg_replace( "/([^>])\r?\n\r?\n/", '\\1<br/><br/>', $content );
		$content	= preg_replace( "/(.+)\t\r?\n/", '\\1<br/>', $content );						//  special break: line ends with tab
		$content	= preg_replace( "/([^*\/])\/\r?\n/", '\\1<br/>', $content );					//  special break: line ends with / but not with */ or //
		return $content;
	}

	protected static function formatCodeBlocks( string $content ): string
	{
		$content	= preg_replace( '/<(\/?)code>/', "_____\\1code_____", $content );				//  preserve <code> tags
		$pattern	= "/(\r?\n)*code:?(\w+)?>(.*)<code(\r?\n)*/siU";
		$matches	= array();
		preg_match_all( $pattern, $content, $matches );
		for( $i=0; $i<count( $matches[0] ); $i++ ){
			$type		= $matches[2][$i];
			$code		= trim( $matches[3][$i] );
			$attributes	= array( 'class' => $type ? $type : 'code' );
			$new		= HtmlTag::create( 'xmp', $code, $attributes );
			$content	= str_replace( $matches[0][$i], $new, $content );
		}
		$content	= preg_replace( '/_____(\/?)code_____/', '<\\1code>', $content );				//  recreate <code> tags
		return $content;
	}

	protected static function formatLinks( string $content ): string
	{
		$matches	= array();
		preg_match_all( '/\[link:(\S+)(\|(.*))?\]/U', $content, $matches );
		for( $i=0; $i<count( $matches[0] ); $i++ ){
			$url		= $matches[1][$i];
			$title		= str_replace( ' ', '&nbsp;', trim( $matches[3][$i] ) );
			$class		= ( self::$linkClass ? self::$linkClass.' ' : '' ).'link-external';
			$link		= HtmlElements::Link( $url, $title, $class, self::$linkTarget );
			$content	= str_replace( $matches[0][$i], $link, $content );
		}
		return $content;
	}

	protected static function formatImageSearch( string $content ): string
	{
		$matches	= array();
		preg_match_all( '/\[image-search:(.+)(\|(.*))?\]/U', $content, $matches );
		for( $i=0; $i<count( $matches[0] ); $i++ ){
			$query		= trim( $matches[1][$i] );
			$title		= empty( $matches[3][$i] ) ? $query : trim( $matches[3][$i] );
			$url		= 'http://images.google.com/images?q='.$query;
			$class		= ( self::$linkClass ? self::$linkClass.' ' : '' ).'link-search-image';
			$link		= HtmlElements::Link( $url, $title, $class, self::$linkTarget );
			$content	= str_replace( $matches[0][$i], $link, $content );
		}
		return $content;
	}

	protected static function formatMapSearch( string $content ): string
	{
		$matches	= array();
		preg_match_all( '/\[map-search:(.+)(\|(.*))?\]/U', $content, $matches );
		for( $i=0; $i<count( $matches[0] ); $i++ ){
			$query		= trim( $matches[1][$i] );
			$title		= trim( $matches[3][$i] );
			$url		= 'http://maps.google.de/maps?hl=de&q='.$query;
			$class		= ( self::$linkClass ? self::$linkClass.' ' : '' ).'link-search-map';
			$link		= HtmlElements::Link( $url, $title, $class, self::$linkTarget );
			$content	= str_replace( $matches[0][$i], $link, $content );
		}
		return $content;
	}

	protected static function formatCurrencies( string $content ): string
	{
		$content	= preg_replace( '/([0-9]+) Euro/', '\\1&nbsp;&euro;', $content );
		$content	= preg_replace( '/([0-9]+) Cent/', '\\1&nbsp;&cent;', $content );
		$content	= preg_replace( '/([0-9]+) Pound/', '\\1&nbsp;&pound;', $content );
		$content	= preg_replace( '/([0-9]+) Yen/', '\\1&nbsp;&yen;', $content );
		$content	= preg_replace( '/([0-9]+) Dollar/', '\\1&nbsp;&#36;', $content );
		$content	= preg_replace( '/([0-9]+) Baht/', '\\1&nbsp;&#3647;', $content );
		$content	= str_replace( '/€', '&euro;', $content );
		return $content;
	}

	protected static function formatWikiLinks( string $content ): string
	{
		$matches	= array();
		preg_match_all( '/\[wiki:(.+)(\|(.*))?\]/U', $content, $matches );
		for( $i=0; $i<count($matches[0]); $i++ ){
			$query		= trim( $matches[1][$i] );
			$title		= empty( $matches[3][$i] ) ? $query : trim( $matches[3][$i] );
			$url		= 'http://de.wikipedia.org/wiki/'.$query;
			$class		= ( self::$linkClass ? self::$linkClass.' ' : '' ).'link-wiki';
			$link		= HtmlElements::Link( $url, $title, $class, self::$linkTarget );
			$content	= str_replace( $matches[0][$i], $link, $content );
		}
		return $content;
	}

	protected static function formatYoutubeLinks( string $content ): string
	{
		$matches	= array();
		preg_match_all( '/\[youtube:(.+)(\|(.*))?\]/U', $content, $matches );
		for( $i=0; $i<count( $matches[0] ); $i++ ){
			$query		= trim( $matches[1][$i] );
			$title		= empty( $matches[3][$i] ) ? $query : trim( $matches[3][$i] );
			$url		= 'http://www.youtube.com/watch?v='.$query;
			$class		= ( self::$linkClass ? self::$linkClass.' ' : '' ).'link-youtube';
			$link		= HtmlElements::Link( $url, $title, $class, self::$linkTarget );
			$content	= str_replace( $matches[0][$i], $link, $content );
		}
		return $content;
	}

	protected static function formatMapLinks( string $content ): string
	{
		$matches	= array();
		preg_match_all( '/\[(link-)?map:([0-9,.]+)(\|(.*))?\]/U', $content, $matches );
		for( $i=0; $i<count( $matches[0] ); $i++ ){
			$geocode	= trim( $matches[2][$i] );
			$parts		= explode( ',', $geocode );
			$longitude	= $parts[0];
			$lattitude	= $parts[1];
			$zoomlevel	= empty( $parts[2] ) ? 16 : $parts[2];
			$title		= trim( $matches[4][$i] );
			$url		= 'http://maps.google.de/maps?hl=de&ll='.$longitude.','.$lattitude.'&z='.$zoomlevel;
			$class		= ( self::$linkClass ? self::$linkClass.' ' : '' ).'link-map';
			$link		= HtmlElements::Link( $url, $title, $class, self::$linkTarget );
			$content	= str_replace( $matches[0][$i], $link, $content );
		}
		return $content;
	}

	protected static function formatDiscogsLinks( string $content ): string
	{
		$matches	= array();
		preg_match_all( '/\[discogs:(.+)(\|(.*))?\]/U', $content, $matches );
		for( $i=0; $i<count( $matches[0] ); $i++ ){
			$query		= trim( $matches[1][$i] );
			$title		= empty( $matches[3][$i] ) ? $query : trim( $matches[3][$i] );
			$url		= 'http://www.discogs.com/'.$query;
			$class		= ( self::$linkClass ? self::$linkClass.' ' : '' ).'link-discogs';
			$link		= HtmlElements::Link( $url, $title, $class, self::$linkTarget );
			$content	= str_replace( $matches[0][$i], $link, $content );
		}
		return $content;
	}

	protected static function formatMyspaceLinks( string $content ): string
	{
		$matches	= array();
		preg_match_all( '/\[myspace:(.+)(\|(.*))?\]/U', $content, $matches );
		for( $i=0; $i<count( $matches[0] ); $i++ ){
			$query		= trim( $matches[1][$i] );
			$title		= empty( $matches[3][$i] ) ? $query : trim( $matches[3][$i] );
			$url		= 'http://www.myspace.com/'.$query;
			$class		= ( self::$linkClass ? self::$linkClass.' ' : '' ).'link-myspace';
			$link		= HtmlElements::Link( $url, $title, $class, self::$linkTarget );
			$content	= str_replace( $matches[0][$i], $link, $content );
		}
		return $content;
	}

	protected static function formatImdbLinks( string $content ): string
	{
		$matches	= array();
		preg_match_all( '/\[imdb:(.+)(\|(.*))?\]/U', $content, $matches );
		for( $i=0; $i<count( $matches[0] ); $i++ ){
			$query		= trim( $matches[1][$i] );
			$title		= empty( $matches[3][$i] ) ? $query : trim( $matches[3][$i] );
			$url		= 'http://www.imdb.com/find?s=tt&q='.$query;
			$class		= ( self::$linkClass ? self::$linkClass.' ' : '' ).'link-imdb';
			$link		= HtmlElements::Link( $url, $title, $class, self::$linkTarget );
			$content	= str_replace( $matches[0][$i], $link, $content );
		}
		return $content;
	}

	protected static function formatText( string $content ): string
	{
		$converters	= array(
			"/####(.+)####\r?\n/U"	=> "<h5>\\1</h5>\n",
			"/###(.+)###\r?\n/U"	=> "<h4>\\1</h4>\n",
			"/##(.+)##\r?\n/U"		=> "<h3>\\1</h3>\n",
			"/#(.+)#\r?\n/U"		=> "<h2>\\1</h2>\n",
			"/([^:])\*\*(.+)\*\*/U"	=> "\\1<b>\\2</b>",
			"/([^:])\/\/(.+)\/\//U"	=> "\\1<em>\\2</em>",
			"/__(.+)__/U"			=> "<u>\\1</u>",
			"/>>(.+)<</U"			=> "<small>\\1</small>",
			"/<<(.+)>>/U"			=> "<big>\\1</big>",
			"/--(.+)--/U"			=> "<strike>\\1</strike>",
		);
		foreach( $converters as $key => $value )
			$content	= preg_replace( $key, $value, $content );
		return $content;
	}

	protected static function formatLists( string $content ): string
	{
		$pattern	= "/(\r?\n)*(o|u)?list:?(\w+)?>(.*)<(o|u)?list(\r?\n)*/siU";
		$matches	= array();
		preg_match_all( $pattern, $content, $matches );
		for( $i=0; $i<count( $matches[0] ); $i++ ){
			$type		= $matches[2][$i] ? $matches[2][$i] : 'u';
			$class		= $matches[3][$i];
			$lines		= explode( "\n", trim( $matches[4][$i] ) );
			foreach( $lines as $nr => $line )
				$lines[$nr]	= preg_replace( '/^- /', '<li>', trim( $lines[$nr] ) ).'</li>';
			$lines	= implode( "\n", $lines );
			$attributes	= array( 'class' => $class ? $class : 'list');
			$new		= HtmlTag::create( $type.'l', $lines, $attributes );
			$content	= str_replace( $matches[0][$i], $new, $content );
		}
		return $content;
	}
}
