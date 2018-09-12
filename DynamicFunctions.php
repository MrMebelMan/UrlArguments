<?php
/*

 Defines a subset of parser functions that must clear the cache to be useful.

 {{#arg:name}} Returns the value of the given URL argument.  Can also be called
               with a default value, which is returned if the given argument is
               undefined or blank: {{#arg:name|default}}

 {{#ip:}}      Returns the current user IP.

 {{#rand:a|b}} Returns a random value between a and b, inclusive.  Can
               also be called with a single value; {{#rand:6}} returns a
               random value between 1 and 6 (equivalent to a dice roll).

 {{#skin:}}    Returns the name of the current skin.

 Author: Algorithm [http://meta.wikimedia.org/wiki/User:Algorithm]
 Version 1.1 (11/25/06)

*/

# Not a valid entry point, skip unless MEDIAWIKI is defined
if ( !defined( 'MEDIAWIKI' ) ) {
   die( 'This file is a MediaWiki extension, it is not a valid entry point' );
}

$wgExtensionFunctions[] = 'wfDynamicFunctions';
$wgExtensionCredits['parserhook'][] = array(
	'name' => 'DynamicFunctions',
	'version' => '1.1',
	'url' => 'https://www.mediawiki.org/wiki/Extension:DynamicFunctions',
	'author' => 'Ross McClure',   
	'description' => 'Defines an additional set of parser functions.'
);

$wgHooks['LanguageGetMagic'][] = 'wfDynamicFunctionsLanguageGetMagic';

function wfDynamicFunctions() {
	global $wgParser, $wgExtDynamicFunctions;

	$wgExtDynamicFunctions = new ExtDynamicFunctions();

	$wgParser->setFunctionHook( 'arg', array( &$wgExtDynamicFunctions, 'arg' ) );
	$wgParser->setFunctionHook( 'ip', array( &$wgExtDynamicFunctions, 'ip' ) );
	$wgParser->setFunctionHook( 'rand', array( &$wgExtDynamicFunctions, 'rand' ), SFH_OBJECT_ARGS );
	$wgParser->setFunctionHook( 'skin', array( &$wgExtDynamicFunctions, 'skin' ) );
}

function wfDynamicFunctionsLanguageGetMagic( &$magicWords, $langCode ) {
	switch ( $langCode ) {
	default:
		$magicWords['arg']    = array( 0, 'arg' );
		$magicWords['ip']     = array( 0, 'ip' );
		$magicWords['rand']   = array( 0, 'rand' );
		$magicWords['skin']   = array( 0, 'skin' );
	}
	return true;
}

class ExtDynamicFunctions {

	function arg( &$parser, $name = '', $default = '' ) {
		global $wgRequest;
		$parser->disableCache();
		return $wgRequest->getVal($name, $default);
	}

	function ip( &$parser ) {
		$parser->disableCache();
		return wfGetIP();
	}

	function rand( &$parser, $frame, $args ) {
		$a = isset( $args[0] ) ? $frame->expand( $args[0] ) : 0;
		$b = isset( $args[1] ) ? $frame->expand( $args[1] ) : 1;
		$parser->disableCache();
		if ( is_callable( array( $frame, 'setVolatile' ) ) ) {
			$frame->setVolatile(); // see bug #58929
		}
		return mt_rand( intval($a), intval($b) );
	}

	function skin( &$parser ) {
		global $wgUser, $wgRequest;
		$parser->disableCache();
		return $wgRequest->getVal('useskin', $wgUser->getOption('skin'));
	}
}