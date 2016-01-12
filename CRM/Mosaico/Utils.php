<?php

class CRM_Mosaico_Utils {

  static function getResource() {
    $args = explode('/', $_GET['q']);
    if ($args[0] == 'civicrm' && $args[1] == 'mosaico' && ($args[2] == 'templates' || $args[2] == 'dist')) {
      array_shift($args);
      array_shift($args);

      $config = CRM_Core_Config::singleton();
      $file   = $config->extensionsURL . 'uk.co.vedaconsulting.mosaico/packages/mosaico/' . implode('/', $args);
      CRM_Core_Error::debug_var('$file', $file);
      $file   = str_replace(" ", "+", $file);
      CRM_Core_Error::debug_var('$file', $file);
      $contentType = self::getUrlMimeType($file);

      header("Content-Type: {$contentType}");
      $content = file_get_contents($file);
      echo $content;
    }
    CRM_Utils_System::civiExit();
  }

  static function getUrlMimeType($url) {
    $buffer = file_get_contents($url);
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    return $finfo->buffer($buffer);
  }

  /**
   * handler for upload requests
   */
  static function processUpload()
  {
    require_once 'packages/mosaico/backend-php/config.php';
    require_once 'packages/mosaico/backend-php/premailer.php';

    global $config;
    //CRM_Core_Error::debug_var('$config', $config);
    global $http_return_code;

    $civiConfig = CRM_Core_Config::singleton();
    CRM_Core_Error::debug_var('$civiConfig', $civiConfig);

    //DS FIXME: replace this with civi config
    $config = array(
      /* base url for image folders */
      //'BASE_URL' => ( array_key_exists( "HTTPS", $_SERVER ) ? "https://" : "http://" ) . $_SERVER[ "HTTP_HOST" ] . dirname( dirname( $_SERVER[ "PHP_SELF" ] ) ) . "/",
      'BASE_URL' => $civiConfig->extensionsURL . 'uk.co.vedaconsulting.mosaico/packages/mosaico/',

      /* local file system base path to where image directories are located */
      //'BASE_DIR' => dirname( dirname( $_SERVER[ "SCRIPT_FILENAME" ] ) ) . "/",
      'BASE_DIR' => $civiConfig->extensionsDir . 'uk.co.vedaconsulting.mosaico/packages/mosaico/',

      /* url to the uploads folder (relative to BASE_URL) */
      'UPLOADS_URL' => "uploads/",

      /* local file system path to the uploads folder (relative to BASE_DIR) */
      'UPLOADS_DIR' => "uploads/",

      /* url to the static images folder (relative to BASE_URL) */
      'STATIC_URL' => "uploads/static/",

      /* local file system path to the static images folder (relative to BASE_DIR) */
      'STATIC_DIR' => "uploads/static/",

      /* url to the thumbnail images folder (relative to BASE_URL */
      'THUMBNAILS_URL' => "uploads/thumbnails/",

      /* local file system path to the thumbnail images folder (relative to BASE_DIR) */
      'THUMBNAILS_DIR' => "uploads/thumbnails/",

      /* width and height of generated thumbnails */
      'THUMBNAIL_WIDTH' => 90,
      'THUMBNAIL_HEIGHT' => 90
    );
    CRM_Core_Error::debug_var('$config', $config);
    CRM_Core_Error::debug_var('$_FILES', $_FILES);

    $files = array();

    if ( $_SERVER[ "REQUEST_METHOD" ] == "GET" )
    {
      $dir = scandir( $config[ BASE_DIR ] . $config[ UPLOADS_DIR ] );

      foreach ( $dir as $file_name )
      {
        $file_path = $config[ BASE_DIR ] . $config[ UPLOADS_DIR ] . $file_name;

        if ( is_file( $file_path ) )
        {
          $size = filesize( $file_path );

          $file = [
            "name" => $file_name,
            "url" => $config[ BASE_URL ] . $config[ UPLOADS_URL ] . $file_name,
            "size" => $size
          ];

          if ( file_exists( $config[ BASE_DIR ] . $config[ THUMBNAILS_DIR ] . $file_name ) )
          {
            $file[ "thumbnailUrl" ] = $config[ BASE_URL ] . $config[ THUMBNAILS_URL ] . $file_name;
          }

          $files[] = $file;
          CRM_Core_Error::debug_var('$files1', $files);
        }
      }
    }
    else if ( !empty( $_FILES ) )
    {
      foreach ( $_FILES[ "files" ][ "error" ] as $key => $error )
      {
        CRM_Core_Error::debug_var('$error', $error);
        if ( $error == UPLOAD_ERR_OK )
        {
          $tmp_name = $_FILES[ "files" ][ "tmp_name" ][ $key ];
          CRM_Core_Error::debug_var('$tmp_name', $tmp_name);

          $file_name = $_FILES[ "files" ][ "name" ][ $key ];

          $file_path = $config[ BASE_DIR ] . $config[ UPLOADS_DIR ] . $file_name;
          CRM_Core_Error::debug_var('$file_path', $file_path);

          if ( move_uploaded_file( $tmp_name, $file_path ) === TRUE )
          {
            $size = filesize( $file_path );

            $image = new Imagick( $file_path );

            $image->resizeImage( $config[ THUMBNAIL_WIDTH ], $config[ THUMBNAIL_HEIGHT ], Imagick::FILTER_LANCZOS, 1.0, TRUE );
            $image->writeImage( $config[ BASE_DIR ] . $config[ THUMBNAILS_DIR ] . $file_name );
            $image->destroy();

            $file = array(
              "name" => $file_name,
              "url" => $config[ BASE_URL ] . $config[ UPLOADS_URL ] . $file_name,
              "size" => $size,
              "thumbnailUrl" => $config[ BASE_URL ] . $config[ THUMBNAILS_URL ] . $file_name
            );

            $files[] = $file;
            CRM_Core_Error::debug_var('$files2', $files);
          }
          else
          {
            $http_return_code = 500;
            return;
          }
        }
        else
        {
          $http_return_code = 400;
          return;
        }
      }
    }
    CRM_Core_Error::debug_var('$files', $files);

    header( "Content-Type: application/json; charset=utf-8" );
    header( "Connection: close" );

    echo json_encode( array( "files" => $files ) );
    CRM_Utils_System::civiExit();
  }

}
