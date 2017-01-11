<?php

/**
 * Class CRM_Mosaico_MosaicoComposer
 *
 * Responsible for composing individual emails based on the email content.
 *
 * For the most part, we use the same structure as CiviMail -- with some
 * exceptions (like disabling Smarty).
 */
class CRM_Mosaico_MosaicoComposer extends \Civi\FlexMailer\Listener\DefaultComposer {

  public function isSupported(\CRM_Mailing_DAO_Mailing $mailing) {
    return $mailing->template_type === 'mosaico';
  }

  public function createTokenProcessorContext(
    \Civi\FlexMailer\Event\ComposeBatchEvent $e
  ) {
    $context = parent::createTokenProcessorContext($e);
    // Smarty would break Mosaico CSS.
    $context['smarty'] = FALSE;
    return $context;
  }

  public function createMailParams(
    \Civi\FlexMailer\Event\ComposeBatchEvent $e,
    \Civi\FlexMailer\FlexMailerTask $task,
    \Civi\Token\TokenRow $row
  ) {
    $mailParams = parent::createMailParams($e, $task, $row);
    $mailParams['X-CiviMail-Mosaico'] = 'Yes';
    return $mailParams;
  }

  public function createMessageTemplates($mailing) {
    $templates = parent::createMessageTemplates($mailing);
    // unset($templates['text']);
    return $templates;
  }

}
