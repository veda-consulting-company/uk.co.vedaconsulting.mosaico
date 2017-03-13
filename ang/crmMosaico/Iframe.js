(function(angular, $, _) {

  /**
   * The class CrmMosaicoIframe allows you to instantiate and manage a
   * full-screen IFRAME with embedded Mosaico runtime.
   */
  angular.module('crmMosaico').factory('CrmMosaicoIframe', function(crmUiAlert, $q, $timeout, $rootScope) {

    /**
     * @param Object newOptions
     *  - topMargin: int (optional)
     *  - url: string (optional)
     *  - actions: Object
     *    - save: function(ko, viewModel)
     *    - test: function(ko, viewModel)
     *    - close: function(ko, viewModel) (an alias for "save")
     *  - model: Object. Must have "metadata+content" or "template"
     *    - metadata: string, JSON-encoded
     *    - content: string, JSON-encoded
     *    - template: string, relative file path
     */
    return function CrmMosaicoIframe(options){
      var cfg = {
        url: CRM.url('civicrm/mosaico/iframe', 'snippet=1'),
        zIndex: 1000,
        topMargin: $('#civicrm-menu').length > 0 ? $('#civicrm-menu').height() : 27
      };
      angular.extend(cfg, options);

      var model = cfg.model, actions = cfg.actions;
      var isVisible = false, $iframe = null, iframe = null;

      if (actions.save && actions.close) {
        throw "Error: Save and Close actions are mutually exclusive";
      }

      function onResize() {
        if ($iframe) $iframe.height($(window).height() - cfg.topMargin);
      }

      this.render = function render() {
        var height = $(window).height() - cfg.topMargin;
        $iframe = $('<iframe frameborder="0" width="100%">');
        $iframe.css({'z-index': cfg.zIndex, position: 'fixed', left:0, top: cfg.topMargin, width: '100%', height: height + 'px'});
        // 'z-index': 100000000
        iframe = $iframe[0];
        iframe.setAttribute('src', cfg.url);
        $('body').append($iframe);
        $(window).on('resize', onResize);
        return this;
      };

      // @return Promise<null>
      this.open = function open() {
        var dfr = $q.defer();

        if ($iframe) {
          dfr.resolve();
          return dfr.promise;
        }

        window.top.crmMosaicoIframe = function(newWindow, Mosaico, config, plugins) {
          plugins.push(function(viewModel) {
            mosaicoPlugin(newWindow.ko, viewModel);
          });

          var ok = true;
          if (model.content) {
            Mosaico.start(config, undefined, JSON.parse(model.metadata), JSON.parse(model.content), plugins);
          } else if (model.template) {
            Mosaico.start(config, model.template, undefined, undefined, plugins);
          } else {
            crmUiAlert({text: 'Cannot edit mailing'});
            ok = false;
          }

          $timeout(function(){
            if (ok) dfr.resolve();
            else dfr.reject();
          }, 150);
        };

        this.render();
        return dfr.promise;
      };

      this.hide = function hide() {
        isVisible = false;
        if ($iframe) $iframe.hide();
        return this;
      };

      this.show = function show() {
        isVisible = true;
        if ($iframe) $iframe.show();
        return this;
      };

      this.destroy = function destroy() {
        this.hide();
        $(window).off('resize', onResize);
        if ($iframe) $iframe.remove();
        iframe = $iframe = null;
        return this;
      };

      // See https://github.com/voidlabs/mosaico/wiki/Mosaico-Plugins
      // Generally: Implement the in-dialog "Save" and "Test" buttons.
      function mosaicoPlugin(ko, viewModel) {
        // Clicking the default link isn't very useful in IFRAME context.
        viewModel.logoUrl = null;

        function mkCmd(name, callback) {
          var cmd = {
            name: name, // l10n happens in the template
            enabled: ko.observable(true)
          };
          cmd.execute = function() {
            cmd.enabled(false);
            $rootScope.$apply(function(){
              callback(ko, viewModel);
            });
            cmd.enabled(true);
          };
          return cmd;
        }

        if (actions.save) {
          viewModel.save = mkCmd("Save", actions.save);
        }
        if (actions.close) { // pretend like Mosaico has a "Close" action.
          viewModel.save = mkCmd("Close", actions.close);
        }
        if (actions.test) {
          viewModel.test = mkCmd("Test", actions.test);
        }
      }

    };
  });


})(angular, CRM.$, CRM._);