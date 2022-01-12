(function(angular, $, _) {
  angular.module('crmMosaico').component('crmMosaicoSearchTemplateListButtons', {
    bindings: {
      tpl: '<',
      refresh: '&'
    },
    templateUrl: '~/crmMosaico/SearchTemplateListButtons.html',
    controller: function($scope, crmApi4, crmStatus, crmMosaicoTemplates, CrmMosaicoIframe, crmBlocker) {
      var ts = $scope.ts = CRM.ts('mosaico'),
        ctrl = this;
      var block = $scope.block = crmBlocker();

      // FIXME: Lots of duplication between this and CrmMosaicoListCtrl.
      // More code could be moved to the shared crmMosaicoTemplates service.

      // Track zero or one instances of CrmMosaicoIframe.
      var crmMosaicoIframe, warmTplId;
      this.$onDestroy = function() {
        if (crmMosaicoIframe) {
          crmMosaicoIframe.destroy();
          crmMosaicoIframe = null;
          warmTplId = null;
        }
      };

      this.copyTpl = function(tpl) {
        crmStatus(
          {start: ts('Copying...'), success: ts('Copied')},
          crmMosaicoTemplates.clone({id: tpl.id, title: tpl.title + ' ' + ts('(copy)')})
        )
        .then(function(newTpl) {
          return ctrl.editTpl(newTpl);
        });
      };

      this.deleteTpl = function(tpl) {
        return crmStatus(
          {start: ts('Deleting...'), success: ts('Deleted')},
          crmMosaicoTemplates.delete(tpl)
        ).then(ctrl.refresh);
      };

      this.editTpl = function(tpl) {
        if (block.check()) {
          return;
        }

        if (warmTplId === tpl.id) {
          crmMosaicoIframe.show();
          return;
        }

        warmTplId = tpl.id;
        var openPromise = crmMosaicoTemplates.getFull(tpl).then(function(fullTpl) {
          if (crmMosaicoIframe) crmMosaicoIframe.destroy();
          // FIXME: baseDetails seems like redundant data
          var base = tpl.baseDetails || crmMosaicoTemplates.getBase(tpl.base);
          crmMosaicoIframe = new CrmMosaicoIframe({
            model: {
              template: base.path,
              metadata: fullTpl.metadata,
              content: fullTpl.content
            },
            actions: {
              sync: function(ko, viewModel) {
                var savePromise = crmMosaicoTemplates.save(tpl.id, viewModel);
                crmStatus({start: ts('Saving'), success: ts('Saved')}, savePromise);
              },
              save: function(ko, viewModel) {
                var savePromise = crmMosaicoTemplates.save(tpl.id, viewModel).then(function() {
                  crmMosaicoIframe.hide();
                });
                ctrl.refresh();
                crmStatus({start: ts('Saving'), success: ts('Saved')}, savePromise);
              }
            }
          });
          return crmMosaicoIframe.open();
        });

        block(crmStatus({start: ts('Loading...'), success: null}, openPromise));
      };

    }
  });
})(angular, CRM.$, CRM._);
