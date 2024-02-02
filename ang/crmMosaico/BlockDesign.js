(function(angular, $, _) {
  angular.module('crmMosaico').directive('crmMosaicoBlockDesign', function($q, crmUiHelp, dialogService, crmMosaicoTemplates, crmStatus, CrmMosaicoIframe, $timeout) {

    return {
      scope: {
        // crmMosaicoCtrl: '@',
        crmMailing: '@',
        crmMailingAttachments: '@',
      },
      templateUrl: '~/crmMosaico/BlockDesign.html',
      link: function (scope, elm, attr) {
        scope.$parent.$watch(attr.crmMailing, function(newValue){
          scope.mailing = newValue;
        });
        scope.$parent.$watch(attr.crmMailingAttachments, function(newValue){
          scope.attachments = newValue;
        });
        scope.crmMailingConst = CRM.crmMailing;
        scope.ts = CRM.ts(null);
        scope.hs = crmUiHelp({file: 'CRM/Mailing/MailingUI'});

        const $scope = scope;
        var crmMosaicoIframe = null, activeDialogs = {};
        $scope.mosaicoCtrl = {
          templates: [],
          // Fill a given "mailing" which the chosen "template".
          select: function(template) {
            const mailing = scope.mailing;
            var topt = mailing.template_options = mailing.template_options || {};
            var promise = crmMosaicoTemplates.getFull(template).then(function(tplCtnt){
              topt.mosaicoTemplate = template.id;
              topt.mosaicoMetadata = tplCtnt.metadata;
              topt.mosaicoContent = tplCtnt.content;
              mailing.body_html = tplCtnt.html;
              // console.log('select', {isAr1: _.isArray(mailing.template_options), isAr2: _.isArray(topt), m: mailing, t: template});
              $scope.mosaicoCtrl.edit();
            });
            return crmStatus({start: ts('Loading...'), success: null}, promise);
          },
          hasSelection: function() {
            const mailing = scope.mailing;
            return !!mailing.template_options.mosaicoTemplate;
          },
          hasMarkup: function() {
            const mailing = scope.mailing;
            return !!mailing.body_html;
          },
          // Figure out which "template" was previously used with a "mailing."
          getTemplate: function() {
            const mailing = scope.mailing;
            if (!mailing || !mailing.template_options || !mailing.template_options.mosaicoTemplate) {
              return null;
            }
            var matches = _.where($scope.mosaicoCtrl.templates, {
              id: mailing.template_options.mosaicoTemplate
            });
            return matches.length > 0 ? matches[0] : null;
          },
          // Reset all Mosaico data in a "mailing'.
          reset: function() {
            const mailing = scope.mailing;
            if (crmMosaicoIframe) crmMosaicoIframe.destroy();
            crmMosaicoIframe = null;
            delete mailing.template_options.mosaicoTemplate;
            delete mailing.template_options.mosaicoMetadata;
            delete mailing.template_options.mosaicoContent;
            mailing.body_html = '';
          },
          // Edit a mailing in Mosaico.
          edit: function() {
            const mailing = scope.mailing;
            if (crmMosaicoIframe) {
              crmMosaicoIframe.show();
              return;
            }

            function syncModel(viewModel) {
              mailing.body_html = viewModel.exportHTML();
              mailing.template_options = mailing.template_options || {};
              // Mosaico exports JSON. Keep their original encoding... or else the loader throws an error.
              mailing.template_options.mosaicoMetadata = viewModel.exportMetadata();
              mailing.template_options.mosaicoContent = viewModel.exportJSON();
            }

            crmMosaicoIframe = new CrmMosaicoIframe({
              model: {
                template: $scope.mosaicoCtrl.getTemplate().path,
                metadata: mailing.template_options.mosaicoMetadata,
                content: mailing.template_options.mosaicoContent
              },
              actions: {
                sync: function(ko, viewModel) {
                  syncModel(viewModel);
                },
                close: function(ko, viewModel) {
                  viewModel.metadata.changed = Date.now();
                  syncModel(viewModel);
                  // TODO: When autosave is better integrated, remove this.
                  $timeout(function(){
                    $scope.$parent.$apply(attr.onSave);
                  }, 100);
                  crmMosaicoIframe.hide('crmMosaicoEditorDialog');
                },
                test: function(ko, viewModel) {
                  syncModel(viewModel);

                  var model = {mailing: $scope.mailing, attachments: $scope.attachments};
                  console.log('test!', model);
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

        crmMosaicoTemplates.whenLoaded().then(function(){
          $scope.mosaicoCtrl.templates = crmMosaicoTemplates.getAll();
          $scope.mosaicoCtrl.categoryFilters = _.transform(crmMosaicoTemplates.getCategories(), function(filters, category) {
            filters.push({id: filters.length, text: category.label, filter: {category_id: category.value}});
          }, [{id: 0, text: ts('Base Template'), filter: {isBase: true}}]);
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


      }
    };
  });
})(angular, CRM.$, CRM._);
