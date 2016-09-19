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
      $('.copy-template').click(function(event) {
	$el = $(this);
	var msgTplId = $(this).attr('value');
	console.log('copying msg template ' + msgTplId);
	var postUrl = {/literal}"{crmURL p='civicrm/mosaico/ajax/copy' h=0 }"{literal};
	console.log('posturl copy=' + postUrl);
	$.ajax({ type: "POST", url: postUrl, data: {id:msgTplId}, async: true, dataType: 'json',
	  success: function(result) {
	    console.log(result.newMosaicoTplId);
	    console.log(result.hash_key);
	    console.log(result.name);
	    //create mos template and update meta data in civicrm_mosaico_msg_template table
	    createmostpl(result.newMosaicoTplId, result.hash_key, result.name);
	    CRM.refreshParent($el);
	  }
	});
	$("#mainTabContainer").tabs( {active: tabIndex} );
      });
      
      function createmostpl(newMosaicoTplId, hash_key, name) {
	//generate random hash key
	var rnd = Math.random().toString(36).substr(2, 7);
	// get template of original mosaico msg template & metadata
	var template = localStorage.getItem("template-" + hash_key);
	var origmetadata =  JSON.parse(localStorage.getItem("metadata-" + hash_key));
      
	// Create new meta data
	var metadata = {"template":origmetadata.template, "name":name, "created":Date.now(),"changed":Date.now(),"key":rnd};
	// Save metadata, template and name details in local storage.
	localStorage.setItem("name-" + rnd, name);
	localStorage.setItem("metadata-" + rnd, JSON.stringify(metadata));
	localStorage.setItem("template-" + rnd, template);
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
	    }
	  }
	});
      }
    });
  {/literal}
</script>
