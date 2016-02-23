(function(angular, $, _) {

  // Controller for the in-place msg-template management
  angular.module('crmMailing').controller('MsgTemplateCtrl', function MsgTemplateCtrl($route, $scope, crmMsgTemplates, dialogService, crmApi) {
    var ts = $scope.ts = CRM.ts(null);
    $scope.crmMsgTemplates = crmMsgTemplates;
    $scope.checkPerm = CRM.checkPerm;
    // @return Promise MessageTemplate (per APIv3)
    $scope.saveTemplate = function saveTemplate(mailing) {
      var model = {
        selected_id: mailing.msg_template_id,
        tpl: {
          msg_title: '',
          msg_subject: mailing.subject,
          msg_text: mailing.body_text,
          msg_html: mailing.body_html
        }
      };
      var options = CRM.utils.adjustDialogDefaults({
        autoOpen: false,
        height: 'auto',
        width: '40%',
        title: ts('Save Template')
      });
      return dialogService.open('saveTemplateDialog', '~/crmMailing/SaveMsgTemplateDialogCtrl.html', model, options)
        .then(function(item) {
          mailing.msg_template_id = item.id;
          return item;
        });
    };

    // mosaicoTemplateLoad(67);
    // @param int id
    // @return Promise
    $scope.loadTemplate = function loadTemplate(mailing, id) {
      mosaicoTemplateLoad(id);
      return crmMsgTemplates.get(id).then(function(tpl) {
        mailing.subject = tpl.msg_subject;
        mailing.body_text = tpl.msg_text;
        mailing.body_html = tpl.msg_html;
      });
    };
    
    function mosaicoTemplateLoad(id) {
      //get mosaico Ids
      crmApi('Mosaico', 'gettemplateid', {'sequential': '1'}).then(
      function (data) { // success
        $mosaicoIds = data.values;
        if ($mosaicoIds.indexOf(id) != -1 ) {
          $('#crmUiId_1').parents('.crm-accordion-body').parents('.crm-accordion-wrapper').parent().hide();
          $('#crmUiId_2').parents('.crm-accordion-body').parents('.crm-accordion-wrapper').parent().hide();
        }else{
          $('#crmUiId_1').parents('.crm-accordion-body').parents('.crm-accordion-wrapper').parent().show();
          $('#crmUiId_2').parents('.crm-accordion-body').parents('.crm-accordion-wrapper').parent().show();
        }
      });
    };
    
  mosaicoTemplateLoad($route.current.scope.mailing.msg_template_id);
  });

})(angular, CRM.$, CRM._);
