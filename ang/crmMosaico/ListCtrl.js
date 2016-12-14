(function(angular, $, _) {

  angular.module('crmMosaico').config(function($routeProvider) {
      $routeProvider.when('/mosaico-template', {
        controller: 'CrmMosaicoListCtrl',
        templateUrl: '~/crmMosaico/ListCtrl.html',
        resolve: {
          tpls: function(crmMosaicoTemplates){
            return crmMosaicoTemplates.whenLoaded();
          }
        }
      });
    }
  );

  angular.module('crmMosaico').controller('CrmMosaicoListCtrl', function($scope, crmApi, crmStatus, crmUiHelp, crmMosaicoTemplates) {
    // The ts() and hs() functions help load strings for this module.
    var ts = $scope.ts = CRM.ts('mosaico');
    var hs = $scope.hs = crmUiHelp({file: 'CRM/crmMosaico/ListCtrl'}); // See: templates/CRM/crmMosaico/ListCtrl.hlp

    $scope.crmMosaicoTemplates = crmMosaicoTemplates;

    $scope.createTpl = function(tpl) {
      return crmStatus(
        {start: ts('Creating...'), success: ts('Created')},
        crmMosaicoTemplates.create({
          base: tpl.baseDetails.name,
          title: ts('%1 (New Template)', {1: tpl.type})
        })
      ).then(function(newTpl){
        return $scope.editTpl(newTpl);
      });
    };

    $scope.copyTpl = function(tpl) {
      return crmStatus(
        {start: ts('Copying...'), success: ts('Copied')},
        crmMosaicoTemplates.clone({
          id: tpl.id,
          title: ts('%1 (Copy)', {1: tpl.title})
        })
      ).then(function(newTpl){
        return $scope.editTpl(newTpl);
      });
    };

    $scope.editTpl = function(tpl) {
      CRM.alert('Edit: ' + tpl.title); // FIXME
    };

    $scope.deleteTpl = function(tpl) {
      return crmStatus(
        {start: ts('Deleting...'), success: ts('Deleted')},
        crmMosaicoTemplates.delete(tpl)
      );
    };

  });

})(angular, CRM.$, CRM._);
