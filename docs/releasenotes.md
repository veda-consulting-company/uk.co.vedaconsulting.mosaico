**DO NOT DOWNLOAD DIRECTLY FROM GITHUB**
Download via https://civicrm.org/extensions/mosaico-civicrm-integration as there are additional packaging requirements not handled by github.

## Release 2.5

* Fix [#319](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/issues/319) Warn user before clicking away from mosaico editor.
* [#388](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/issues/388) Adds bullets (unorderd list) to editor
* [#389](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/issues/389)
[#390](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/issues/390)
Fix placeholder images showing on Drupal8, Wordpress.

## Release 2.4

#### Features
* [#345](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/pull/345) Use mosaico graphics service to resize thumbnail.
* [#352](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/pull/352) Make spell check available directly in mosaico.
* [#374](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/pull/374) Add API method to update template URLs.
* [#371](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/pull/371) Provide better support for high resolution images in mosaico.
* Improvements to documentation.

#### Fixes
* Fix path to translation files.
* [#376](https://github.com/veda-consulting-company/uk.co.vedaconsulting.mosaico/pull/376) Fix issue with image rendering (sometimes output buffer started by another module or plugin causes problem with image rendering).
* Update composer libraries for PHP7.3 support.
* Update Mosaico Services to be compatible with Symfony 4.
* Update npm dependencies.
* PHPUnit configuration compatible with PHPUnit6 and PHPUnit7.
* Various tweaks for compatibility if using Shoreditch theme.

## Release 2.3

* Change image placeholder permissions (*Fixes issue with image placeholders for some sites*).

## Release 2.2

* Allow sending traditional emails as a search task.
* Improve validation in image processing.
* Move maximum image size into a setting.
* Drop system check for shoreditch theme as it is not required for Mosaico to function.
* Improve browser incompatibility message.
* Sort templates alphabetically when displaying 'New Mailing'.
* Add translation support - the Mosaico editor will now appear in the same language as CiviCRM if language files are available.

## Release 2.1

* Always place iframe below KAM menu instead of behind.
* Replace '<title>TITLE<title>' with subject (render-time).
* Document hook_civicrm_mosaicoConfig.

## Release 2.0

All users of Mosaico should evaluate and upgrade to this release.

* Simplify requirements:
  * The shoreditch theme is not required - the extension will work with the current CiviCRM theme.
  * ImageMagick is not required - the extension will auto-detect between imagemagick and gd depending on what the server supports.
* Reduce memory usage which previously caused issues sending mail on some servers.
* Add a hook for mosaico editor configuration.

**Download link: https://civicrm.org/extensions/mosaico-civicrm-integration/version-20**
