## uk.co.vedaconsulting.mosaico
This extension integrates Mosaico a responsive email template editor, with CiviCRM.
- Blog Post - https://civicrm.org/blogs/parvez/a-new-beginning-for-civimail
- Video - https://vimeo.com/156633077

### How to Install
1. Have imageMagick installed in your environment. Mainly as php module.
2. Download extension from https://github.com/veda-consulting/uk.co.vedaconsulting.mosaico/releases/latest.
3. Unzip / untar the package and place it in your configured extensions directory.
4. Make sure to keep the directory name as "uk.co.vedaconsulting.mosaico" (for e.g not like - uk.co.vedaconsulting.mosaico-1.0-alphaX) to avoid any icon / image loading issues.
5. When you reload the Manage Extensions page the new “Mosaico” extension should be listed with an Install link.
6. Click the "Install" link.
7. Make sure "Extension Resource URL" is configured with Administer » System Settings » Resouce URLs.

### Usage
1. Go to the CiviCRM Mailings menu.  Select the new option "Message Template Builder".
2. Build a template in Mosaico.  If you haven't used Mosaico before, a tutorial is available when you select a template when trying it at http://mosaico.io/index.html#about.
3. When you're done creating your template, click the "Save" link at the top right of the screen.
4. To use the template, create a new CiviMail mailing.  Your Mosaico template will be available from the "Templates" drop-down menu.

### Having issues with this extension?

Please make sure you have followed installation instructions. 

To the "Message Template Builder" screen add a runcheck=1 argument in the url to see any errors that might be causing it - url e.g: http://example.org/civicrm/mosaico/index?reset=1&runcheck=1.

Open issues on [github](https://github.com/veda-consulting/uk.co.vedaconsulting.mosaico/issues) with:
- screenshot of failure with any possible errors in firebug or js console
- any related logs or backtrace from civicrm
- tell us what version of CiviCRM and extension, you using.
- tell us the browser you are using (name and version) and test at least a second browser to tell us if this happen in both or only one (tell us the details about the second browser too).
