<?php

/**
 * Interface CRM_Mosaico_Graphics_Interface
 *
 * This is an abstraction to help us among a couple graphics backends.
 *
 * NOTE: This interface has not been publicly documented because it's not
 * been thought through very aggressively, and the contract could
 * probably be better. As long as it remains internal, we have some
 * flexibility to clean it up.
 */
interface CRM_Mosaico_Graphics_Interface {

  /**
   * Generate a placeholder image.
   *
   * @param int $width
   * @param int $height
   * @return mixed
   */
  public function sendPlaceholder($width, $height);

}
