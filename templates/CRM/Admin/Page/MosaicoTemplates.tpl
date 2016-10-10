<div id="mosaicoTemplates" class='ui-tabs-panel ui-widget-content ui-corner-bottom' style="display:none;">
    <div>
      <p></p>
        {if !empty( $mosaicoTemplates ) }
          <table class="display">
            <thead>
              <tr>
                <th class="sortable">{ts}Message Title{/ts}</th>
                <th>{ts}Message Subject{/ts}</th>
                <th>{ts}Enabled?{/ts}</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
            {foreach from=$mosaicoTemplates item=row}
                <tr id="message_template-{$row.msg_tpl_id}" class="crm-entity {$row.class}{if NOT $row.is_active} disabled{/if}">
                  <td>{$row.msg_title}</td>
                  <td>{$row.msg_subject}</td>
                  <td id="row_{$row.id}_status">{if $row.is_active eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
                  <td>{$row.action|replace:'xx':$row.id}</td>
                </tr>
            {/foreach}
            </tbody>
          </table>
          {/if}

          <div class="spacer"></div>
          <div class="action-link">
            {crmButton p='civicrm/mosaico/index' q="reset=1" id="newMessageTemplates"  icon="plus-circle"}{ts}Add Mosaico Template{/ts}{/crmButton}
          </div>
          <div class="spacer"></div>

        {if empty( $mosaicoTemplates) }
            <div class="messages status no-popup">
                <div class="icon inform-icon"></div>&nbsp;
                {ts 1=$crmURL}There are no User-driven Message Templates entered. You can <a href='%1'>add one</a>.{/ts}
            </div>
        {/if}
     </div>
</div>

<!-- MV:Drop down list of available mosaico templates, which use to display in dialog -->
<div id='template_dialog' style="display:none;">
  <div class="messages status no-popup">
    <div class="icon inform-icon"> </div>
    <p>WARNING: This is experimental feature. Please note that the content may not appear exactly in same layout. However using the builder, by adding new blocks, you can turn it into a layout you desire. This feature is intended to import your old message templates in builder, so you can re-use and create new mosaico templates.</p>
    <p>If you really want to make most of mosaico builder, its recommended to create a new one from "Mosaico Message" tab.</p>
    <p>Note that the feature doesn't modify your existing message template. Pressing SAVE in template builder will create new message template. If you intend to create a copy of an existing mosaico template, use the new copy feature instead, available in "Mosaico Messages" tab.</p>
  </div>
  <div class="clear"></div>
  <br>
  <label>Select template : </label>
  <select id="template_list" class="crm-form-select">
    <option value="versafix-1">versafix-1</option>
    <option value="tedc15">tedc15</option>
    <option value="tutorial">tutorial</option>
  </select>
  <br>
  <br>
</div>


<script type='text/javascript'>
  {literal}
    CRM.$(function($) {
      {/literal}{if $selectedChild}$('#mosaicoTemplates').show();{/if}{literal}
      //MV: to display mosaicoTemplates in tab
      $('#mainTabContainer li').click( function(){
        if($(this).attr('id') == 'tab_mosaico'){
          $('#mosaicoTemplates').show();
        }else{
          $('#mosaicoTemplates').hide();
        }
      });
      // Fill the localstaorage for mosaico builder to work. This way data infact is being loaded from database
      // even if it may not seem so, for the mosaico builder.
      // FIXME: do it only for template whose edit is clicked.
      var postUrl = {/literal}"{crmURL p='civicrm/mosaico/ajax/getallmd' h=0 }"{literal};
      console.log('posturl=' + postUrl);
      $.ajax({ type: "POST", url: postUrl, data: {}, async: true, dataType: 'json',
        success: function(result) {
          console.log(result);
          $.each(result, function(key, mtpl) {
            if (mtpl.id) {
              localStorage.setItem("metadata-" + mtpl.hash_key, mtpl.metadata);
              localStorage.setItem("template-" + mtpl.hash_key, mtpl.template);
              localStorage.setItem("name-"     + mtpl.hash_key, mtpl.name);
            }
          });
        }
      });
      //Copy mosaico template
      // step1: create new civicrm msg template using msg template id which is being copied
      // step2: crate new civicrm mosaico msg template using created new civicrm msg tpl id in step 1 + civicrm mosaico msg template fields(metadata, template, etc) which are linked to copying msg temple
      // step3: generate new hash key and new metadata using the data which is availabe in newly created civicrm mosaico msg template in step 2
      // step4: save name, metadata and template in local storage
      // step5: update metadata, hash key for newly created civicrm mosaico template in step 2
      $('.copy-template').click(function(event) {
      	var msgTplId = $(this).attr('value');
      	var postUrl = {/literal}"{crmURL p='civicrm/mosaico/ajax/copy' h=0 }"{literal};
      	$.ajax({ type: "POST", url: postUrl, data: {id:msgTplId}, async: true, dataType: 'json',
      	  success: function(result) {
      	    //create mos template and update meta data in civicrm_mosaico_msg_template table
            if (result.newMosaicoTplId) {
              createMetaData(result);
            } else {
              CRM.alert('Something went wrong on coping', 'Error');
            }
      	  },
      	  error : function() {
      	    CRM.alert('Could not copy mosaico template', 'Error');
      	  }
      	});
      });

      //MV: Open in mosaico editor link
      //Provide link to open a Civi Message template in Mosaico Editors
      //amended $row action link in pageRun hook itself.
      var editorUrl = "{/literal}{crmURL p='civicrm/mosaico/editor' h=0 q='snippet=2'}{literal}";

      //Random Unique hash key for mosaico template.
      var rndHashKeyForEditMosaico= Math.random().toString(36).substr(2, 7);

      //allow user to click 'Open in Mosaico editor', Show dialog with list of available template
      $('a.edit_msg_tpl_to_mosaico').click(function(){
        var msgTplId = $(this).attr('value');
        console.log(msgTplId);
        $('#template_dialog').dialog({
          modal: true,
          title: 'Import in Mosaico',
          buttons: {
            Open: function() {
              var selectTemplateName = $('#template_list').val();
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

        //we dont want generate hash key over and over again, so we reuse the hash key from result variables.
        var rnd = result.new_hash_key;

        //if we edit existing template, then we dont want to wait for dummy template. redirect straight away to Mosaico template,
        //otherwise, we create dummy template and metadata with required values and redirect with new hash key.
        var template = localStorage.getItem("template-" + rnd);
        if (template) {
          document.location = editorUrl+'#'+rnd;
        }
        else{
          var parseTemplate = JSON.parse(result.template);
          //every template have different block structure and block ids.
          switch(selectTemplateName) {
              case 'tedc15':
                var blocksValues = {
                  "type":"footerBlock"
                   ,"customStyle":false
                   ,"id":"ko_footerBlock_1"
                   ,"footerText":"<p>"+result.msg_html+"<br></p>"
                  };
                break;
              case 'tutorial':
                 var blocksValues = {
                      "type":"fixedlist"
                      ,"customStyle":false
                      ,"id":"ko_fixedlist_1"
                      ,"firstBodyText":"<p>"+result.msg_html+"<br></p>"
                    };
                  break;
              default:
                var blocksValues = {
                    "type":"textBlock"
                    ,"customStyle":false
                    ,"longText":"<p>"+result.msg_html+"<br></p>"
                    ,"id":"ko_textBlock_1"
                  };
          }


          parseTemplate.mainBlocks.blocks.push(blocksValues);

          //we can reuse / catch data by using local storage.
          localStorage.setItem("edit_msg_tpl_id-" + rnd, result.msg_tpl_id);
          localStorage.setItem("name-" + rnd, result.name);
          localStorage.setItem("metadata-" + rnd, result.metadata);
          localStorage.setItem("template-" + rnd, JSON.stringify(parseTemplate));

          //once set new values in local storage.then all set to go to Mosaico editor.
          document.location = editorUrl+'#'+rnd;
        }
      }

            
      function createMetaData(result) {
        //define variables we need 
        var newMosaicoTplId = result.newMosaicoTplId;
        var from_template   = result.from_template;
        var from_metadata   = result.from_metadata;
        var name            = result.name;
      	// mosaico tab url
      	var mosaicoTabUrl = {/literal}"{crmURL p='civicrm/admin/messageTemplates' q="reset=1&activeTab=mosaico" h=0 }"{literal};
      	//generate random hash key
      	var rnd = Math.random().toString(36).substr(2, 7);
      	// get metadata of original mosaico msg template
      	var fromMetaData =  JSON.parse(from_metadata);

		
      	// Create new meta data
      	var metadata = {"template":fromMetaData.template, "name":name, "created":Date.now(),"changed":Date.now(),"key":rnd};
      	// Save metadata, template and name details in local storage.
      	localStorage.setItem("name-" + rnd, name);
      	localStorage.setItem("metadata-" + rnd, JSON.stringify(metadata));
      	localStorage.setItem("template-" + rnd, from_template);
      	// get new meta data saved on local
      	var newMetaData = localStorage.getItem("metadata-" + rnd);

      	// Post new meta data , new hash key to update in civicrm_mosaico_msg_template table
      	var postUrl = {/literal}"{crmURL p='civicrm/mosaico/ajax/setmd' h=0 }"{literal};
      	$.ajax({ type: "POST", url: postUrl, data: {md:newMetaData, id:newMosaicoTplId, hash_key:rnd}, async: true, dataType: 'json',
      	  success: function(result) {
      	    console.log(result);
      	    if (result.data == 'success') {
      	      var successMsg = "Mosaico Message template copied";
      	      CRM.status(successMsg, "success");
      	      window.location.href = mosaicoTabUrl;
      	    }
      	  },
      	  error : function() {
      	    CRM.alert('Could not update meta data for newly created mosaico msg template', 'Error');
      	  }
      	});
      }
    });
  {/literal}
</script>
