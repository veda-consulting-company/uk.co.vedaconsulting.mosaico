<body class="mo-standalone">
</body>
{literal}
<script type="text/javascript">
  $(function() {
    addCustomButton();
  });
  
  function addCustomButton() {
    if($('#page .rightButtons').is(':visible')) {
      $("#page .rightButtons").append('<a href="index" class="ui-button">Done</a>');
    } else {
      console.log('timeout 50');
      setTimeout(addCustomButton, 50);
    }
  }
</script>
{/literal}
