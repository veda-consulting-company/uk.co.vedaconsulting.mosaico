# Development

This extension integrates `voidlabs/mosaico` with CiviCRM.  Like CiviCRM, the main body of this extension is built on
PHP, MySQL, and AngularJS. You can generally develop updates to the extension in a regular CiviCRM environment.

There are a few important exceptions and additions: the stylesheets (`./sass`, `./css`) and the editor (`voidlabs/mosaico`).
If you wish to develop patches for these, then it will require additional tools and processes.

## Requirements

* *Basic Development*
    * CiviCRM
    * [`git`](https://git-scm.com/)
    * [`composer`](https://git-scm.com/)
    * [`cv`](https://github.com/civicrm/cv) (*recommended*)
    * [`phpunit`](https://phpunit.de) (*recommended*)
* *Stylesheet Development* and *Editor Development*
    * [`nodejs`](https://nodejs.org/en) (*Currently the mosaico build script only works with node v8 and older. You can use
        [nvm](https://github.com/nvm-sh/nvm#installing-and-updating) to use multiple versions of nodejs. Eg. `nvm install 8 && nvm use 8`*)
    * [`npm`](https://www.npmjs.com)
    * [`grunt-cli`](http://gruntjs.com/getting-started)
    * [`shoreditch`](https://github.com/civicrm/org.civicrm.shoreditch) (*recommended*)

## Basic Development

The process is similar to many CiviCRM extensions:

```
## Navigate to your extension directory, e.g.
cd sites/default/files/civicrm/ext

## Download the extensions
git clone https://github.com/veda-consulting/uk.co.vedaconsulting.mosaico

## Download additional dependencies
cd uk.co.vedaconsulting.mosaico
composer install

## Enable the extension via web UI or CLI, e.g.
cv en mosaico
```

At this point, you can iteratively develop patches.  Submit proposed updates via Github pull-requests.

## Stylesheet Development

We use Gulp and Sass for styling and handle different running tasks. Firstly, you should install node packages using npm package manager:

```
npm install
```

Styling changes should go into `sass` directory and compiled to CSS using the following command:
```
gulp sass
```

Once you are done making your changes, please use BackstopJS (see [Testing](/testing#backstopjs-visual-regression-testing) to check for any possible visual regression issues.

Commit changes for both SCSS and CSS. Submit proposed updates via Github pull-requests.

## Editor Development

The CiviCRM extension (`uk.co.vedaconsulting.mosaico`) depends on the editor ([mosaico](https://github.com/voidlabs/mosaico)), which is an
independent project. The `mosaico` component has its own requirements and workflows.

`uk.co.vedaconsulting.mosaico` uses a pre-built copy of `mosaico` (`./packages/mosaico`), so you may work on `uk.co.vedaconsulting.mosaico`
without needing to understand `mosaico` development.  However, if you are doing development for both, then there are a few important details:

* __Using `mosaico.git` in the extension__: You may replace the pre-built folder (`./packages/mosaico`) with a git repo. The extension specifically
  uses a fork (https://github.com/civicrm/mosaico) with a few small patches. Typical setup steps:
    ```bash
    ## Remove pre-built copy of mosaico
    rm -rf packages/mosaico

    ## Download git repo
    git clone https://github.com/civicrm/mosaico.git -b v0.15-civicrm-2

    ## Build
    cd packages/civicrm
    npm install
    grunt build
    ```
* __Branching for `mosaico.git`__: The `civicrm/mosaico` fork follows the [Twigflow (Rebase)](https://gist.github.com/totten/39e932e5d10bc9e73e82790b2475eff2) pattern.
  You will notice additional branches such as `v0.15-civicrm-2` (*a branch derived from `v0.15` for use by `civicrm`; it is the second major variant of the branch*).
* __Tagging for `mosaico.git`__: If there has been an update to `mosaico.git`, then you should make a new tag (eg `v0.15-civicrm-2.1`). Github will generate
  a pre-built package for the new version.
* __Updating the dependency__: If there is a newer build of `mosaico`, then you may edit `./composer.json` and update the the `extra: downloads` configuration.
    ```bash
    vi composer.json
    composer update --lock
    ```

## Addendum: Setup.sh

The script `bin/setup.sh` handles various build activities:

```
## Download dependencies
./bin/setup.sh -D

## Regenerate DAOs
./bin/setup.sh -g

## Build zip archive
./bin/setup.sh -z
```

## Addendum: Publication

Whenever a change is merged or pushed to `uk.co.vedaconsulting.mosaico`, a bot on [jenkins test-ci](https://test.civicrm.org/view/Tools/job/Tool-Publish-mosaico/) automatically builds a new `zip` archive
and publishes [uk.co.vedaconsulting.mosaico-latest.zip](https://download.civicrm.org/extension/uk.co.vedaconsulting.mosaico/latest/uk.co.vedaconsulting.mosaico-latest.zip).

The build/publish process has a few properties:

 * It combines [`uk.co.vedaconsulting.mosaico`](https://github.com/veda-consulting/uk.co.vedaconsulting.mosaico),
   [`civicrm/mosaico`](https://github.com/civicrm/mosaico), and any other runtime dependencies into one `zip` file.
 * The version number is determined by reading `info.xml` (`<version>`) and appending the current Unix timestamp.
   * Example: If the `version` is declared as `1.0.beta1`, then it will be published as `1.0.beta1.1478151288`.
 * Three files are published:
   * The `zip` archive
   * The new `info.xml` file
   * A JSON document describing the build.
 * An alias is provided under the folder `latest`.

The bot does *not* publish the new version to `civicrm.org`.  To do this, download the `latest.zip` to get the version from the info.xml (
eg. `2.5.1597918155`).

Then add the actual release zip file to the release node on `https://civicrm.org/extensions/email-template-builder`.

Example filename: `https://download.civicrm.org/extension/uk.co.vedaconsulting.mosaico/2.5.1597918155/uk.co.vedaconsulting.mosaico-2.5.1597918155.zip`

### Currently you should trigger manually the following hooks:

* https://civicrm.org/extdir-backend/scan/5243 - this triggers the release process on https://civicrm.org/extensions, generates the unpublished release node and sends an email with a link to that node.
* https://docs.civicrm.org/admin/publish/mosaico - this forces an update of the mosaico docs

