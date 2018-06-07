(function(angular, $, _) {
  angular.module('crmMosaico').directive('crmMosaicoBlockMailing', function($q, crmMetadata, crmUiHelp, crmMailingSimpleDirective) {
    return crmMailingSimpleDirective('crmMosaicoBlockMailing', '~/crmMosaico/BlockMailing.html');
  });
})(angular, CRM.$, CRM._);
