<?php
/**
 *	@author		Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright	2021 Ceus Media
 */
namespace CeusMedia\HydrogenSourceIndexer;

use UI_HTML_Tag as HtmlTag;
use UI_HTML_Elements as HtmlElements;

/**
 *	@author		Christian Würker <christian.wuerker@ceusmedia.de>
 *	@copyright	2021 Ceus Media
 */
class ModuleDescriptionRenderer
{
	/**	@var	string		$linkClass		... */
	public static $linkClass		= 'icon-label';

	/**	@var	string		$linkTarget		... */
	public static $linkTarget		= '_self';

	/**	@var	array		$callbacks		... */
	protected static $callbacks		= array();

	/**	@var	string		$content	... */
	protected $content				= '';

	/**
	 *	@access		public
	 *	@return		string
	 */
	public function render(): string
	{
		$content	= $this->content;
		$content	= self::formatLists( $content );
		$content	= self::formatText( $content );
		$content	= self::formatBreaks( $content );
		$content	= self::formatCodeBlocks( $content );
		$content	= self::formatLinks( $content );
		$content	= self::formatCurrencies( $content );
		$content	= self::formatWikiLinks( $content );
		$content	= self::formatYoutubeLinks( $content );
		return $content;
	}

	/**
	 *	@access		public
	 *	@param		string		$content		...
	 *	@return		self
	 */
	public function setContent( string $content ): self
	{
		$this->content	= $content;
		return $this;
	}

	protected static function multiPregReplace( string $content, array $replacements ): string
	{
		foreach( $replacements as $expression => $replacement ){
			$result	= preg_replace( $expression, $replacement, $content );
			if( is_string( $result ) )
				$content	= $result;
		}
		return $content;
	}

	/**
	 *	@access		protected
	 *	@param		string		$content		...
	 *	@return		string
	 */
	protected static function formatBreaks( string $content ): string
	{
		return self::multiPregReplace( $content, [
			"/(----){4,}\r?\n/"			=> ' <hr/>',							//  four dashes make a horizontal row
			"/([^>])\r?\n\r?\n\r?\n/"	=> '\\1<br class="clearfloat">',
			"/([^>])\r?\n\r?\n/"		=> '\\1<br/><br/>',
			"/(.+)\t\r?\n/"				=> '\\1<br/>',							//  special break: line ends with tab
			"/([^*\/])\/\r?\n/"			=> '\\1<br/>',							//  special break: line ends with / but not with */ or //
		] );
	}

	/**
	 *	@access		protected
	 *	@param		string		$content		...
	 *	@return		string
	 */
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

	/**
	 *	@access		protected
	 *	@param		string		$content		...
	 *	@return		string
	 */
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

	/**
	 *	@access		protected
	 *	@param		string		$content		...
	 *	@return		string
	 */
	protected static function formatCurrencies( string $content ): string
	{
		return self::multiPregReplace( $content, [
			'/([0-9]+) Euro/'	=> '\\1&nbsp;&euro;',
			'/([0-9]+) Cent/'	=> '\\1&nbsp;&cent;',
			'/([0-9]+) Pound/'	=> '\\1&nbsp;&pound;',
			'/([0-9]+) Yen/'	=> '\\1&nbsp;&yen;',
			'/([0-9]+) Dollar/'	=> '\\1&nbsp;&#36;',
			'/([0-9]+) Baht/'	=> '\\1&nbsp;&#3647;',
			'/€/'				=> '&euro;',
		] );
	}

	/**
	 *	@access		protected
	 *	@param		string		$content		...
	 *	@return		string
	 */
	protected static function formatWikiLinks( string $content ): string
	{
		$matches	= array();
		preg_match_all( '/\[wiki:(.+)(\|(.*))?\]/U', $content, $matches );
		for( $i=0; $i<count($matches[0]); $i++ ){
			$query		= trim( $matches[1][$i] );
			$title		= isset( $matches[3][$i] ) ? trim( $matches[3][$i] ) : $query;
			$url		= 'http://de.wikipedia.org/wiki/'.$query;
			$class		= ( self::$linkClass ? self::$linkClass.' ' : '' ).'link-wiki';
			$link		= HtmlElements::Link( $url, $title, $class, self::$linkTarget );
			$content	= str_replace( $matches[0][$i], $link, $content );
		}
		return $content;
	}

	/**
	 *	@access		protected
	 *	@param		string		$content		...
	 *	@return		string
	 */
	protected static function formatYoutubeLinks( string $content ): string
	{
		$matches	= array();
		preg_match_all( '/\[youtube:(.+)(\|(.*))?\]/U', $content, $matches );
		for( $i=0; $i<count( $matches[0] ); $i++ ){
			$query		= trim( $matches[1][$i] );
			$title		= isset( $matches[3][$i] ) ? trim( $matches[3][$i] ) : $query;
			$url		= 'http://www.youtube.com/watch?v='.$query;
			$class		= ( self::$linkClass ? self::$linkClass.' ' : '' ).'link-youtube';
			$link		= HtmlElements::Link( $url, $title, $class, self::$linkTarget );
			$content	= str_replace( $matches[0][$i], $link, $content );
		}
		return $content;
	}

	/**
	 *	@access		protected
	 *	@param		string		$content		...
	 *	@return		string
	 */
	protected static function formatText( string $content ): string
	{
		return self::multiPregReplace( $content, [
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
		] );
	}

	/**
	 *	@access		protected
	 *	@param		string		$content		...
	 *	@return		string
	 */
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
