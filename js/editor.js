$(function() {
  if (!Mosaico.isCompatible()) {
    alert('Update your browser!');
    return;
  }
  var cmsPath = window.location.href.substr(0, window.location.href.lastIndexOf('/'));
  var plugins;
  // A basic plugin that expose the "viewModel" object as a global variable.
  // plugins = [function(vm) {window.viewModel = vm;}];
  var ok = Mosaico.init({
    imgProcessorBackend: cmsPath+'/img',
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
