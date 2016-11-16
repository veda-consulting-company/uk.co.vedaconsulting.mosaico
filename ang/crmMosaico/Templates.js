(function (angular, $, _) {

  angular.module('crmMosaico').factory('crmMosaicoTemplates', function ($q, crmApi) {
    var ts = CRM.ts(null);
    var tplsUrl = CRM.resourceUrls['uk.co.vedaconsulting.mosaico'] + '/packages/mosaico/templates';

    var baseTemplates = [
      {id: 'base-1', title: ts('Empty Template'), type: 'Versafix 1', thumbnail: tplsUrl + '/versafix-1/edres/_full.png', path: 'templates/versafix-1/template-versafix-1.html'},
      {id: 'base-6', title: ts('Empty Template'), type: 'TEDC 15', thumbnail: tplsUrl + '/tedc15/edres/_full.png', path: 'templates/tedc15/template-tedc15.html'}
    ];

    var configuredTemplates = [
      {id: '1', title: 'Event Promo', type: 'Versafix 1', thumbnail: tplsUrl + '/versafix-1/edres/_full.png', path: 'templates/versafix-1/template-versafix-1.html'},
      {id: '2', title: 'Donor Newsletter', type: 'Versafix 1', thumbnail: tplsUrl + '/versafix-1/edres/_full.png', path: 'templates/versafix-1/template-versafix-1.html'},
      {id: '3', title: 'Member Newsletter', type: 'Versafix 1', thumbnail: tplsUrl + '/versafix-1/edres/_full.png', path: 'templates/versafix-1/template-versafix-1.html'},
      {id: '4', title: 'Special Offer', type: 'TEDC 15', thumbnail: tplsUrl + '/tedc15/edres/_full.png', path: 'templates/tedc15/template-tedc15.html'}
    ];

    var allTemplates = _.extend([], baseTemplates, configuredTemplates);

    return {
      getBases: function getBases() {
        return $q(function(resolve, reject){
          setTimeout(function(){
            resolve(baseTemplates);
          }, 100);
        });
      },
      getConfigured: function getBases() {
        return $q(function(resolve, reject){
          setTimeout(function(){
            resolve(configuredTemplates);
          }, 100);
        });
      },
      getAll: function getAll() {
        return $q(function(resolve, reject){
          setTimeout(function(){
            resolve(allTemplates);
          }, 100);
        });
      }
    };
  });

})(angular, CRM.$, CRM._);
