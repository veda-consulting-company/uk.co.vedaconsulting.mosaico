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

    $scope.createTpl = function(template) {
      return crmStatus(
        {start: ts('Creating...'), success: ts('Created')},
        crmMosaicoTemplates.copy(template, {title: 'New template'})
      ).then(function(newTemplate){
        return $scope.editTpl(newTemplate);
      });
    };

    $scope.copyTpl = function(template) {
      return crmStatus(
        {start: ts('Copying...'), success: ts('Copied')},
        crmMosaicoTemplates.copy(template, {title: ts('Copy of %1', {1: template.title})})
      ).then(function(newTemplate){
        return $scope.editTpl(newTemplate);
      });
    };

    $scope.editTpl = function(template) {
      CRM.alert('Edit: ' + template.title); // FIXME
    };

    $scope.deleteTpl = function(template) {
      return crmStatus(
        {start: ts('Deleting...'), success: ts('Deleted')},
        crmMosaicoTemplates.delete(template)
      );
    };

  });

})(angular, CRM.$, CRM._);
