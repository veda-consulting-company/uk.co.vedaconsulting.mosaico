(function(angular, $, _) {

  // This provides additional actions for editing a Mosaico mailing.
  // It coexists with crmMailing's EditMailingCtrl.
  angular.module('crmCxn').controller('CrmMosaicoMixinCtrl', function CrmMosaicoMixinCtrl($scope, dialogService, crmMosaicoTemplates, crmStatus) {
    // var ts = $scope.ts = CRM.ts(null);

    // Main data is in $scope.mailing, $scope.mosaicoCtrl.template

    // Hrm, would like `ng-controller="CrmMosaicoMixinCtrl as mosaicoCtrl`, but that's not working...
    $scope.mosaicoCtrl = {
      templates: [],
      select: function(mailing, template) {
        var topt = mailing.template_options = mailing.template_options || {};
        var promise = crmMosaicoTemplates.getFull(template).then(function(tplCtnt){
          topt.mosaicoTemplate = template.id;
          topt.mosaicoMetadata = tplCtnt.metadata;
          topt.mosaicoContent = tplCtnt.content;
          mailing.body_html = tplCtnt.html;
          // console.log('select', {isAr1: _.isArray(mailing.template_options), isAr2: _.isArray(topt), m: mailing, t: template});
          $scope.mosaicoCtrl.edit(mailing);
        });
        return crmStatus({start: ts('Loading...'), success: null}, promise);
      },
      getTemplate: function(mailing) {
        if (!mailing || !mailing.template_options || !mailing.template_options.mosaicoTemplate) {
          return null;
        }
        var matches = _.where($scope.mosaicoCtrl.templates, {
          id: mailing.template_options.mosaicoTemplate
        });
        return matches.length > 0 ? matches[0] : null;
      },
      reset: function(mailing) {
        delete mailing.template_options.mosaicoTemplate;
        delete mailing.template_options.mosaicoMetadata;
        delete mailing.template_options.mosaicoContent;
        mailing.body_html = '';
      },
      // Open a dialog running Mosaico in an iframe.
      edit: function(mailing) {
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
        window.top.crmMosaicoIframe = function(newWindow, Mosaico, config, plugins) {
          plugins.push(function(viewModel) {
            mosaicoPlugin(newWindow.ko, viewModel);
          });

          if (mailing.template_options && mailing.template_options.mosaicoMetadata) {
            Mosaico.start(config, undefined,
              JSON.parse(mailing.template_options.mosaicoMetadata),
              JSON.parse(mailing.template_options.mosaicoContent),
              plugins);
            return;
          }

          var template = $scope.mosaicoCtrl.getTemplate(mailing);
          if (template) {
            Mosaico.start(config, template.path, undefined, undefined, plugins);
            return;
          }

          CRM.alert('Cannot edit mailing');
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

    crmMosaicoTemplates.whenLoaded().then(function(){
      $scope.mosaicoCtrl.templates = crmMosaicoTemplates.getAll();
    });

    // See https://github.com/voidlabs/mosaico/wiki/Mosaico-Plugins
    // Generally: Implement the in-dialog "Save" and "Test" buttons.
    function mosaicoPlugin(ko, viewModel) {
      viewModel.logoUrl = null;

      function syncModel() {
        $scope.mailing.body_html = viewModel.exportHTML();
        $scope.mailing.template_options = $scope.mailing.template_options || {};
        // Mosaico exports JSON. Keep their original encoding... or else the loader throws an error.
        $scope.mailing.template_options.mosaicoMetadata = viewModel.exportMetadata();
        $scope.mailing.template_options.mosaicoContent = viewModel.exportJSON();
      }

      var saveCmd = {
        name: 'Save', // l10n happens in the template
        enabled: ko.observable(true)
      };
      saveCmd.execute = function() {
        saveCmd.enabled(false);
        viewModel.metadata.changed = Date.now();
        syncModel();
        saveCmd.enabled(true);
        dialogService.close('crmMosaicoEditorDialog');
        $scope.save();
      };
      viewModel.save = saveCmd;

      var testCmd = {
        name: 'Test', // l10n happens in the template
        enabled: ko.observable(true)
      };
      testCmd.execute = function() {
        // testCmd.enabled(false);
        // CRM.alert('TODO: Test');
        // testCmd.enabled(true);
        syncModel();

        var model = {mailing: $scope.mailing, attachments: $scope.attachments};
        var options = CRM.utils.adjustDialogDefaults(angular.extend(
          {
            autoOpen: false,
            title: ts('Test Mailing')
          },
          options
        ));
        return dialogService.open('crmMosaicoPreviewDialog', '~/crmMosaico/PreviewDialogCtrl.html', model, options);
      };
      viewModel.test = testCmd;
    }

  });

})(angular, CRM.$, CRM._);
