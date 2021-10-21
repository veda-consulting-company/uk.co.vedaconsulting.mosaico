(function(angular, $, _) {

  // The STALE_FLAG is added to `body_html` when it is out-of-sync with the `template_options.mosaicoContent`.
  // Why does this exist? We want to autosave while the user interacts with the editor.
  // However, rendering HTML while the user interacts is problematic (eg requires ~200ms on i3-10100/Firefox v93;
  // eg provokes JS warnings). So we save just `mosaicoContent` (JSON) and allow the `body_html` to grow stale.
  var STALE_FLAG = '<!--STALE-->';

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

  angular.module('crmMosaico').controller('CrmMosaicoListCtrl', function($scope, crmApi, crmStatus, crmUiHelp, crmMosaicoTemplates, CrmMosaicoIframe, crmBlocker, crmMosaicoPrompt) {
    // The ts() and hs() functions help load strings for this module.
    var ts = $scope.ts = CRM.ts('mosaico');
    var hs = $scope.hs = crmUiHelp({file: 'CRM/crmMosaico/ListCtrl'}); // See: templates/CRM/crmMosaico/ListCtrl.hlp
    var block = $scope.block = crmBlocker();
    $scope.crmMosaicoTemplates = crmMosaicoTemplates;

    // Track zero or one instances of CrmMosaicoIframe.
    var crmMosaicoIframe, warmTplId;
    $scope.$on("$destroy", function() {
      if (crmMosaicoIframe) {
        crmMosaicoIframe.destroy();
        crmMosaicoIframe = null;
        warmTplId = null;
      }
    });

    $scope.createTpl = function createTpl(tpl) {
      return crmMosaicoPrompt(ts('Create new template'), ts('New Template (%1)', {1: tpl.type}))
        .then(function(newTitle) {
          return crmStatus(
            {start: ts('Creating...'), success: ts('Created')},
            crmMosaicoTemplates.create({base: tpl.baseDetails.name, title: newTitle})
          );
        })
        .then(function(newTpl) {
          return $scope.editTpl(newTpl);
        });
    };

    $scope.copyTpl = function copyTpl(tpl) {
      return crmMosaicoPrompt(ts('Create new template'), ts('Copy of %1', {1: tpl.title}))
        .then(function(newTitle) {
          return crmStatus(
            {start: ts('Copying...'), success: ts('Copied')},
            crmMosaicoTemplates.clone({id: tpl.id, title: newTitle})
          );
        })
        .then(function(newTpl) {
          return $scope.editTpl(newTpl);
        });
    };

    $scope.renameTpl = function renameTpl(tpl) {
      crmMosaicoPrompt(ts('Edit template name'), tpl.title)
        .then(function(newTitle) {
          return crmStatus(
            {start: ts('Saving...'), success: ts('Saved')},
            crmApi('MosaicoTemplate', 'create', {id: tpl.id, title: newTitle}));
        })
        .then(function(r){
          tpl.title = r.values[tpl.id].title;
        });
    };

    $scope.editTpl = function editTpl(tpl) {
      if (block.check()) {
        return;
      }

      if (warmTplId == tpl.id) {
        crmMosaicoIframe.show();
        return;
      }

      warmTplId = tpl.id;
      var openPromise = crmMosaicoTemplates.getFull(tpl).then(function(fullTpl) {
        if (crmMosaicoIframe) crmMosaicoIframe.destroy();
        var lastHtml = fullTpl.html;

        crmMosaicoIframe = new CrmMosaicoIframe({
          syncInterval: 30*1000, // Moderately infrequent. AJAX save operation fires immediately.
          model: {
            template: tpl.baseDetails.path,
            metadata: fullTpl.metadata,
            content: fullTpl.content
          },
          actions: {
            sync: function(ko, viewModel, iframeState) {
              if (!iframeState.isVisible) return;

              viewModel.metadata.changed = Date.now();

              var savePromise = crmApi('MosaicoTemplate', 'create', {
                id: tpl.id,
                html: STALE_FLAG + lastHtml,
                metadata: viewModel.exportMetadata(),
                content: viewModel.exportJSON()
              });
              crmStatus({start: ts('Saving draft'), success: ts('Saved draft')}, savePromise);
            },
            save: function(ko, viewModel) {
              viewModel.metadata.changed = Date.now();

              var savePromise = crmApi('MosaicoTemplate', 'create', {
                id: tpl.id,
                html: lastHtml = viewModel.exportHTML(),
                metadata: viewModel.exportMetadata(),
                content: viewModel.exportJSON()
              }).then(function() {
                crmMosaicoIframe.hide();
              });

              crmStatus({start: ts('Saving'), success: ts('Saved')}, savePromise);
            }
          }
        });
        return crmMosaicoIframe.open();
      });

      block(crmStatus({start: ts('Loading...'), success: null}, openPromise));
    };

    $scope.deleteTpl = function(tpl) {
      return crmStatus(
        {start: ts('Deleting...'), success: ts('Deleted')},
        crmMosaicoTemplates.delete(tpl)
      );
    };

    $scope.canDelete = function() {
      return CRM.crmMosaico.canDelete;
    };

  });

})(angular, CRM.$, CRM._);
