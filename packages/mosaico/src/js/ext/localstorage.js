"use strict";
/* global global: false */
var console = require("console");
var ko = require("knockout");
var $ = require("jquery");

var lsLoader = function(hash_key, emailProcessorBackend) {
  var mdStr = global.localStorage.getItem("metadata-" + hash_key);
  if (mdStr !== null) {
    var model;
    var td = global.localStorage.getItem("template-" + hash_key);
    if (td !== null) model = JSON.parse(td);
    var md = JSON.parse(mdStr);
    return {
      metadata: md,
      model: model,
      extension: lsCommandPluginFactory(md, emailProcessorBackend)
    };
  } else {
    throw "Cannot find stored data for "+hash_key;
  }
};

var lsCommandPluginFactory = function(md, emailProcessorBackend) {
  var commandsPlugin = function(mdkey, mdname, viewModel) {

    // console.log("loading from metadata", md, model);
    var saveCmd = {
      name: 'Save', // l10n happens in the template
      enabled: ko.observable(true)
    };
    saveCmd.execute = function() {
      saveCmd.enabled(false);
      viewModel.metadata.changed = Date.now();
      console.log(viewModel.metadata);
      // DS: a popup asking msg template title to save in civi
      var date  = new Date();
      var options = { hour: 'numeric', minute: 'numeric', second: 'numeric'};
      var fulldate = date.toLocaleDateString('en-GB',options);

      var metaName = global.localStorage.getItem("name-" + mdkey);
      if (!metaName || metaName == 'null') metaName   = viewModel.t('Mosaico Template ' + fulldate);
      metaName = global.prompt(viewModel.t("Please enter the Message title"), metaName);
      viewModel.metadata.name = metaName;
      global.localStorage.setItem("name-" + mdkey, metaName);
      // end
      if (typeof viewModel.metadata.key == 'undefined') {
        console.warn("Unable to find ket in metadata object...", viewModel.metadata);
        viewModel.metadata.key = mdkey;
      }
      global.localStorage.setItem("metadata-" + mdkey, viewModel.exportMetadata());
      global.localStorage.setItem("template-" + mdkey, viewModel.exportJSON());
      saveCmd.enabled(true);

      viewModel.notifier.info(viewModel.t("Saving in CiviCRM..."));
      var postUrl = emailProcessorBackend ? emailProcessorBackend : '/dl/';
      var post = $.post(postUrl, {
        action: 'save',
        key:  viewModel.metadata.key,
        name: viewModel.metadata.name,
        html: viewModel.exportHTML(),
        metadata: viewModel.exportMetadata(),
        template: viewModel.exportJSON(),
      }, null, 'html');
      post.fail(function() {
        console.log("fail", arguments);
        viewModel.notifier.error(viewModel.t('Unexpected error talking to server: contact us!'));
      });
      post.success(function() {
        console.log("success", arguments);
        try {
          var result = JSON.parse(arguments[0]);
          if ('id' in result) {
            console.log("id", result.id);
            viewModel.notifier.success(viewModel.t("Saved as message template in CiviCRM."));
          } else {
            viewModel.notifier.error(viewModel.t('Something went wrong while saving message template!'));
          }
        }
        catch(e) {
          viewModel.notifier.error(viewModel.t('Something went wrong while saving message template!'));
        }
      });
    };
    var testCmd = {
      name: 'Test', // l10n happens in the template
      enabled: ko.observable(true)
    };
    var downloadCmd = {
      name: 'Download', // l10n happens in the template
      enabled: ko.observable(true)
    };
    testCmd.execute = function() {
      testCmd.enabled(false);
      var email = global.localStorage.getItem("testemail");
      if (email === null || email == 'null') email = viewModel.t('Insert here the recipient email address');
      email = global.prompt(viewModel.t("Test email address"), email);
      if (email.match(/@/)) {
        global.localStorage.setItem("testemail", email);
        console.log("TODO testing...", email);
        var postUrl = emailProcessorBackend ? emailProcessorBackend : '/dl/';
        var post = $.post(postUrl, {
          action: 'email',
          rcpt: email,
          subject: "[test] " + mdkey + " - " + mdname,
          html: viewModel.exportHTML()
        }, null, 'html');
        post.fail(function() {
          console.log("fail", arguments);
          viewModel.notifier.error(viewModel.t('Unexpected error talking to server: contact us!'));
        });
        post.success(function() {
          console.log("success", arguments);
          try {
            var result = JSON.parse(arguments[0]);
            if ('sent' in result) {
              console.log("sent", result.sent);
              viewModel.notifier.success(viewModel.t("Test email sent..."));
            } else {
              viewModel.notifier.error(viewModel.t('Something went wrong while sending email!'));
            }
          }
          catch(e) {
            viewModel.notifier.error(viewModel.t('Something went wrong while sending email!'));
          }
        });
        post.always(function() {
          testCmd.enabled(true);
        });
      } else {
        global.alert(viewModel.t('Invalid email address'));
        testCmd.enabled(true);
      }
    };
    downloadCmd.execute = function() {
      downloadCmd.enabled(false);
      viewModel.notifier.info(viewModel.t("Downloading..."));
      viewModel.exportHTMLtoTextarea('#downloadHtmlTextarea');
      var postUrl = emailProcessorBackend ? emailProcessorBackend : '/dl/';
      global.document.getElementById('downloadForm').setAttribute("action", postUrl);
      global.document.getElementById('downloadForm').submit();
      downloadCmd.enabled(true);
    };

    viewModel.save = saveCmd;
    viewModel.test = testCmd;
    viewModel.download = downloadCmd;
  }.bind(undefined, md.key, md.name);

  return commandsPlugin;
};

module.exports = lsLoader;
