<?php

require_once __DIR__ . '/TestCase.php';

/**
 * Class CRM_Mosaico_AbDemuxTest
 *
 * General integration test to ensure that A/B tests are generated when
 * Mosaico mailings specify multiple variants.
 *
 * @group headless
 */
class CRM_Mosaico_AbDemuxTest extends CRM_Mosaico_TestCase implements \Civi\Test\HeadlessInterface, \Civi\Test\TransactionalInterface {

  use \Civi\Test\ContactTestTrait;
  use \Civi\Test\DbTestTrait;

  /**
   * Civi\Test has many helpers, like install(), uninstall(), sql(), and sqlFile().
   * See: https://github.com/civicrm/org.civicrm.testapalooza/blob/master/civi-test.md
   */
  public function setUpHeadless() {
    return \Civi\Test::headless()
      ->install(['org.civicrm.flexmailer', 'uk.co.vedaconsulting.mosaico'])
      ->apply();
  }

  public function getVariantExamples() {
    $cases = array();

    // array($field, $inputValue, $expectRegex).

    $cases[] = [
      ['subject' => 'New Subject A'],
      ['subject' => 'New Subject B'],
      ['subject' => 'New Subject A', 'body_html' => 'Placeholder'],
      ['subject' => 'New Subject B', 'body_html' => 'Placeholder'],
    ];
    $cases[] = [
      ['subject' => 'New Subject A', 'body_html' => 'New Html A'],
      ['subject' => 'New Subject B', 'body_html' => 'New Html B'],
      ['subject' => 'New Subject A', 'body_html' => 'New Html A'],
      ['subject' => 'New Subject B', 'body_html' => 'New Html B'],
    ];

    return $cases;
  }

  /**
   * Generate a preview of an outgoing email. Check that the field is
   * rendered the expected way.
   *
   * @dataProvider getVariantExamples
   */
  public function testMailingSubmit($inputVariantA, $inputVariantB, $expectVariantA, $expectVariantB) {
    $this->assertTrue(Civi::container()->has('mosaico_ab_demux'), 'Mosaico services should be active in test environment');

    Civi::settings()->set('disable_mandatory_tokens_check', TRUE);
    $contactId = $this->createLoggedInUser();

    $createParams = [
      'template_type' => 'mosaico',
      'template_options' => [
        'variants' => [
          0 => $inputVariantA,
          1 => $inputVariantB,
        ]
      ],
      'subject' => 'Placeholder',
      'body_text' => "Placeholder",
      'body_html' => "Placeholder",
      'name' => 'AbDemuxTest' . md5(uniqid()),
      'created_id' => $contactId,
      'header_id' => '',
      'footer_id' => '',
    ];
    $createResult = $this->callAPISuccess('mailing', 'create', $createParams);

    $submitParams = [
      'id' => $createResult['id'],
      'scheduled_date' => '2014-12-13 10:00:00',
      'approval_date' => '2014-12-13 00:00:00',
    ];
    $submitResult = $this->callAPISuccess('mailing', 'submit', $submitParams, __FUNCTION__, __FILE__);
    $this->assertTrue(is_numeric($submitResult['id']));
    $this->assertEquals($createResult['id'], $submitResult['id']);
    $this->assertTrue(is_numeric($submitResult['values'][$createResult['id']]['scheduled_id']));
    // $this->assertEquals($submitParams['scheduled_date'], $submitResult['values'][$createResult['id']]['scheduled_date']);

    $ab = $submitResult['values'][$createResult['id']]['_mailing_ab'];
    $mailingJobCounts = [
      ['created', $createResult['id'], 1],
      ['a', $ab['mailing_id_a'], 1],
      ['b', $ab['mailing_id_b'], 1],
      ['c', $ab['mailing_id_c'], 0],
    ];
    foreach ($mailingJobCounts as $mailingJobCount) {
      list ($name, $mailingId, $expectJobCount) = $mailingJobCount;
      $this->assertDBQuery($expectJobCount, 'SELECT count(*) FROM civicrm_mailing_job WHERE mailing_id = %1', [
        1 => [$mailingId, 'Integer'],
      ], "Check that item \"$name\" has $expectJobCount job(s)");
    }

    $aLoad = $this->callAPISuccess('Mailing', 'getsingle', ['id' => $ab['mailing_id_a']]);
    $bLoad = $this->callAPISuccess('Mailing', 'getsingle', ['id' => $ab['mailing_id_b']]);
    $this->assertAttributesEquals($expectVariantA, $aLoad);
    $this->assertAttributesEquals($expectVariantB, $bLoad);
  }

}
