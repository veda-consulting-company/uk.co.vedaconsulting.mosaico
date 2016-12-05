(function (angular, $, _) {

  angular.module('crmMosaico').factory('crmMosaicoTemplates', function ($q, crmApi, $timeout) {
    var ts = CRM.ts(null);
    var cache = {};

    function filterBase(base) {
      return {
        id: 'base-' + base.name,
        title: ts('Empty Template'),
        type: base.title,
        thumbnail: base.thumbnail,
        path: base.path,
        isBase: true
      };
    }
    function filterTemplate(tpl) {
      var base = cache.basesByName[tpl.base];
      return {
        id: tpl.id,
        title: tpl.title,
        type: base.title,
        thumbnail: base.thumbnail,
        path: base.path,
        isBase: false
      };
    }

    crmApi({
      bases: ['MosaicoBaseTemplate', 'get', {sequential: 1, limit: 0}],
      templates: ['MosaicoTemplate', 'get', {sequential: 1, limit: 0, return:['title', 'base']}]
    }).then(function(r){
      cache.basesByName = _.indexBy(r.bases.values, 'name');
      cache.bases = _.map(r.bases.values, filterBase);
      cache.configured = _.map(r.templates.values, filterTemplate);
      cache.all = _.union(cache.bases, cache.configured);
    });

    return {
      // Return Promise<void>
      whenLoaded: function whenLoaded() {
        return $q(function(resolve) {
          var poll = function() {
            if (cache.all !== undefined) resolve();
            else $timeout(poll, 100);
          };
          poll();
        });
      },
      // Load the full content of a template (HTML, metadata, content -- as applicable).
      getFull: function getFull(template) {
        if (!template.isBase) {
          return crmApi('MosaicoTemplate', 'getsingle', {id: template.id});
        }
        return $q(function(resolve){
          $timeout(function(){resolve({});}, 100);
        });
      },
      getBases: function(){ return cache.bases; },
      getConfigured: function(){ return cache.configured; },
      getAll: function(){ return cache.all; }
    };
  });

})(angular, CRM.$, CRM._);
