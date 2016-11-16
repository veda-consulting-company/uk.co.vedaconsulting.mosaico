(function(angular, $, _) {

  // This provides additional actions for editing a Mosaico mailing.
  // It coexists with crmMailing's EditMailingCtrl.
  angular.module('crmCxn').controller('CrmMosaicoMixinCtrl', function CrmMosaicoMixinCtrl($scope, dialogService, crmMosaicoTemplates) {
    // var ts = $scope.ts = CRM.ts(null);

    // Main data is in $scope.mailing, $scope.mosaicoCtrl.template

    // Hrm, would like `ng-controller="CrmMosaicoMixinCtrl as mosaicoCtrl`, but that's not working...
    $scope.mosaicoCtrl = {
      template: null,
      templates: [],
      select: function(template) {
        $scope.mosaicoCtrl.template = template;
        $scope.mosaicoCtrl.edit();
      },
      preview: function(template) {
        CRM.alert('Preview: ' + template.title);
      },
      edit: function() {
        var model = {url: CRM.url('civicrm/mosaico/iframe', 'snippet=1')};
        var options = CRM.utils.adjustDialogDefaults(angular.extend(
          {
            autoOpen: false,
            height: '96%',
            width: '96%',
            title: ts('Edit Design')
          },
          options
        ));
        window.top.crmMosaicoIframe = function(newWindow, config, plugins) {
          config.template = $scope.mosaicoCtrl.template.path;
          config.data = null;
          plugins.push(function(viewModel) {
            mosaicoPlugin(newWindow.ko, viewModel);
          });
        };
        return dialogService.open('crmMosaicoEditorDialog', '~/crmMosaico/EditorDialogCtrl.html', model, options)
          .then(function(item) {
            // mailing.msg_template_id = item.id;
            return item;
          });
      }
    };

    $scope.openAdvancedOptions = function() {
      var model = {mailing: $scope.mailing, attachments: $scope.attachments};
      var options = CRM.utils.adjustDialogDefaults(angular.extend(
        {
          autoOpen: false,
          title: ts('Advanced')
        },
        options
      ));
      return dialogService.open('crmMosaicoAdvancedDialog', '~/crmMosaico/AdvancedDialogCtrl.html', model, options);
    };

    crmMosaicoTemplates.getAll().then(function(tpls){
      $scope.mosaicoCtrl.templates = tpls;
    });

    // See https://github.com/voidlabs/mosaico/wiki/Mosaico-Plugins
    function mosaicoPlugin(ko, viewModel) {
      var saveCmd = {
        name: 'Save', // l10n happens in the template
        enabled: ko.observable(true)
      };
      saveCmd.execute = function() {
        CRM.alert('TODO: Save');
        saveCmd.enabled(false);
        viewModel.metadata.changed = Date.now();
        console.log({metadata: viewModel.exportMetadata(), content: viewModel.exportJSON(), html: viewModel.exportHTML()});
        saveCmd.enabled(true);
        dialogService.close('crmMosaicoEditorDialog');
      };
      viewModel.save = saveCmd;

      var testCmd = {
        name: 'Test', // l10n happens in the template
        enabled: ko.observable(true)
      };
      testCmd.execute = function() {
        saveCmd.enabled(false);
        CRM.alert('TODO: Test');
        saveCmd.enabled(true);
      };
      viewModel.test = testCmd;
    }

  });

})(angular, CRM.$, CRM._);
