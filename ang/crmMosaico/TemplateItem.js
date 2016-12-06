(function(angular, $, _) {
  // "crmMosaicoTemplateItem" displays an actionable thumbnail.
  // To specify action handlers, use `on-item-click` and `on-item-preview`.
  // Example usage: <div crm-mosaico-template-item="{title: 1, subtitle: 2, thumbnail: 3}" on-item-click="alert('Click')" on-item-preview="alert('Preview')"></div>
  angular.module('crmMosaico').directive('crmMosaicoTemplateItem', function() {
    return {
      restrict: 'AE',
      templateUrl: '~/crmMosaico/TemplateItem.html',
      scope: {
        crmMosaicoTemplateItem: '='
      },
      link: function($scope, $el, $attr) {
        var ts = $scope.ts = CRM.ts('mosaico');
        $scope.$watch('crmMosaicoTemplateItem', function(newValue){
          $scope.myOptions = newValue;
        });
        $scope.hasAction = function hasAction(action) {
          return !!$attr[action];
        };
        $scope.fireAction = function fireAction(action) {
          $scope.$parent.$eval($attr[action], {});
        };
      }
    };
  });
})(angular, CRM.$, CRM._);
