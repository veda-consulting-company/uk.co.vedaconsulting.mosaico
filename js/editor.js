$(function() {
  if (!Mosaico.isCompatible()) {
    alert('Update your browser!');
    return;
  }
  var cmsPath = window.location.href.substr(0, window.location.href.lastIndexOf('/'));
  var imageUrl = cmsPath;
  if (cmsPath.indexOf('wp-admin/admin.php') !== -1) {//Fix me CRM.url 
    imageUrl = cmsPath.replace('wp-admin/admin.php','civicrm');//In order to stop image urls get admin in there which causes images not being displayed in email for wordpress
  } else if (cmsPath.indexOf('administrator/') !== -1) { //Fix me CRM.url
    imageUrl = cmsPath.replace('administrator/',''); //In order to stop image urls get admin in there which causes images not being displayed in email for joomla
  }
  var plugins;
  // A basic plugin that expose the "viewModel" object as a global variable.
  // plugins = [function(vm) {window.viewModel = vm;}];
  var ok = Mosaico.init({
    imgProcessorBackend: imageUrl+'/img',
    emailProcessorBackend: cmsPath+'/dl',
    titleToken: "MOSAICO Responsive Email Designer",
    fileuploadConfig: {
      url: cmsPath+'/upload',
      // messages??
    }
  }, plugins);
  if (!ok) {
    console.log("Missing initialization hash, redirecting to main entrypoint");
    //document.location = ".";
  }
});
