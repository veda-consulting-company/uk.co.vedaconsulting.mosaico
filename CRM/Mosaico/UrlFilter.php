<?php

use Civi\FlexMailer\Event\ComposeBatchEvent;
use Civi\FlexMailer\Listener\SimpleFilter;

/**
 * Class ImageUrlFilter
 * @package Civi\FlexMailer\Listener
 *
 * Find relative URLs and convert to absolute URLs.
 */
class CRM_Mosaico_UrlFilter extends \Civi\FlexMailer\Listener\BaseListener {

  /**
   * @var string|NULL
   */
  protected $baseUrl = NULL;

  public function onCompose(ComposeBatchEvent $e) {
    if (!$this->isActive() || $e->getMailing()->template_type !== 'mosaico') {
      return;
    }

    SimpleFilter::byColumn($e, 'html', array($this, 'filterHtml'));
  }

  /**
   * Find any image URLs and ensure they're absolute (not relative).
   *
   * @param array<string> $htmls
   * @return array<string>
   *   Filtered HTML, with relative IMG url's changed to absolute URLs.
   */
  public function filterHtml($htmls) {
    $callback = function ($matches) {
      if (preg_match('/^https?:/', $matches[2])) {
        return $matches[0];
      }

      return $matches[1] . $this->getBaseUrl() . $matches[2] . $matches[3];
    };

    $htmls = preg_replace_callback(';(\<img [^>]*src *= *")([^">]+)(");i', $callback, $htmls);
    $htmls = preg_replace_callback(';(\<img [^>]*src *= *\')([^">]+)(\');i', $callback, $htmls);
    $htmls = preg_replace_callback(';(\<table [^>]*background *= *")([^">]+)(");i', $callback, $htmls);
    $htmls = preg_replace_callback(';(\<table [^>]*background *= *")([^\'>]+)(\');i', $callback, $htmls);
    // WISHLIST: CSS backgrounds?
    return $htmls;
  }

  /**
   * @return string|NULL
   */
  public function getBaseUrl() {
    if ($this->baseUrl === NULL) {
      $this->baseUrl = \CRM_Utils_File::addTrailingSlash(CIVICRM_UF_BASEURL, '/');
    }
    return $this->baseUrl;
  }

  /**
   * @param string|NULL $baseUrl
   * @return CRM_Mosaico_UrlFilter
   */
  public function setBaseUrl($baseUrl) {
    $this->baseUrl = $baseUrl;
    return $this;
  }

}
