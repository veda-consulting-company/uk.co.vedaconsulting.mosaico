<body class="mo-standalone">
</body>
{capture assign=msgTplURL}{crmURL p='civicrm/admin/messageTemplates' q="reset=1&activeTab=mosaico"}{/capture}
{literal}
<script type="text/javascript">
  $(function() {
    addCustomButton();
  });
  function addCustomButton() {
    var msgTplURL = "{/literal}{$msgTplURL}{literal}";
    if($('#page .rightButtons').is(':visible')) {
      $("#page .rightButtons").append('<a href="'+msgTplURL+'" class="ui-button">Done</a>');
    } else {
      console.log('timeout 50');
      setTimeout(addCustomButton, 50);
    }
  }
  window.print = function() {
    return false;
  }
</script>
{/literal}
