(function(angular, $, _) {
  // "crmMosaicoTemplateItem" displays an actionable thumbnail.
  // To specify action handlers, use `on-item-click`, `on-item-preview`, etal.
  // To conditionally enable/disable actions, use `check-item-click`, `check-item-preview`, etal.
  // Example usage: <div crm-mosaico-template-item="{state: 'selected', title: 1, subtitle: 2, thumbnail: 3}" on-item-click="alert('Click')" on-item-preview="alert('Preview')"></div>
  // Avaiable state options are (select, selected, configure, new)
  angular.module('crmMosaico').directive('crmMosaicoTemplateItem', function() {
    return {
      restrict: 'AE',
      templateUrl: '~/crmMosaico/TemplateItem.html',
      scope: {
        crmMosaicoTemplateItem: '='
      },
      link: function($scope, $el, $attr) {
        var ts = $scope.ts = CRM.ts('mosaico');

        $scope.$watch('crmMosaicoTemplateItem', function(newValue) {
          $scope.myOptions = newValue;

          // Template default action label based on current state
          if (newValue['state']) {
            var clickActionLabels = {
              select: "Select",
              selected: "Edit",
              configure: "Edit",
              new: "New"
            };
            var clickActionLabel = clickActionLabels[newValue['state']];
            $scope.actionLabel = clickActionLabel;
          }
        });
        $scope.hasAction = function hasAction(action) {
          if (!$attr['on' + action]) return false;
          if ($attr['check' + action]) {
            if (!$scope.$parent.$eval($attr['check' + action])) {
              return false;
            }
          }
          return true;
        };
        $scope.fireAction = function fireAction(action) {
          $scope.$parent.$eval($attr[action], {});
        };
      }
    };
  });
})(angular, CRM.$, CRM._);
