<?php

namespace MH17CDN;

class RES {

  public $fileExtension;
  public $mime;
  public $mimeList;

  /**
   * RES Constructor
   */
  function __construct()
  {
    $this->fileExtension = null;
    $this->mime = null;
    $this->mimeList = [
      'js' => 'text/javascript',
      'css' => 'text/css',
      'png' => 'image/png',
      'gif' => 'image/gif',
      'jpg' => 'image/jpeg',
      'jpeg' => 'image/jpeg',
      'map' => 'text/plain'
    ];
  }

  /**
   * send the response
   *
   * @param int $code
   * @param string|null $etag
   * @param string|null $filePath
   * @return void
   */
  public function send(int $code, string $etag = null, string $filePath = null): void {

    if ($this->fileExtension) {
      if (array_key_exists($this->fileExtension, $this->mimeList)) {
        $this->mime = $this->mimeList[$this->fileExtension];
      } else {
        $this->mime = null;
      }
    }

  	if ($etag) {
  		header("Etag: {$etag}");
  	}

    // only send mime if actual data will be sent
    if ($code === 200 || $code === 304) {
      if ($this->mime)
        header("Content-Type: {$this->mime}");
    }

    http_response_code($code);
    header_remove('X-Powered-By');

  	if ($filePath) {
  		$size = filesize($filePath);
  		header("Content-Length: {$size}");
  		readfile($filePath);
  	} else {
  		header('Content-Length: 0');
  	}

  	exit;
  }
}
