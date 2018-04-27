(function(angular, $, _) {
  angular.module('crmMosaico').directive('crmMosaicoBlockMailing', function($q, crmMetadata, crmUiHelp) {
    return crmMailingSimpleDirective('crmMosaicoBlockMailing', '~/crmMosaico/BlockMailing.html');
  });
})(angular, CRM.$, CRM._);
