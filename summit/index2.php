<?php
require_once 'bootstrap.php';

/*
 * Config 
 */
Fl_Config::set('cache', 'dir', BASEPATH.'tmp');
// -------------------------------------------------------------------------

$url = (isset($_GET['url'])) ? $_GET['url'] : FALSE;

if($url === FALSE)
{
	exit('please set the url parameter');
}
// can acccept without http
else if(!preg_match('/^http/', $url))
{
	$url = 'http://'.$url;
}

$url_parts = (parse_url($url));

if(!preg_match('/linuxplumbersconf.org$/', $url_parts['host']))
{
	exit($url . ' is not allowed');
}
else
{
	// get content from cache or remote site
	$content = Fl_Remote::curl_get($url, TRUE);
	
	// find anything between specified tags
	// http://kevin.deldycke.com/2007/03/ultimate-regular-expression-for-html-tag-parsing-with-php/
	$regex = "/<body((\s+(\w|\w[\w-]*\w)(\s*=\s*(?:\".*?\"|'.*?'|[^'\">\s]+))?)+\s*|\s*)\/?>(.*)<\/body>/is";
	$matches = array();
	preg_match($regex, $content, $matches);
	//print_r($matches);
	$content = $matches[5];
	
	// remove ie6 hacks bits
	$content = preg_replace('#<!-- IE6 hacks -->(.*)<!\[endif\]-->#s', '', $content); 	
	echo '<!DOCTYPE html><html><head><meta charset="UTF-8" /> <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<link rel="stylesheet" type="text/css" media="all" href="http://lpc.chrisjohnston.org/remote/css/style.css" /><script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script><script src="http://lpc.chrisjohnston.org/remote/resize.js"></script>


 </head><body id="targetdiv">' . $content . '</body></html>';
}
