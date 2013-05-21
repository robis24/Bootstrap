<?php
/**
 * An extension providing the Bootstrap library to other extensions
 *
 * @see https://www.mediawiki.org/wiki/Extension:Bootstrap
 * @see http://twitter.github.io/bootstrap
 *
 * @author Stephan Gambke
 * @version 0.1 alpha
 *
 * @defgroup Bootstrap Bootstrap
 */

/**
 * The main file of the Bootstrap extension
 *
 * @copyright (C) 2013, Stephan Gambke
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License, version 3 (or later)
 *
 * This file is part of the MediaWiki extension Bootstrap.
 * The Bootstrap extension is free software: you can redistribute it and/or
 * modify it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * The Bootstrap extension is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @file
 * @ingroup Bootstrap
 */
if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'This file is part of a MediaWiki extension, it is not a valid entry point.' );
}

if ( !defined( 'LESS_VERSION' ) ) {
	die( '<b>Error:</b> The <a href="https://www.mediawiki.org/wiki/Extension:Bootstrap">Bootstrap</a> extension depends on the Less extension. You need to install the <a href="https://www.mediawiki.org/wiki/Extension:Less">Less</a> extension first.' );
}

/**
 * The extension version
 */
define( 'BS_VERSION', '0.1 alpha' );

// register the extension
$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'Bootstrap',
	'author' => '[http://www.mediawiki.org/wiki/User:F.trott Stephan Gambke]',
	'url' => 'https://www.mediawiki.org/wiki/Extension:Bootstrap',
	'descriptionmsg' => 'bootstrap-desc',
	'version' => BS_VERSION,
);


// server-local path to this file
$dir = dirname( __FILE__ );

// register message files
$wgExtensionMessagesFiles['Bootstrap'] = $dir . '/Bootstrap.i18n.php';

// register hook handlers
$wgHooks['ParserBeforeStrip'][] = 'loadBootstrap';

// register resource modules with the Resource Loader

$moduleTemplate = array(
	'localBasePath' => $dir,
	'remoteExtPath' => 'Bootstrap',
	'dependencies' => array( 'jquery' ),
);

// module loading all styles
$wgResourceModules['ext.bootstrap.styles'] = array(
	'localBasePath' => $dir,
	'remoteExtPath' => 'Bootstrap',
	'styles' => array( 'fixed.less' ),
	'class' => 'ResourceLoaderLessFileModule',
	'dependencies' => array( ),
);

// module loading all scripts
$wgResourceModules['ext.bootstrap.scripts'] = array(
	'localBasePath' => $dir,
	'remoteExtPath' => 'Bootstrap',
	'dependencies' => array(  ),
);

$moduleNames = array(
	'affix',
	'alert',
	'button',
	'carousel',
	'collapse',
	'dropdown',
	'modal',
	'popover',
	'scrollspy',
	'tab',
	'tooltip',
	'transition',
	'typeahead'
);

foreach ( $moduleNames as $modName ) {

	$wgResourceModules["ext.bootstrap.scripts.$modName"] = array_merge( $moduleTemplate, array(
		'scripts' => array( "bootstrap/js/bootstrap-$modName.js" ),
			) );

	$wgResourceModules['ext.bootstrap.scripts']['dependencies'][] = "ext.bootstrap.scripts.$modName";
}

// Fix dependencies between modules explicitely
$wgResourceModules['ext.bootstrap.scripts.popover']['dependencies'][] = "ext.bootstrap.scripts.tooltip";

// all-including module
$wgResourceModules['ext.bootstrap'] = array(
	'localBasePath' => $dir,
	'remoteExtPath' => 'Bootstrap',
	'dependencies' => array( 'ext.bootstrap.styles', 'ext.bootstrap.scripts' ),
);

unset( $dir );

function loadBootstrap( Parser &$parser ) {

	// load scripts and styles
	$out = RequestContext::getMain()->getOutput();
	if ( $out->isArticle() ) {

		$parserOutput = $parser->getOutput();
		$parserOutput->addModules( 'ext.bootstrap.scripts' );
		$parserOutput->addModuleStyles( 'ext.bootstrap.styles' );

	} else {

		$out->addModules( 'ext.bootstrap.scripts' );
		$out->addModuleStyles( 'ext.bootstrap.styles' );

	}

	return true;
}
