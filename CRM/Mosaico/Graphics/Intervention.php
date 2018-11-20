<?php

use CRM_Mosaico_ExtensionUtil as E;
use Intervention\Image\ImageManagerStatic as Image;

/**
 * Class CRM_Mosaico_Graphics_Intervention
 *
 * @see http://image.intervention.io/getting_started/introduction
 */
class CRM_Mosaico_Graphics_Intervention implements CRM_Mosaico_Graphics_Interface {

  const FONT_PATH = 'packages/mosaico/dist/vendor/notoregular/NotoSans-Regular-webfont.ttf';

  /**
   * CRM_Mosaico_Graphics_Intervention constructor.
   *
   * @param array $options
   * @see Image::configure()
   */
  public function __construct($options) {
    Image::configure($options);
  }

  public function sendPlaceholder($width, $height) {
    $img = Image::canvas($width, $height, '#707070');

    $x = 0;
    $y = 0;
    $size = 40;
    while ($y < $height) {
      $points = array(
        ["x" => $x, "y" => $y],
        ["x" => $x + $size, "y" => $y],
        ["x" => $x + $size * 2, "y" => $y + $size],
        ["x" => $x + $size * 2, "y" => $y + $size * 2],
      );
      $img->polygon(self::flattenPoints($points), function ($draw) {
        $draw->background("#808080");
      });
      $points = array(
        ["x" => $x, "y" => $y + $size],
        ["x" => $x + $size, "y" => $y + $size * 2],
        ["x" => $x, "y" => $y + $size * 2],
      );
      $img->polygon(self::flattenPoints($points), function ($draw) {
        $draw->background("#808080");
      });
      $x += $size * 2;
      if ($x > $width) {
        $x = 0;
        $y += $size * 2;
      }
    }
    $img->text("{$width} x {$height}", $width / 2, $height / 2, function ($font) use ($width) {
      $font->file(E::path(self::FONT_PATH));
      $font->size($width / 5);
      $font->align('center');
      $font->valign('middle');
      $font->color("#B0B0B0");
    });

    echo $img->response('png');
  }

  /**
   * @param array $points
   *   List of points; each is an array of "x","y" values.
   *   Ex: $points[0]=['x'=>'100', 'y'=>'200'];
   *   Ex: $points[1]=['x'=>'300', 'y'=>'400'];
   * @return array
   *   List of arrays. All "x","y" values indicated positoinally.
   *   Ex: ['100','200','300','400'].
   */
  protected static function flattenPoints($points) {
    $r = [];
    foreach ($points as $point) {
      $r[] = $point['x'];
      $r[] = $point['y'];
    }
    return $r;
  }

}
