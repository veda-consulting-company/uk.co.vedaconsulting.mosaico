(function(angular, $, _) {
  angular.module('crmMosaico').directive('crmMosaicoBlockDesign', function($q, crmUiHelp) {
    return {
      scope: {
        crmMosaicoCtrl: '@',
        crmMailing: '@',
        crmMailingAttachments: '@'
      },
      templateUrl: '~/crmMosaico/BlockDesign.html',
      link: function (scope, elm, attr) {
        scope.$parent.$watch(attr.crmMailing, function(newValue){
          scope.mailing = newValue;
        });
        scope.$parent.$watch(attr.crmMailingAttachments, function(newValue){
          scope.attachments = newValue;
        });
        scope.$parent.$watch(attr.crmMosaicoCtrl, function(newValue){
          scope.mosaicoCtrl = newValue;
        });
        scope.crmMailingConst = CRM.crmMailing;
        scope.ts = CRM.ts(null);
        scope.hs = crmUiHelp({file: 'CRM/Mailing/MailingUI'});
      }
    };
  });
})(angular, CRM.$, CRM._);
