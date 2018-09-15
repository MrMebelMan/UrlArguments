<?php

/*
 Defines a parser function that allows to access URL arguments.
 Based on the DynamicFunctions extension made by RossMcClure a.k.a. Algorothm.

 {{#arg:name}} Returns the value of the given URL argument.  Can also be called
               with a default value, which is returned if the given argument is
               undefined or blank: {{#arg:name|default}}

 Author: Algorithm, MrMebelMan [http://meta.wikimedia.org/wiki/User:Algorithm, https://github.com/MrMebelMan]
 Version 1.1 (11/25/06)

*/

if ( !defined( 'MEDIAWIKI' ) ) {
   die( 'This file is a MediaWiki extension, it is not a valid entry point' );
}

$wgExtensionFunctions[] = 'wfUrlArguments';

$wgExtensionCredits['parserhook'][] = array(
	'name' => 'UrlArguments',
	'version' => '1.0',
	'url' => 'https://github.com/MrMebelMan/mediawiki-extensions-UrlArguments',
	'author' => 'Ross McClure, MrMebelMan',   
	'description' => 'Defines an additional parser function that allows to access URL arguments.'
);

$wgHooks['LanguageGetMagic'][] = 'wfUrlArgumentsLanguageGetMagic';

function wfUrlArguments() {
	global $wgParser, $wgExtUrlArguments;

	$wgExtUrlArguments = new ExtUrlArguments();

	$wgParser->setFunctionHook( 'arg', array( &$wgExtUrlArguments, 'arg' ) );
}

function wfUrlArgumentsLanguageGetMagic( &$magicWords, $langCode ) {
	switch ( $langCode ) {
		default:
			$magicWords['arg'] = array( 0, 'arg' );
	}
	return true;
}

class ExtUrlArguments {
	function arg( &$parser, $name = '', $default = NULL ) {
		global $wgRequest;

		$parser->disableCache();  // TODO: is this necessary?

		$arg_value = $wgRequest->getVal($name, $default);

		if ( $arg_value !== NULL && ( $arg_value < 1 || $arg_value > 2 ) ) {
            return NULL;
		}

		return $arg_value;
	}
}

?>
