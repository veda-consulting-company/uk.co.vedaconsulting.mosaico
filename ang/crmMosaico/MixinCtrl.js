(function(angular, $, _) {

  // This provides additional actions for editing a Mosaico mailing.
  // It coexists with crmMailing's EditMailingCtrl.
  angular.module('crmMosaico').controller('CrmMosaicoMixinCtrl', function CrmMosaicoMixinCtrl($scope, dialogService) {
    // var ts = $scope.ts = CRM.ts(null);

    var activeDialogs = {};

    // Open a dialog of advanced options.
    $scope.openAdvancedOptions = function() {
      var model = {mailing: $scope.mailing, attachments: $scope.attachments};
      var options = CRM.utils.adjustDialogDefaults(angular.extend(
        {
          autoOpen: false,
          title: ts('Advanced Settings'),
          width: 600,
          height: 'auto'
        },
        options
      ));
      activeDialogs.crmMosaicoAdvancedDialog = 1;
      return dialogService.open('crmMosaicoAdvancedDialog', '~/crmMosaico/AdvancedDialogCtrl.html', model, options)
        .finally(function(){ delete activeDialogs.crmMosaicoAdvancedDialog; });
    };

    $scope.$on("$destroy", function() {
      angular.forEach(activeDialogs, function(v,name){
        dialogService.cancel(name);
      });
    });

  });

})(angular, CRM.$, CRM._);
