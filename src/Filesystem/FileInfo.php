<?php

namespace Wpce\Filesystem;

class FileInfo {

  /**
   * Returns a human-readable PHP uploaded file error message
   *
   * @param integer $errorCode
   * @return string|null error message for valid error codes
   */
  static function getPhpUploadedFileErrorMessageByCode(int $errorCode): ?string {
    switch ($errorCode) {
      case 1:
        return 'File is too big (server limit is '.ini_get('upload_max_filesize').').';
      case 2:
        return 'File is too big.';
      case 3:
        return 'File was only partially uploaded';
      case 4:
        return 'No file was uploaded';
      case 6:
        return 'Missing a temporary folder';
      case 0:
      default:
        return null;
    }
  }

  /**
   * Gets PHP uploaded file errors - assumes the files is in $_FILES array element format.
   *
   * @param array $uploadedFile PHP uploaded file array (as in $_FILES)
   * @param array $config configurable params used for validation
   *
   * @return null|string error message if any error found
   */
  static public function getPhpUploadedFileError(array $uploadedFile, array $config = []): ?string {
    $maxFileSizeInBytes = $config['maxFileSizeInBytes'] ?? 8000000;
    $maxFileSizeInMegabytes = $config['maxFileSizeInMegabytes'] ?? round($maxFileSizeInBytes/1000000);
    $allowedFileTypes = $config['allowedFileTypes'] ?? [];
    $disallowedFileTypes = $config['disallowedFileTypes'] ?? [];
    $labels = $config['labels'] ?? [
      'fileTooBig' => 'File is too big - files cannot be larger than %s MB.',
      'invalidFormat' => 'Invalid file format.',
      'invalidSource' => 'Invalid file source.',
      'noFile' => 'File was not sent.',
    ];

    if (empty($uploadedFile)) {
      return $labels['noFile'];
    }
    if (!is_uploaded_file($uploadedFile['tmp_name'])) {
      return $labels['invalidSource'];
    }
    if ($uploadedFile['error'] > 0) {
      return self::getPhpUploadedFileErrorMessageByCode($uploadedFile['error']);
    }
    if ($uploadedFile['size'] > $maxFileSizeInBytes) {
      return sprintf($labels['fileTooBig'], $maxFileSizeInMegabytes);
    }
    $fileMimeType = mime_content_type($uploadedFile['tmp_name']);
    if (
      (count($allowedFileTypes) > 0 && !in_array($fileMimeType, $allowedFileTypes))
      || in_array($fileMimeType, $disallowedFileTypes)
    ) {
      return $labels['invalidFormat'];
    }
    return null;
  }


  /**
   * Gets human-readable file size description
   *
   * @param int $fileSize
   * @return string human-readable file size, eg. 1.7 GB
   */
  static public function getHumanReadableFileSize(int $fileSize): string {
    $sizeArray = [
      "TB" => pow(1024, 4),
      "GB" => pow(1024, 3),
      "MB" => pow(1024, 2),
      "KB" => 1024,
      "B" => 1,
    ];
    $result = '';

    foreach($sizeArray as $unitName => $byteSize) {
      if ($fileSize >= $byteSize) {
        $result = $fileSize / $byteSize;
        $result = str_replace(".", "," , strval(round($result, 2)))." ".$unitName;
        break;
      }
    }

    return $result;
  }

}
