  private static function openUpload($path) {
    $params = ['action' => 'put'];
    $response = Api::sendRequest('/files/' . rawurlencode($path), 'POST', $params);

    $partData = (array)$response->data;
    $partData['headers'] = $response->headers;
    $partData['parameters'] = $params;

    return new FilePartUpload($partData);
  }

  private static function continueUpload($path, $partNumber, $firstFilePartUpload) {
    $params = [
      'action' => 'put',
      'part' => $partNumber,
      'ref' => $firstFilePartUpload->ref,
    ];

    $response = Api::sendRequest('/files/' . rawurlencode($path), 'POST', $params);

    $partData = (array)$response->data;
    $partData['headers'] = $response->headers;
    $partData['parameters'] = $params;

    return new FilePartUpload($partData);
  }

  private static function completeUpload($filePartUpload) {
    $params = [
      'action' => 'end',
      'ref' => $filePartUpload->ref,
    ];

    $response = Api::sendRequest('/files/' . rawurlencode($filePartUpload->path), 'POST', $params);
  }

  public static function uploadFile($destinationPath, $sourceFilePath) {
    $filePartUpload = self::openUpload($destinationPath);

    Logger::debug('File::uploadFile() filePartUpload = ' . print_r($filePartUpload, true));

    $sourceFileHandle = fopen($sourceFilePath, 'rb');

    $filesize = filesize($sourceFilePath);
    $totalParts = ceil($filesize / $filePartUpload->partsize);

    if ($totalParts === 1) {
      Api::sendFile($filePartUpload->upload_uri, 'PUT', $sourceFileHandle);
    } else {
      // send part 1
      $partFilePath = tempnam(sys_get_temp_dir(), basename($filePartUpload->path));
      $partFileHandle = fopen($partFilePath, 'w+b');
      stream_copy_to_stream($sourceFileHandle, $partFileHandle, $filePartUpload->partsize);
      rewind($partFileHandle);

      Api::sendFile($filePartUpload->upload_uri, 'PUT', $partFileHandle);

      unlink($partFilePath);

      $failed = false;

      // send parts 2..n
      for ($part = 2; $part <= $totalParts; ++$part) {
        $response = null;
        $retries = 0;

        $sourceOffset = ftell($sourceFileHandle);

        do {
          $nextFilePartUpload = self::continueUpload($destinationPath, $part, $filePartUpload);

          $partFilePath = tempnam(sys_get_temp_dir(), basename($filePartUpload->path) . '~part' . $part);
          $partFileHandle = fopen($partFilePath, 'w+b');

          if ($retries > 0) {
            fseek($sourceFileHandle, $sourceOffset);
          }

          stream_copy_to_stream($sourceFileHandle, $partFileHandle, $nextFilePartUpload->partsize);

          rewind($partFileHandle);

          $response = Api::sendFile($nextFilePartUpload->upload_uri, 'PUT', $partFileHandle);

          unlink($partFilePath);
        } while (!$response && ++$retries <= Files::$maxNetworkRetries);

        if ($retries > Files::$maxNetworkRetries) {
          $failed = true;
          break;
        }
      }
    }

    self::completeUpload($filePartUpload);

    return !$failed;
  }

  public static function uploadData($destinationPath, $data) {
    $tempPath = tempnam(sys_get_temp_dir(), basename($destinationPath));
    file_put_contents($tempPath, $data);

    $result = self::uploadFile($destinationPath, $tempPath);

    unlink($tempPath);

    return $result;
  }

  public static function find($path) {
    $response = Api::sendRequest('/files/' . rawurlencode($path), 'GET');
    return new File((array)$response->data);
  }

  public function get($path) {
    return self::find($path);
  }

  public function copyTo($destinationFilePath) {
    $params = ['destination' => $destinationFilePath];
    return Api::sendRequest('/file_actions/copy/' . rawurlencode($this->path), 'POST', $params);
  }

  public function moveTo($destinationFilePath) {
    $params = ['destination' => $destinationFilePath];
    return Api::sendRequest('/file_actions/move/' . rawurlencode($this->path), 'POST', $params);
  }
