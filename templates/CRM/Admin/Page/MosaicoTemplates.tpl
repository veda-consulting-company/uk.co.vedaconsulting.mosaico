<div id="mosaicoTemplates" class='ui-tabs-panel ui-widget-content ui-corner-bottom' style="display:none;">
  <div class="help">FIXME: Mosaico Templates Help message</div>
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
                <tr id="message_template-{$row.id}" class="crm-entity {$row.class}{if NOT $row.is_active} disabled{/if}">
                  <td>{$row.msg_title}</td>
                  <td>{$row.msg_subject}</td>
                  <td id="row_{$row.id}_status">{if $row.is_active eq 1} {ts}Yes{/ts} {else} {ts}No{/ts} {/if}</td>
                  <td>{$row.action|replace:'xx':$row.id}</td>
                </tr>
            {/foreach}
            </tbody>
          </table>
          {/if}

          <div class="action-link">
            {crmButton p='civicrm/admin/messageTemplates/add' q="action=add&reset=1" id="newMessageTemplates"  icon="circle-plus"}{ts}Add Message Template{/ts}{/crmButton}
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
            }
          }); 
        }
      });
    });
  {/literal}
</script>
