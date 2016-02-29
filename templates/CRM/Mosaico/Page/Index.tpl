{literal}
  <body style="overflow: auto; text-align: center; background-color: #3f3d33; padding: 0; margin: 0; display: none;" data-bind="visible: true">
  <div style="background-color: #d2cbb1; padding: 10px;">
    <table class="logoWrapper" valign="bottom" align="center"><tr><td valign="bottom"><img class="logoImage" alt="Mosaico.io" style="display: block;" src="{/literal}{crmURL p='civicrm/mosaico/dist/img/mosaicologo.png' h=0}{literal}" /><div class="logoContainer"></div></td><td class="byTable" valign="bottom"><a href="http://www.voxmail.it"><img src="{/literal}{crmURL p='civicrm/mosaico/dist/img/byvoxmail.png' h=0}{literal}" alt="by VOXmail" /></a></td></tr></table>
    <div class="ribbon">opensource email template editor <span class="byRibbon" style="display: none;">by VOXmail</span></div>
  </div>
  <div class="disclaimer" style="position: absolute; top: 10px; right: 10px; width: 140px; padding: .5em; background-color: #900000; color: white; border: 2px dashed #d2cbb1">WARNING: experimental beta version, use with care!</div>
    <!-- ko if: edits().length -->
    <div style="overflow-y: auto; max-height: 200px; z-index: 10; position: relative; padding: 1em; background-color: #f1eee6;">
    <!-- ko ifnot: $root.showSaved --><span>You have saved contents in this browser! <a class="resumeButton" href="#" data-bind="click: $root.showSaved.bind(undefined, true);"><i class="fa fa-plus-square"></i> Show</a></span><!-- /ko -->
    <!-- ko if: $root.showSaved -->
    <table id="savedTable" align="center" cellspacing="0" cellpadding="8" style="padding: 5px; ">
    <caption>Email contents saved in your browser <a href="#" class="resumeButton" data-bind="click: $root.showSaved.bind(undefined, false);"><i class="fa fa-minus-square"></i> Hide</a></caption>
      <thead><tr>
        <th>Id</th><th>Name</th><th>Created</th><th>Last changed</th><th>Operations</th>
      </tr></thead>
    <tbody data-bind="foreach: edits">
      <tr>
        <td align="left"><a href="#" data-bind="attr: { href: '{/literal}{crmURL p='civicrm/mosaico/editor' h=0 q='snippet=2#'}{literal}'+key }"><code>#<span data-bind="text: key">key</span></code></a></td>
        <td style="font-weight: bold" align="left"><a href="#" data-bind="attr: { href: '{/literal}{crmURL p='civicrm/mosaico/editor' h=0 q='snippet=2#'}{literal}'+key }"><span data-bind="text: name">versamix</span></a></td>
        <td><span data-bind="text: typeof created !== 'undefined' ? $root.dateFormat(created) : '-'">YYYY-MM-DD</span></td>
        <td><span style="font-weight: bold" data-bind="text: typeof changed !== 'undefined' ? $root.dateFormat(changed) : '-'">YYYY-MM-DD</span></td>
        <td>
        <a class="operationButton" href="#" data-bind="attr: { href: '{/literal}{crmURL p='civicrm/mosaico/editor' h=0 q='snippet=2#'}{literal}'+key }" title="edit"><i class="fa fa-pencil"></i></a>
        <!--(<a href="#" data-bind="click: $root.renameEdit.bind(undefined, $index())" title="rinomina"><i class="fa fa-trash-o"></i></a>)-->
        <a class="operationButton" href="#" data-bind="click: $root.deleteEdit.bind(undefined, $index())" title="delete"><i class="fa fa-trash-o"></i></a>
        </td>
      </tr>
    </tbody>
    </table>
    <!-- /ko -->
    </div>
    <!-- /ko -->
    <div class="content" style="background-color: white; margin-top: -20px; padding-top: 15px; background-origin: border; padding-bottom: 2em">
    <h3>Try Mosaico first template: more to come soon, stay tuned!</h3>
    <div data-bind="foreach: templates">
      <div class="template template-xx" style="" data-bind="attr: { class: 'template template-'+name }">
        <div class="description" style="padding-bottom:5px"><b data-bind="text: name">xx</b>: <span data-bind="text: desc">xx</span></div>
        <a href="#" data-bind="click: $root.newEdit.bind(undefined, name), attr: { href: '{/literal}{crmURL p='civicrm/mosaico/editor' h=0 q='snippet=2#templates/'}{literal}'+name+'/template-'+name+'.html' }">
          <img src width="100%" alt="xx" data-bind="attr: { src: '{/literal}{crmURL p='civicrm/mosaico/templates/' h=0}{literal}'+name+'/edres/_full.png' }">
        </a>
      </div>
    </div>
    </div>
    <div class="subscribe" style="background-color: #900000; color: white; padding: .5em">
      <form action="http://mosaico.voxmail.it/user/register"  accept-charset="UTF-8" method="post">
        <label>Stay up to date about Mosaico main news via email (No spam!): </label>
        <input type="text" placeholder="email address" maxlength="64" name="mail" value="" class="form-text required" style="border: 0; padding: 5px 10px; box-sizing: border-box;"/>
        <input type="submit" name="submit" value="Keep in touch!"  class="form-submit" style="background-color: #330000; color: white; border: 0; box-sizing: border-box; border-radius: 3px; padding: 5px 10px" />
        <input type="hidden" name="form_id" value="user_register"  />
      </form>
    </div>
    <div class="about" style="background-color: #f1eee6; color: #333332; padding: 1em;">
      <h3>Why MOSAICO?</h3>
      <p>Designing and coding an email that <strong>works on every device</strong> and every client is a <strong>daunting task</strong> even for professionals.</p>
      <p>Mosaico allows you to realize <strong>a beautiful and effective template</strong> without a <strong>team of professionals</strong> and hours of testing to let it work everywhere.</p>
      <h3>What does make Mosaico unique?</h3>
      <p><strong>Responsive and tested Template</strong>, working with <strong>all major email clients and devices</strong></p>
      <p><strong>Rapid graphic personalization</strong> of the overall theme</p>
      <p><strong>Flexibility and style customization</strong> of single elements</p>
      <p>Intuitive <strong>drag &amp; drop image upload</strong> and <strong>automatic resizing</strong> to fit available space</p>
      <p><strong>Global undo/redo system</strong>: stop wasting time with saves, reviews and confirmations</p>
      <p><strong>Custom templates support</strong>, with a simple template language (make your html design work on Mosaico in few hours)</p>
      <p><strong>Open Source</strong>: Mosaico is distributed under the GPL license and the <a href="https://github.com/voidlabs/mosaico">complete code base</a> is available on GitHub</p>
    </div>
    <div class="footer" style="background-color: #3f3d33; color: #d2cbb1; padding: 1em">
      Void Labs Snc 2015® - All rights reserved - P.IVA 02137700395
    </div>
  </body>
{/literal}
