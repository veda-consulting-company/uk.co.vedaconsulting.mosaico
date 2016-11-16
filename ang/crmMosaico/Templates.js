(function (angular, $, _) {

  angular.module('crmMosaico').factory('crmMosaicoTemplates', function ($q, crmApi) {
    var ts = CRM.ts(null);
    var tplsUrl = CRM.resourceUrls['uk.co.vedaconsulting.mosaico'] + '/packages/mosaico/templates';

    var baseTemplates = [
      {id: 'base-1', title: ts('Empty Template'), type: 'Versafix 1', thumbnail: tplsUrl + '/versafix-1/edres/_full.png', path: 'templates/versafix-1/template-versafix-1.html'},
      {id: 'base-6', title: ts('Empty Template'), type: 'TEDC 15', thumbnail: tplsUrl + '/tedc15/edres/_full.png', path: 'templates/tedc15/template-tedc15.html'}
    ];

    var exMetadata = '{"created":1480972208311,"key":"8m0qymc","name":"My Example Template","template":"/sites/all/modules/civicrm/ext/uk.co.vedaconsulting.mosaico/packages/mosaico/templates/versafix-1/template-versafix-1.html","editorversion":"0.15.0","templateversion":"1.0.5","changed":1480972251321}';
    var exContent = '{"type":"template","customStyle":false,"preheaderVisible":true,"titleText":"TITLE","preheaderBlock":{"type":"preheaderBlock","customStyle":false,"id":"ko_preheaderBlock_1","backgroundColor":null,"preheaderText":"","linkStyle":{"type":"linkStyle","face":null,"color":null,"size":null,"decoration":null},"preheaderLinkOption":"{action.unsubscribeUrl}","longTextStyle":{"type":"longTextStyle","face":null,"color":null,"size":null,"linksColor":null},"unsubscribeText":"Unsubscribe","webversionText":"View in your browser"},"mainBlocks":{"type":"blocks","blocks":[{"type":"sideArticleBlock","customStyle":false,"backgroundColor":null,"titleVisible":true,"buttonVisible":true,"imageWidth":"166","imagePos":"left","titleTextStyle":{"type":"textStyle","face":null,"color":null,"size":null},"longTextStyle":{"type":"longTextStyle","face":null,"color":null,"size":null,"linksColor":null},"buttonStyle":{"type":"buttonStyle","face":null,"color":null,"size":null,"buttonColor":null,"radius":null},"image":{"type":"image","src":"","url":"","alt":""},"longText":"<p>This is definitely an example template.<br></p>","buttonLink":{"type":"link","text":"BUTTON","url":""},"id":"ko_sideArticleBlock_2","externalBackgroundColor":null,"titleText":"Mosaico Example Template"},{"type":"buttonBlock","customStyle":false,"id":"ko_buttonBlock_3","externalBackgroundColor":null,"backgroundColor":null,"bigButtonStyle":{"type":"buttonStyle","face":null,"color":null,"size":null,"buttonColor":null,"radius":null},"link":{"type":"link","text":"Make cheese","url":""}}]},"theme":{"type":"theme","frameTheme":{"type":"frameTheme","backgroundColor":"#3f3f3f","longTextStyle":{"type":"longTextStyle","face":"Arial, Helvetica, sans-serif","color":"#919191","size":"13","linksColor":"#cccccc"},"linkStyle":{"type":"linkStyle","face":"Arial, Helvetica, sans-serif","color":"#ffffff","size":"13","decoration":"underline"}},"contentTheme":{"type":"contentTheme","longTextStyle":{"type":"longTextStyle","face":"Arial, Helvetica, sans-serif","color":"#3f3f3f","size":"13","linksColor":"#3f3f3f"},"externalBackgroundColor":"#bfbfbf","externalTextStyle":{"type":"textStyle","face":"Arial, Helvetica, sans-serif","color":"#f3f3f3","size":"18"},"backgroundColor":"#ffffff","titleTextStyle":{"type":"textStyle","face":"Arial, Helvetica, sans-serif","color":"#3f3f3f","size":"18"},"buttonStyle":{"type":"buttonStyle","face":"Arial, Helvetica, sans-serif","color":"#3f3f3f","size":"13","buttonColor":"#bfbfbf","radius":"4"},"bigTitleStyle":{"type":"bigTitleStyle","face":"Arial, Helvetica, sans-serif","color":"#3f3f3f","size":"22","align":"center"},"hrStyle":{"type":"hrStyle","color":"#3f3f3f","hrWidth":"100","hrHeight":"1"},"bigButtonStyle":{"type":"buttonStyle","face":"Arial, Helvetica, sans-serif","color":"#3f3f3f","size":"22","buttonColor":"#bfbfbf","radius":"4"}}}}';

    var configuredTemplates = [
      {id: '1', title: 'Event Promo', type: 'Versafix 1', thumbnail: tplsUrl + '/versafix-1/edres/_full.png', path: 'templates/versafix-1/template-versafix-1.html', hasContent: true},
      {id: '2', title: 'Donor Newsletter', type: 'Versafix 1', thumbnail: tplsUrl + '/versafix-1/edres/_full.png', path: 'templates/versafix-1/template-versafix-1.html', hasContent: true},
      {id: '3', title: 'Member Newsletter', type: 'Versafix 1', thumbnail: tplsUrl + '/versafix-1/edres/_full.png', path: 'templates/versafix-1/template-versafix-1.html', hasContent: true},
      {id: '4', title: 'Special Offer', type: 'TEDC 15', thumbnail: tplsUrl + '/tedc15/edres/_full.png', path: 'templates/tedc15/template-tedc15.html', hasContent: true}
    ];

    var allTemplates = [];
    _.each(baseTemplates, function(t){ allTemplates.push(t); });
    _.each(configuredTemplates, function(t){ allTemplates.push(t); });

    return {
      getContent: function getContent(template) {
        //   return crmApi('MosaicoTemplate', 'get', {id: template.id});
        return $q(function(resolve, reject){
          setTimeout(function(){
            if (!template.hasContent) {
              resolve({});
            } else {
              resolve({
                metadata: exMetadata,
                content: exContent
              });
            }
          }, 100);
        });
      },
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
