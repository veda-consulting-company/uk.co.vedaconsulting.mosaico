## uk.co.vedaconsulting.mosaico
This extension integrates Mosaico a responsive email template editor, with CiviCRM.
- Initial Blog Post - https://civicrm.org/blogs/parvez/a-new-beginning-for-civimail
- Beta Blog Post - https://civicrm.org/blog/deepaksrivastava/email-template-builder-civimosaico-is-now-beta
- Initial Video - https://vimeo.com/156633077

### Requirements

 * CiviCRM v4.7.16+ (or v4.6.26+ with unofficial backports from [#9555](https://github.com/civicrm/civicrm-core/pull/9555))
 * A CiviCRM extensions folder (In new sites since CiviCRM v4.7.0, this defaults to `files/civicrm/ext`. For older systems, see [the wiki](https://wiki.civicrm.org/confluence/display/CRMDOC/Extensions).)
 * PHP-ImageMagick
 * (For CLI-based installation) [`cv`](https://github.com/civicrm/cv)
 * (For git-based installation) [`cv`](https://github.com/civicrm/cv), [`git`](https://git-scm.com/), and [`grunt-cli`](http://gruntjs.com/getting-started)

### Installation (CLI)

If you have the command line tool [`cv`](https://github.com/civicrm/cv), then download Mosaico and related extensions:

```
cv dl --dev flexmailer shoreditch
cv dl uk.co.vedaconsulting.mosaico@http://dist.civicrm.org/extension/uk.co.vedaconsulting.mosaico/latest/uk.co.vedaconsulting.mosaico-latest.zip
```

> Tip: If you're using v4.6.x with backports, then `cv dl` will require an
> extra argument: "`--filter-ver=4.7.16`".  This enables it to download the
> latest extensions intended for v4.7.x (even if they aren't officially
> compatible with v4.6.x).

### Installation (Git)

Alternatively:

```
## Navigate to your extension directory, e.g.
cd sites/default/files/civicrm/ext

## Download the extensions
git clone https://github.com/civicrm/org.civicrm.flexmailer
git clone https://github.com/civicrm/org.civicrm.shoreditch
git clone https://github.com/veda-consulting/uk.co.vedaconsulting.mosaico
cd uk.co.vedaconsulting.mosaico
./bin/setup.sh -D
```

### Usage

If you haven't used Mosaico before, consult the the demo and tutorial materials from http://mosaico.io/index.html.

To send a new mailing, simply navigate to "Mailings => New Mailing". The CiviMail-Mosaico UI should appear.

Optionally, you may design reusable templates by navigating to "Mailings => Mosaico Templates".

When composing a new mailing, the default layout uses a single page.  To
change the layout, you can update the setting `mosaico_layout` to
`bootstrap-wizard` or `bootstrap-single`, e.g.

```
cv api setting.create mosaico_layout=bootstrap-wizard
```

### Having issues with this extension?

Please make sure you have followed installation instructions. 

Open issues on [github](https://github.com/veda-consulting/uk.co.vedaconsulting.mosaico/issues) with:
- screenshot of failure with any possible errors in firebug or js console
- any related logs or backtrace from civicrm
- tell us what version of CiviCRM and extension, you using.
- tell us the browser you are using (name and version) and test at least a second browser to tell us if this happen in both or only one (tell us the details about the second browser too).

### Development: Setup.sh

The script `bin/setup.sh` handles various build activities:

```
## Download dependencies
./bin/setup.sh -D

## Regenerate DAOs
./bin/setup.sh -g

## Build zip archive
./bin/setup.sh -z
```

### Development: Styling Changes

We use Gulp and Sass for styling and handle different running tasks. Firstly, you should install node packages using npm package manager:
```
npm install
```

Styling changes should go into `sass` directory and compiled to CSS using the following command:
```
gulp sass
```


### Development: Patching Mosaico

This extensions ships with a patched version of Mosaico. The patches are maintained as a fork
in https://github.com/civicrm/mosaico using [Twigflow (Rebase)](https://gist.github.com/totten/39e932e5d10bc9e73e82790b2475eff2).

### Development: Testing

See [TESTING.md](TESTING.md)

### Development: Publication

Whenever a change is merged or pushed to `uk.co.vedaconsulting.mosaico`, a bot automatically builds a new `zip` archive
and publishes to [http://dist.civicrm.org/extension/uk.co.vedaconsulting.mosaico/](http://dist.civicrm.org/extension/uk.co.vedaconsulting.mosaico/).

The build/publish process has a few properties:
 * It combines [`uk.co.vedaconsulting.mosaico`](https://github.com/veda-consulting/uk.co.vedaconsulting.mosaico),
   [`civicrm/mosaic`](https://github.com/civicrm/mosaico), and any other runtime dependencies into one `zip` file.
 * The version number is determined by reading `info.xml` (`<version>`) and appending the current Unix timestamp.
   * Example: If the `version` is declared as `1.0.beta1`, then it will be published as `1.0.beta1.1478151288`.
 * Three files are published:
   * The `zip` archive
   * The new `info.xml` file
   * A JSON document describing the build.
 * An alias is provided under the folder `latest`.

The bot does *not* publish the new version to `civicrm.org`.  To do this, take the new `info.xml` file and manually
upload it.  Since `civicrm.org` provides a directory of past and current versions, be sure to specify the download-URL
for a specific version number (e.g.  `1.0.beta1.1478151288`) rather than an alias (`latest`).
