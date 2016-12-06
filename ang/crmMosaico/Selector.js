(function(angular, $, _) {
  // "crmMosaicoSelector" is a basic skeletal directive.
  // Example usage: <div crm-mosaico-selector="{}" on-select="alert("Selected " + selection.title)" on-open="alert('Opened ' + selection.title)"></div>
  angular.module('crmMosaico').directive('crmMosaicoSelector', function(crmMosaicoTemplates, dialogService) {
    return {
      restrict: 'AE',
      templateUrl: '~/crmMosaico/Selector.html',
      scope: {
        crmMosaicoSelector: '='
      },
      link: function($scope, $el, $attr) {
        var ts = $scope.ts = CRM.ts('mosaico');
        $scope.$watch('crmMosaicoSelector', function(newValue){
          $scope.myOptions = newValue;
        });

        $scope.selectedMosaicoTemplate = null;

        $scope.doSelect = function doSelect(template) {
          $scope.selectedMosaicoTemplate = template;
          $scope.$parent.$eval($attr.onSelect, {
            selectedMosaicoTemplate: $scope.selectedMosaicoTemplate
          });
          $scope.doOpen();
        };

        $scope.doOpen = function doOpen() {
          $scope.$parent.$eval($attr.onOpen, {
            selectedMosaicoTemplate: $scope.selectedMosaicoTemplate
          });
        };

        $scope.doPreview = function doPreview(template) {
          CRM.alert('Preview: ' + template.title);
        };

        $scope.configuredTemplates = $scope.baseTemplates = [];
        crmMosaicoTemplates.getBases().then(function(ts){
          $scope.baseTemplates = ts;
        });
        crmMosaicoTemplates.getConfigured().then(function(ts){
          $scope.configuredTemplates = ts;
        });
      }
    };
  });
})(angular, CRM.$, CRM._);
