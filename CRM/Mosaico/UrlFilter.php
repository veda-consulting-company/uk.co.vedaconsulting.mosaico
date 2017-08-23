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
    // Ex: "https://example.org:8080/subdir/"
    $stdBase = \CRM_Utils_File::addTrailingSlash($this->getBaseUrl(), '/');
    // Ex: "https://example.org:8080"
    $domainBase = $this->createDomainBase($stdBase);

    $callback = function ($matches) use ($stdBase, $domainBase) {
      if (preg_match('/^https?:/', $matches[2]) || empty($matches[2])) {
        // If path is absolute, or empty, return it without processing
        return $matches[0];
      }

      if (preg_match('/^templates\//', $matches['2'])) {
        // If path is relative, but prefixed with templates directory, make it absolute
        $templateBase = CRM_Mosaico_Utils::getTemplatesUrl('absolute');
        return $matches[1] . $templateBase . substr($matches[2], 9) . $matches[3];
      }

      if ($matches[2]{0} === '/') {
        // If path is is relative to domain root, return with domain prepended.
        return $matches[1] . $domainBase . $matches[2] . $matches[3];
      }
      else {
        // Return
        return $matches[1] . $stdBase . $matches[2] . $matches[3];
      }
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
      return CIVICRM_UF_BASEURL;
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

  /**
   * @param string $url
   *   Ex: 'https://user:pass@host:port/path?query'.
   * @return string
   *   Ex: 'https://host:port'.
   */
  protected function createDomainBase($url) {
    $result = parse_url($url, PHP_URL_SCHEME);
    $result .= '://';
    $result .= parse_url($url, PHP_URL_HOST);
    $port = parse_url($url, PHP_URL_PORT);
    if ($port) {
      $result .= ":" . $port;
    }
    return $result;
  }

}
