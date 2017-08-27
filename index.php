<?php

$p = ltrim(rtrim($_SERVER['REQUEST_URI'], '/'), '/');
$ps = explode('/', $p);

if (count($ps) < 2) send(404, null, null, null);

$dir = $ps[0]; $fn = $ps[1];

$fp = __DIR__."/{$dir}/{$fn}";
if (!file_exists($fp)) send(404, null, null, null);

$reqe = isset($_SERVER['HTTP_IF_NONE_MATCH']) ? trim($_SERVER['HTTP_IF_NONE_MATCH']) : null;
$rese = '"'.md5_file($fp).'"';

$fe = isset(pathinfo($fn)['extension']) ? pathinfo($fn)['extension'] : null;

if ($reqe && $reqe === $rese) send(304, $rese, null, null);
send(200, $rese, $fp, $fe);

function send($c, $et, $fp, $fe) {

  $mime = null; $duration = 120;
  if ($fe) {
    $fs = [
      'js' => ['application/javascript', 120],
      'map' => ['application/octet-stream', 120],
      'css' => ['text/css', 120],
      'jpg' => ['image/jpeg', 3600],
      'jpeg' => ['image/jpeg', 3600],
      'png' => ['image/png', 3600],
      'svg' => ['image/svg+xml', 3600],
      'gif' => ['image/gif', 3600],
      'tif' => ['image/tiff', 3600],
      'tiff' => ['image/tiff', 3600],
      'woff' => ['application/octet-stream', 2592000],
      'woff2' => ['application/octet-stream', 2592000],
      'fft' => ['application/octet-stream', 2592000],
      'eot' => ['application/octet-stream', 2592000]
    ];
    if (array_key_exists($fe, $fs)) {
      $mime = $fs[$fe][0];
      $duration = $fs[$fe][1];
    }
  }
  if ($et) header("Etag: {$et}");
  if ($c === 200 && $mime) header("Content-Type: {$mime}");
  http_response_code($c);
  header_remove('X-Powered-By');
  if ($fp) {
    $s = filesize($fp);
    header("Content-Length: {$s}");
    header("Cache-Control: public, max-age={$duration}");
    readfile($fp);
  } else {
    header('Content-Length: 0');
  }
  exit;
}
