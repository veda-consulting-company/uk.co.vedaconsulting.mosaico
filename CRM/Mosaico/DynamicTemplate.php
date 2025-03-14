<?php

class CRM_Mosaico_DynamicTemplate {

  public function buildAsset($asset, $params, &$mimeType, &$content): void {
    $templates = CRM_Mosaico_BAO_MosaicoTemplate::findBaseTemplates();
    $template = $templates[$params['name']] ?? NULL;
    if (empty($template)) {
      throw new \CRM_Core_Exception("Unknown Mosaico template '{$params['name']}'.");
    }

    $mimeType = 'text/html';
    $params['_template'] = $template;;

    switch ($template['type']) {
      case 'php':
        $content = $this->renderPhp($template['src'], $params);
        break;

      // For Smarty support, need to map $template['src'] to be relative to a Smarty dir... then...
      // case 'tpl':
      //   $content = CRM_Core_Smarty::singleton()->fetchWith($name, $params);
      //   break;

      case 'html':
        // We shouldn't usually get to this line, but maybe if someone is doing dev and juggling files...
        $content = file_get_contents($template['src']);
        break;
    }
  }

  /**
   * Render a template.
   *
   * @param string $file
   *   Logical template name. See "./templates/{$name}.php".
   * @param array $args
   *   Variables to pass into the template.
   *
   * @return false|string
   */
  public function renderPhp(string $file, array $args) {
    $_view = ['file' => $file, 'args' => $args];
    $render = function () use ($_view) {
      extract($_view['args']);
      include $_view['file'];
    };
    ob_start();
    try {
      $render();
      return ob_get_clean();
    }
    catch (\Throwable $t) {
      ob_get_clean();
      throw $t;
    }
  }

}
