(function(angular, $, _) {

  // The STALE_FLAG is added to `body_html` when it is out-of-sync with the `template_options.mosaicoContent`.
  // Why does this exist? We want to autosave while the user interacts with the editor.
  // However, rendering HTML while the user interacts is problematic (eg requires ~200ms on i3-10100/Firefox v93;
  // eg provokes JS warnings). So we save just `mosaicoContent` (JSON) and allow the `body_html` to grow stale.
  var STALE_FLAG = '<!--STALE-->';

  // This provides additional actions for editing a Mosaico mailing.
  // It coexists with crmMailing's EditMailingCtrl.
  angular.module('crmMosaico').controller('CrmMosaicoMixinCtrl', function CrmMosaicoMixinCtrl($scope, dialogService, crmMosaicoTemplates, crmStatus, CrmMosaicoIframe, $timeout) {
    // var ts = $scope.ts = CRM.ts(null);

    // Main data is in $scope.mailing, $scope.mosaicoCtrl.template

    var crmMosaicoIframe = null, activeDialogs = {};

    // Hrm, would like `ng-controller="CrmMosaicoMixinCtrl as mosaicoCtrl`, but that's not working...
    $scope.mosaicoCtrl = {
      templates: [],
      // Fill a given "mailing" which the chosen "template".
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
      // Figure out which "template" was previously used with a "mailing."
      getTemplate: function(mailing) {
        if (!mailing || !mailing.template_options || !mailing.template_options.mosaicoTemplate) {
          return null;
        }
        var matches = _.where($scope.mosaicoCtrl.templates, {
          id: mailing.template_options.mosaicoTemplate
        });
        return matches.length > 0 ? matches[0] : null;
      },
      // Reset all Mosaico data in a "mailing'.
      reset: function(mailing) {
        if (crmMosaicoIframe) crmMosaicoIframe.destroy();
        crmMosaicoIframe = null;
        delete mailing.template_options.mosaicoTemplate;
        delete mailing.template_options.mosaicoMetadata;
        delete mailing.template_options.mosaicoContent;
        mailing.body_html = '';
      },
      // Edit a mailing in Mosaico.
      edit: function(mailing) {
        if (crmMosaicoIframe) {
          crmMosaicoIframe.show();
          return;
        }

        function syncModel(viewModel, mode) {
          switch (mode) {
            case 'full':
              mailing.body_html = viewModel.exportHTML();
              break;

            case 'partial':
              if (mailing.body_html && !mailing.body_html.startsWith(STALE_FLAG)) {
                mailing.body_html = STALE_FLAG + mailing.body_html;
              }
              break;

            default:
              console.log('Unrecognized syncModel(...mode): ' + mode);
          }

          mailing.template_options = mailing.template_options || {};
          // Mosaico exports JSON. Keep their original encoding... or else the loader throws an error.
          mailing.template_options.mosaicoMetadata = viewModel.exportMetadata();
          mailing.template_options.mosaicoContent = viewModel.exportJSON();
        }

        crmMosaicoIframe = new CrmMosaicoIframe({
          syncInterval: 1000, // Moderately frequent. Note that the main editor has its own autosave on a longer schedule.
          model: {
            template: $scope.mosaicoCtrl.getTemplate(mailing).path,
            metadata: mailing.template_options.mosaicoMetadata,
            content: mailing.template_options.mosaicoContent
          },
          actions: {
            sync: function(ko, viewModel, iframeState) {
              if (iframeState.isVisible) {
                syncModel(viewModel, 'partial');
              }
            },
            close: function(ko, viewModel) {
              viewModel.metadata.changed = Date.now();
              syncModel(viewModel, 'full');
              // TODO: When autosave is better integrated, remove this.
              $timeout(function(){$scope.save();}, 100);
              crmMosaicoIframe.hide('crmMosaicoEditorDialog');
            },
            test: function(ko, viewModel) {
              syncModel(viewModel, 'full');

              var model = {mailing: $scope.mailing, attachments: $scope.attachments};
              var options = CRM.utils.adjustDialogDefaults(angular.extend(
                {autoOpen: false, title: ts('Preview / Test'), width: 550},
                options
              ));
              activeDialogs.crmMosaicoPreviewDialog = 1;
              var pr = dialogService.open('crmMosaicoPreviewDialog', '~/crmMosaico/PreviewDialogCtrl.html', model, options)
                .finally(function(){ delete activeDialogs.crmMosaicoPreviewDialog; });
              return pr;
            }
          }
        });

        return crmStatus({start: ts('Loading...'), success: null}, crmMosaicoIframe.open());
      }
    };

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

    crmMosaicoTemplates.whenLoaded().then(function(){
      $scope.mosaicoCtrl.templates = crmMosaicoTemplates.getAll();
    });

    $scope.$on("$destroy", function() {
      angular.forEach(activeDialogs, function(v,name){
        dialogService.cancel(name);
      });
      if (crmMosaicoIframe) {
        crmMosaicoIframe.destroy();
        crmMosaicoIframe = null;
      }
    });

  });

})(angular, CRM.$, CRM._);
