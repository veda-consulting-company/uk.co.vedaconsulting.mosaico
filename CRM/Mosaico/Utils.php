<?php

require_once 'packages/mosaico/backend-php/premailer.php';

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

  static function getConfig() {
    static $mConfig = array();

    if (empty($mConfig)) {
      $civiConfig = CRM_Core_Config::singleton();

      //DS FIXME: replace this with civi config
      $mConfig = array(
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

        /* url to the thumbnail images folder (relative to'BASE_URL'*/
        'THUMBNAILS_URL' => "uploads/thumbnails/",

        /* local file system path to the thumbnail images folder (relative to BASE_DIR) */
        'THUMBNAILS_DIR' => "uploads/thumbnails/",

        /* width and height of generated thumbnails */
        'THUMBNAIL_WIDTH' => 90,
        'THUMBNAIL_HEIGHT' => 90
      );
    }
    CRM_Core_Error::debug_var('$mConfig', $mConfig);
    return $mConfig;
  }


  /**
   * handler for upload requests
   */
  static function processUpload()
  {
    $config = self::getConfig();

    global $http_return_code;

    $files = array();

    if ( $_SERVER[ "REQUEST_METHOD" ] == "GET" )
    {
      $dir = scandir( $config['BASE_DIR'] . $config['UPLOADS_DIR'] );

      foreach ( $dir as $file_name )
      {
        $file_path = $config['BASE_DIR'] . $config['UPLOADS_DIR'] . $file_name;

        if ( is_file( $file_path ) )
        {
          $size = filesize( $file_path );

          $file = [
            "name" => $file_name,
            "url" => $config['BASE_URL'] . $config['UPLOADS_URL'] . $file_name,
            "size" => $size
          ];

          if ( file_exists( $config['BASE_DIR'] . $config[ THUMBNAILS_DIR ] . $file_name ) )
          {
            $file[ "thumbnailUrl" ] = $config['BASE_URL'] . $config[ THUMBNAILS_URL ] . $file_name;
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

          $file_path = $config['BASE_DIR'] . $config['UPLOADS_DIR'] . $file_name;
          CRM_Core_Error::debug_var('$file_path', $file_path);

          if ( move_uploaded_file( $tmp_name, $file_path ) === TRUE )
          {
            $size = filesize( $file_path );

            $image = new Imagick( $file_path );

            $image->resizeImage( $config[ THUMBNAIL_WIDTH ], $config[ THUMBNAIL_HEIGHT ], Imagick::FILTER_LANCZOS, 1.0, TRUE );
            $image->writeImage( $config['BASE_DIR'] . $config[ THUMBNAILS_DIR ] . $file_name );
            $image->destroy();

            $file = array(
              "name" => $file_name,
              "url" => $config['BASE_URL'] . $config['UPLOADS_URL'] . $file_name,
              "size" => $size,
              "thumbnailUrl" => $config['BASE_URL'] . $config[ THUMBNAILS_URL ] . $file_name
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

  /**
   * handler for img requests
   */
  static function processImg()
  {
    if ( $_SERVER[ "REQUEST_METHOD" ] == "GET" )
    {
      $method = $_GET[ "method" ];

      $params = explode( ",", $_GET[ "params" ] );

      $width = (int) $params[ 0 ];
      $height = (int) $params[ 1 ];

      if ( $method == "placeholder" )
      {
        $image = new Imagick();

        $image->newImage( $width, $height, "#707070" );
        $image->setImageFormat( "png" );

        $x = 0;
        $y = 0;
        $size = 40;

        $draw = new ImagickDraw();

        while ( $y < $height )
        {
          $draw->setFillColor( "#808080" );

          $points = [
            [ "x" => $x, "y" => $y ],
            [ "x" => $x + $size, "y" => $y ],
            [ "x" => $x + $size * 2, "y" => $y + $size ],
            [ "x" => $x + $size * 2, "y" => $y + $size * 2 ]
          ];

          $draw->polygon( $points );

          $points = [
            [ "x" => $x, "y" => $y + $size ],
            [ "x" => $x + $size, "y" => $y + $size * 2 ],
            [ "x" => $x, "y" => $y + $size * 2 ]
          ];

          $draw->polygon( $points );

          $x += $size * 2;

          if ( $x > $width )
          {
            $x = 0;
            $y += $size * 2;
          }
        }

        $draw->setFillColor( "#B0B0B0" );
        $draw->setFontSize( $width / 5 );
        $draw->setFontWeight( 800 );
        $draw->setGravity( Imagick::GRAVITY_CENTER );
        $draw->annotation( 0, 0, $width . " x " . $height );

        $image->drawImage( $draw );

        header( "Content-type: image/png" );

        echo $image;
      }
      else
      {
        $file_name = $_GET[ "src" ];

        $path_parts = pathinfo( $file_name );

        switch ( $path_parts[ "extension" ] )
        {
        case "png":
          $mime_type = "image/png";
          break;

        case "gif":
          $mime_type = "image/gif";
          break;

        default:
          $mime_type = "image/jpeg";
          break;
        }

        $file_name = $path_parts[ "basename" ];

        $image = self::resizeImage( $file_name, $method, $width, $height );

        header( "Content-type: " . $mime_type );

        echo $image;
      }
    }
    CRM_Utils_System::civiExit();
  }

  /**
   * handler for dl requests
   */
  static function processDl()
  {
    $config = self::getConfig();
    global $http_return_code;

    /* run this puppy through premailer */

    $premailer = Premailer::html( $_POST[ "html" ], true, "hpricot", $config['BASE_URL'] );

    $html = $premailer[ "html" ];

    /* create static versions of resized images */

    $matches = [];

    $num_full_pattern_matches = preg_match_all( '#<img.*?src="([^"]*?\/[^/]*\.[^"]+)#i', $html, $matches );

    for ( $i = 0; $i < $num_full_pattern_matches; $i++ )
    {
      if ( stripos( $matches[ 1 ][ $i ], "/img?src=" ) !== FALSE )
      {
        $src_matches = [];

        if ( preg_match( '#/img\?src=(.*)&amp;method=(.*)&amp;params=(.*)#i', $matches[ 1 ][ $i ], $src_matches ) !== FALSE )
        {
          $file_name = urldecode( $src_matches[ 1 ] );
          $file_name = substr( $file_name, strlen( $config['BASE_URL'] . $config['UPLOADS_URL'] ) );

          $method = urldecode( $src_matches[ 2 ] );

          $params = urldecode( $src_matches[ 3 ] );
          $params = explode( ",", $params );
          $width = (int) $params[ 0 ];
          $height = (int) $params[ 1 ];

          $static_file_name = $method . "_" . $width . "x" . $height . "_" . $file_name;

          $html = str_ireplace( $matches[ 1 ][ $i ], $config['BASE_URL'] . $config['STATIC_URL'] . urlencode( $static_file_name ), $html );

          $image = self::resizeImage( $file_name, $method, $width, $height );

          $image->writeImage( $config['BASE_DIR'] . $config['STATIC_DIR'] . $static_file_name );
        }
      }
    }

    /* perform the requested action */

    switch ( $_POST[ "action" ] ) {
    case "download": {
      // save to message templates
      $messageTemplate = array(
        //'msg_text' => $formValues['text_message'],
        'msg_html'    => $html,
        'msg_subject' => "Mosaico saved - " . date('YmdHis'),
        'is_active'   => TRUE,
      );

      $messageTemplate['msg_title'] = $messageTemplate['msg_subject'];
      CRM_Core_BAO_MessageTemplate::add($messageTemplate);

      // download
      header( "Content-Type: application/force-download" );
      header( "Content-Disposition: attachment; filename=\"" . $_POST[ "filename" ] . "\"" );
      header( "Content-Length: " . strlen( $html ) );

      echo $html;

      break;
    }

    case "save": {
      // save to message templates
      $messageTemplate = array(
        //'msg_text' => $formValues['text_message'],
        'msg_html'    => $html,
        'msg_subject' => "Mosaico saved - " . date('YmdHis'),
        'is_active'   => TRUE,
      );
      $messageTemplate['msg_title'] = $messageTemplate['msg_subject'];
      $msgTpl = CRM_Core_BAO_MessageTemplate::add($messageTemplate);
      
      $mosaicoTemplate = array(
        //'msg_text' => $formValues['text_message'],
        'msg_tpl_id' => $msgTpl->id,
        'hash_key'   => $_POST['key'],
        'name'    => $_POST['name'],
        'html'    => $_POST['html'],
        'metadata' => $_POST['metadata'],
        'template' => $_POST['template'],
      );
      $mosTpl = new CRM_Mosaico_DAO_MessageTemplate();
      $mosTpl->msg_tpl_id = $msgTpl->id;
      $mosTpl->find(TRUE);
      $mosTpl->copyValues($mosaicoTemplate);
      $mosTpl->save();

      break;
    }

    case "email": {
      $to = $_POST[ "rcpt" ];
      $subject = $_POST[ "subject" ];

      /* mosaico
      $headers = array();

      $headers[] = "MIME-Version: 1.0";
      $headers[] = "Content-type: text/html; charset=iso-8859-1";
      $headers[] = "To: $to";
      $headers[] = "Subject: $subject";

      $headers = implode( "\r\n", $headers );

      if ( mail( $to, $subject, $html, $headers ) === FALSE )
      {
        $http_return_code = 500;
        return;
      }
       */

      $mailParams = array(
        //'groupName' => 'Activity Email Sender',
        'from' => 'cms46@mosaicoexample.org',
        'toName' => 'Test Recipient',
        'toEmail' => $to,
        'subject' => $subject,
        //'text' => $text_message,
        'html' => $html,
      );

      CRM_Core_Error::debug_var('$mailParams', $mailParams);
      if (!CRM_Utils_Mail::send($mailParams)) {
        return FALSE;
      }

      break;
    }
    }
    CRM_Utils_System::civiExit();
  }

  /**
   * function to resize images using resize or cover methods
   */
  static function resizeImage( $file_name, $method, $width, $height )
  {
    $config = self::getConfig();
    CRM_Core_Error::debug_var('$config in resizeImage()', $config);

    $image = new Imagick( $config['BASE_DIR'] . $config['UPLOADS_DIR'] . $file_name );

    if ( $method == "resize" )
    {
      $image->resizeImage( $width, $height, Imagick::FILTER_LANCZOS, 1.0 );
    }
    else // $method == "cover"
    {
      $image_geometry = $image->getImageGeometry();

      $width_ratio = $image_geometry[ "width" ] / $width;
      $height_ratio = $image_geometry[ "height" ] / $height;

      $resize_width = $width;
      $resize_height = $height;

      if ( $width_ratio > $height_ratio )
      {
        $resize_width = 0;
      }
      else
      {
        $resize_height = 0;
      }

      $image->resizeImage( $resize_width, $resize_height, Imagick::FILTER_LANCZOS, 1.0 );

      $image_geometry = $image->getImageGeometry();

      $x = ( $image_geometry[ "width" ] - $width ) / 2;
      $y = ( $image_geometry[ "height" ] - $height ) / 2;

      $image->cropImage( $width, $height, $x, $y );
    }

    return $image;
  }

}
