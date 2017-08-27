<?php

namespace MH17CDN;

define('DS', DIRECTORY_SEPARATOR);
define('WWW_ROOT', __DIR__ . DS);

require WWW_ROOT . 'library' . DS .'ERR.php';
require WWW_ROOT . 'library' . DS .'RES.php';

$RES = new RES();
$ERR = new ERR($RES);

$url = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$path = ltrim(rtrim(parse_url($url, PHP_URL_PATH), '/'), '/');
$parts = explode('/', $path);

if (count($parts) < 2) $RES->send(404);

$directory = $parts[0];
$fileName = $parts[1];

if (!in_array($directory, ['js', 'css', 'img'])) $RES->send(404);

$filePath = WWW_ROOT . 'content' . DS . $directory . DS . $fileName;

if (!file_exists($filePath)) $RES->send(404);

$requestEtag = isset($_SERVER['HTTP_IF_NONE_MATCH'])
	? trim($_SERVER['HTTP_IF_NONE_MATCH'])
	: null;

$responseEtag = md5_file($filePath);

$RES->fileExtension = array_key_exists('extension', pathinfo($fileName))
	? pathinfo($fileName)['extension']
	: null;

if ($requestEtag && $requestEtag === $responseEtag) {
	$RES->send(304, $responseEtag);
} else {
	$RES->send(200, $responseEtag, $filePath);
}
