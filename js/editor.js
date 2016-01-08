$(function() {
  if (!Mosaico.isCompatible()) {
    alert('Update your browser!');
    return;
  }
  // var basePath = window.location.href.substr(0, window.location.href.lastIndexOf('/')).substr(window.location.href.indexOf('/','https://'.length));
  var basePath = window.location.href.substr(0, window.location.href.lastIndexOf('/'));
  var plugins;
  // A basic plugin that expose the "viewModel" object as a global variable.
  // plugins = [function(vm) {window.viewModel = vm;}];
  var ok = Mosaico.init({
    imgProcessorBackend: basePath+'/img/',
    emailProcessorBackend: basePath+'/dl/',
    titleToken: "MOSAICO Responsive Email Designer",
    fileuploadConfig: {
      url: basePath+'/upload/',
      // messages??
    }
  }, plugins);
  if (!ok) {
    console.log("Missing initialization hash, redirecting to main entrypoint");
    //document.location = ".";
  }
});
