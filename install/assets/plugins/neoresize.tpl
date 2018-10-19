//<?php
/**
 * NeoResize Plugin v2.0.0
 * 
 * The following System Events should be checked:
 *  
 * [X] OnWebPagePrerender
 *
 * @category    plugin
 * @version     2.0.0
 * @author      Milos Djordjevic (milos@themelaboratory.com)
 */


if($modx->documentObject['contentType'] != 'application/rss+xml') {
	define(DIRECTRESIZE_PATH, "assets/plugins/neoresize/");
	@require_once $modx->config["base_path"].DIRECTRESIZE_PATH."neoResize.php";
	$direct = new directResize($config);

	global $content;

	$e = &$modx->Event;
	switch ($e->name) {
		case "OnWebPagePrerender":
		$modx->documentOutput = $direct->Process($modx->documentOutput);    
		break;
	default :
		return;
		break;
	}	
}