(function(angular, $, _) {
  angular.module('crmMosaico').directive('crmMosaicoBlockMailing', function($q, crmMetadata, crmUiHelp) {
    var directiveName = 'crmMosaicoBlockMailing', templateUrl = '~/crmMosaico/BlockMailing.html';
    return {
      scope: {
        crmMailing: '@'
      },
      templateUrl: templateUrl,
      link: function (scope, elm, attr) {
        // Common elements - like crmMailingSimpleDirective
        scope.$parent.$watch(attr.crmMailing, function(newValue){
          scope.mailing = newValue;
        });
        scope.crmMailingConst = CRM.crmMailing;
        scope.ts = CRM.ts(null);
        scope.hs = crmUiHelp({file: 'CRM/Mailing/MailingUI'});
        scope[directiveName] = attr[directiveName] ? scope.$parent.$eval(attr[directiveName]) : {};
        $q.when(crmMetadata.getFields('Mailing'), function(fields) {
          scope.mailingFields = fields;
        });

        // Unique elements
        scope.groupNames = CRM.crmMailing.testGroupNames || CRM.crmMailing.groupNames;
      }
    };
  });
})(angular, CRM.$, CRM._);
