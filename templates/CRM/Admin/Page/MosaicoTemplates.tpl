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
      	    createMetaData(result);
      	  },
      	  error : function() {
      	    CRM.alert('Could not copy mosaico template', 'Error');
      	  }
      	});
      });
            
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
