{*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.6                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2015                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*}
{capture assign=crmURL}{crmURL p='civicrm/admin/messageTemplates/add' q="action=add&reset=1"}{/capture}
{if $action eq 1 or $action eq 2 or $action eq 8}
   {include file="CRM/Admin/Form/MessageTemplates.tpl"}

{elseif $action eq 4}
  {* View a system default workflow template *}

  <div id="help">
    {ts}You are viewing the default message template for this system workflow.{/ts} {help id="id-view_system_default"}
  </div>

  <fieldset>
  <div class="crm-section msg_title-section">
    <div class="bold">{$form.msg_title.value}</div>
  </div>
  <div class="crm-section msg_subject-section">
  <h3 class="header-dark">{$form.msg_subject.label}</h3>
    <div class="text">
      <textarea name="msg-subject" id="msg_subject" style="height: 6em; width: 45em;">{$form.msg_subject.value}</textarea>
      <div class='spacer'></div>
      <div class="section">
        <a href='#' onclick='MessageTemplates.msg_subject.select(); return false;' class='button'><span>Select Subject</span></a>
        <div class='spacer'></div>
      </div>
    </div>
  </div>

  <div class="crm-section msg_txt-section">
  <h3 class="header-dark">{$form.msg_text.label}</h3>
    <div class="text">
      <textarea class="huge" name='msg_text' id='msg_text'>{$form.msg_text.value|htmlentities}</textarea>
      <div class='spacer'></div>
      <div class="section">
        <a href='#' onclick='MessageTemplates.msg_text.select(); return false;' class='button'><span>Select Text Message</span></a>
        <div class='spacer'></div>
      </div>
    </div>
  </div>

  <div class="crm-section msg_html-section">
  <h3 class="header-dark">{$form.msg_html.label}</h3>
    <div class='text'>
      <textarea class="huge" name='msg_html' id='msg_html'>{$form.msg_html.value|htmlentities}</textarea>
      <div class='spacer'></div>
      <div class="section">
        <a href='#' onclick='MessageTemplates.msg_html.select(); return false;' class='button'><span>Select HTML Message</span></a>
        <div class='spacer'></div>
      </div>
    </div>
  </div>

  <div class="crm-section msg_html-section">
  <h3 class="header-dark">{$form.pdf_format_id.label}</h3>
    <div class='text'>
      {$form.pdf_format_id.html}
    </div>
  </div>

  <div id="crm-submit-buttons">{$form.buttons.html}</div>
  </fieldset>
{/if}

{if $rows and $action ne 2 and $action ne 4}

{if $showTinymceTpl}
  {* include wysiwyg related files*}
  {include file="CRM/common/wysiwyg.tpl" includeWysiwygEditor=true}
{/if}

  <div id='mainTabContainer'>
    <ul>
      <li id='tab_user'>    <a href='#user'     title='{ts}User-driven Messages{/ts}'>    {ts}User-driven Messages{/ts}    </a></li>
      <li id='tab_workflow'><a href='#workflow' title='{ts}System Workflow Messages{/ts}'>{ts}System Workflow Messages{/ts}</a></li>
      <li id='tab_mosaico'> <a href='#mosaico'  title='{ts}Mosaico Messages{/ts}'>        {ts}Mosaico Messages{/ts}        </a></li>
    </ul>

    {* create two selector tabs, first being the ‘user’ one, the second being the ‘workflow’ one *}
    {include file="CRM/common/enableDisableApi.tpl"}
    {include file="CRM/common/jsortable.tpl"}
    {foreach from=$rows item=template_row key=type}
      <div id="{if $type eq 'userTemplates'}user{else}workflow{/if}" class='ui-tabs-panel ui-widget-content ui-corner-bottom'>
          <div class="help">
          {if $type eq 'userTemplates'}
            {capture assign=schedRemURL}{crmURL p='civicrm/admin/scheduleReminders' q="reset=1"}{/capture}
            {ts 1=$schedRemURL}User-driven message templates allow you to save and re-use messages with layouts. They are useful if you need to send similar emails or letters to contacts on a recurring basis. You can also use them in CiviMail mailings. Messages used for membership renewal reminders, as well as event and activity related reminders should be created via <a href="%1">Scheduled Reminders</a>.{/ts} {help id="id-intro"}
          {else}
            {ts}System workflow message templates are used to generate the emails sent to constituents and administrators for contribution receipts, event confirmations and many other workflows. You can customize the style and wording of these messages here.{/ts} {help id="id-system-workflow"}
          {/if}
          </div>
        <div>
          <p></p>
            {if !empty( $template_row) }
              <table class="display">
                <thead>
                  <tr>
                    <th class="sortable">{if $type eq 'userTemplates'}{ts}Message Title{/ts}{else}{ts}Workflow{/ts}{/if}</th>
                    {if $type eq 'userTemplates'}
                      <th>{ts}Message Subject{/ts}</th>
                      <th>{ts}Enabled?{/ts}</th>
                    {/if}
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                {foreach from=$template_row item=row}
                    <tr id="message_template-{$row.id}" class="crm-entity {$row.class}{if NOT $row.is_active} disabled{/if}">
                      <td>{$row.msg_title}</td>
                      {if $type eq 'userTemplates'}
                        <td>{$row.msg_subject}</td>
                        <td id="row_{$row.id}_status">{if $row.is_active eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
                      {/if}
                      <td>{$row.action|replace:'xx':$row.id}</td>
                    </tr>
                {/foreach}
                </tbody>
              </table>
              {/if}

            {if $action ne 1 and $action ne 2 and $type eq 'userTemplates'}
              <div class="action-link">
                {crmButton p='civicrm/admin/messageTemplates/add' q="action=add&reset=1" id="newMessageTemplates"  icon="plus-circle"}{ts}Add Message Template{/ts}{/crmButton}
              </div>
              <div class="spacer"></div>
            {/if}

            {if empty( $template_row) }
                <div class="messages status no-popup">
                    <div class="icon inform-icon"></div>&nbsp;
                    {ts 1=$crmURL}There are no User-driven Message Templates entered. You can <a href='%1'>add one</a>.{/ts}
                </div>
            {/if}
         </div>
      </div>
    {/foreach}

    <!-- MV include mosaico templates -->
    {include file="CRM/Admin/Page/MosaicoTemplates.tpl"}
    <!-- END -->
    <div id='template_dialog' style="display:none;">
      <label>Select template : </label>
      <select id="template_list" class="crm-form-select">
        <option value="versafix-1">versafix-1</option>
        <option value="tedc15">tedc15</option>
        <option value="tutorial">tutorial</option>
      </select>
    </div>
  </div>

  {include file="packages/mosaico/index.html"}

  <script type='text/javascript'>
    var selectedTab = 'user';
    {if $selectedChild}selectedTab = '{$selectedChild}';{/if}
    {literal}
      CRM.$(function($) {
        var tabIndex  = $('#tab_' + selectedTab).prevAll().length
        $("#mainTabContainer").tabs( {active: tabIndex} );

        var editorUrl = "{/literal}{crmURL p='civicrm/mosaico/editor' h=0 q='snippet=2'}{literal}";
        var rndHashKeyForEditMosaico= Math.random().toString(36).substr(2, 7);


        $('a.edit_msg_tpl_to_mosaico').click(function(){
          var msgTplId = $(this).attr('value');
          console.log(msgTplId);
          $('#template_dialog').dialog({
            modal: true,
            title: 'Mosaico Template',
            buttons: {
              Open: function() {
                var selectTemplateName = $('#template_list').val();
                console.log(selectTemplateName);
                var postUrl = {/literal}"{crmURL p='civicrm/mosaico/ajax/edit' h=0 }"{literal};
                $.ajax({ type: "POST"
                  , url: postUrl
                  , data: {id:msgTplId, hash_key:rndHashKeyForEditMosaico, template_name: selectTemplateName}
                  , async: true
                  , dataType: 'json'
                  , success: function(result) {
                    editMsgTempalteInMosaico(result, selectTemplateName);
                  },
                  error : function() {
                    CRM.alert('Could not copy mosaico template', 'Error');
                  }
                });
              },
              Cancel: function() {
                $('#template_dialog').hide();
                $(this).dialog("close");
              }
            }
          });
        });


      function editMsgTempalteInMosaico(result, selectTemplateName) {

        //generate random hash key
        var rnd = result.new_hash_key;

        // get template of original mosaico msg template & metadata
        var template = localStorage.getItem("template-" + rnd);
        if (template) {
          document.location = editorUrl+'#'+rnd;
        }
        else{
          var parseTemplate = JSON.parse(result.template);
          // var blocksValues = result.blockValues
          console.log(parseTemplate);
          switch(selectTemplateName) {
              case 'tedc15':
                var blocksValues = {
                  "type":"heroBlock"
                   ,"insertWidth":"340"
                   ,"customStyle":false
                   ,"id":"ko_heroBlock_1"
                   ,"topImage":{
                      "type":"image"
                      ,"src":""
                      ,"url":"http://sierrawild.gov/wilderness/ansel-adams"
                      ,"alt":""
                    }
                   ,"link":{"type":"link","url":"http://sierrawild.gov/wilderness/ansel-adams","text":"Plan Your Visit"}
                   ,"bottomRightImage":{"type":"image","src":"","url":"http://sierrawild.gov/wilderness/ansel-adams","alt":""}
                   ,"title":"Ansel Adams Wilderness"
                   ,"subTitle":"<p>"+result.msg_html+"<br></p>"
                  };
                break;
              case 'tutorial':
                 var blocksValues = {
                      "type":"fixedlist"
                      ,"customStyle":false
                      ,"id":"ko_fixedlist_1"
                      ,"color":null
                      ,"listsize":"1 "
                      ,"headerText":"fixed size list"
                      ,"firstBodyText":"<p>"+result.msg_html+"<br></p>"
                      ,"secondBodyText":"item 2"
                      ,"thirdBodyText":""
                    };
                  break;
              default:
                var blocksValues = {
                    "type":"textBlock"
                    ,"customStyle":false
                    ,"backgroundColor":null
                    ,"longTextStyle":{"type":"longTextStyle","face":null,"color":null,"size":null,"linksColor":null}
                    ,"longText":"<p>"+result.msg_html+"<br></p>"
                    ,"id":"ko_textBlock_1"
                    ,"externalBackgroundColor":null
                  };
          }


          parseTemplate.mainBlocks.blocks.push(blocksValues);
          // // Save metadata, template and name details in local storage.
          localStorage.setItem("edit_msg_tpl_id-" + rnd, result.msg_tpl_id);
          localStorage.setItem("name-" + rnd, result.name);
          localStorage.setItem("metadata-" + rnd, result.metadata);
          localStorage.setItem("template-" + rnd, JSON.stringify(parseTemplate));

          //Update DB with new Template.
          // Post new meta data , new hash key to update in civicrm_mosaico_msg_template table
          var postUrl = {/literal}"{crmURL p='civicrm/mosaico/ajax/settemplate' h=0 }"{literal};
          console.log(result.id)
          $.ajax({ type: "POST", url: postUrl, data: {template:JSON.stringify(parseTemplate), id:result.id}, async: true, dataType: 'json',
            success: function(updateResult) {
              console.log(updateResult);
              //redirect to editor
              document.location = editorUrl+'#'+rnd;
            },
            error : function() {
              CRM.alert('Could not update meta data for newly created mosaico msg template', 'Error');
            }
          });

        }
      }


      });
    {/literal}
  </script>

{elseif $action ne 1 and $action ne 2 and $action ne 4 and $action ne 8}
  <div class="messages status no-popup">
      <img src="{$config->resourceBase}i/Inform.gif" alt="{ts}status{/ts}"/>
      {ts 1=$crmURL}There are no Message Templates entered. You can <a href='%1'>add one</a>.{/ts}
  </div>
{/if}
