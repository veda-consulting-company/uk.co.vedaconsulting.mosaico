#!/usr/bin/env bash
set -e

DEFAULT_MOSAICO_BRANCH="v0.15-civicrm-1"
EXTROOT=$(cd `dirname $0`/..; pwd)
XMLBUILD="$EXTROOT/build/xml/schema"

##############################
function do_help() {
  echo "usage: $0 [options]"
  echo "example: $0"
  echo "  -h     (Help)           Show this help screen"
  echo "  -a     (All)            Implies -Dg (default)"
  echo "  -D     (Download)       Download dependencies"
  echo "  -g     (GenCode)        Generate DAO files, SQL files, etc"
  echo "  -z     (Zip)            Build installable ZIP file"
}

##############################
function use_civiroot() {
  if [ -z "$CIVIROOT" ]; then
    CIVIROOT=$(cv ev 'echo $GLOBALS["civicrm_root"];')
    if [ -z "$CIVIROOT" -o ! -d "$CIVIROOT" ]; then
      do_help
      echo ""
      echo "ERROR: invalid civicrm-dir: [$CIVIROOT]"
      exit
    fi
  fi
}

##############################
## Make a tempdir, $ext/build/xml/schema; compile full XML tree
function buildXmlSchema() {
  use_civiroot

  mkdir -p "$XMLBUILD"

  ## Mix together main xml files
  cp -fr "$CIVIROOT"/xml/schema/* "$XMLBUILD/"
  cp -fr "$EXTROOT"/xml/schema/* "$XMLBUILD/"

  ## Build root xml file
  ## We build on the core Schema.xml so that we don't have to do as much work to
  ## manage inter-table dependencies
  grep -v '</database>' "$CIVIROOT"/xml/schema/Schema.xml > "$XMLBUILD"/Schema.xml
  cat "$XMLBUILD"/Schema.xml.inc >> "$XMLBUILD"/Schema.xml
  echo '</database>' >> "$XMLBUILD"/Schema.xml
}

##############################
## Run GenCode; copy out the DAOs
function buildDAO() {
  use_civiroot
  pushd $CIVIROOT/xml > /dev/null
    php GenCode.php $XMLBUILD/Schema.xml
  popd > /dev/null

  [ ! -d "$EXTROOT/CRM/Mosaico/DAO/" ] && mkdir -p "$EXTROOT/CRM/Mosaico/DAO/"
  cp -f "$CIVIROOT/CRM/Mosaico/DAO"/* "$EXTROOT/CRM/Mosaico/DAO/"
}

##############################
function cleanup() {
  use_civiroot
  for DIR in "$XMLBUILD" "$CIVIROOT/CRM/Mosaico" "$EXTROOT/CRM/Mosaico/DAO/" ; do
    if [ -e "$DIR" ]; then
      rm -rf "$DIR"
    fi
  done
}

##############################
function do_gencode() {
  cleanup
  buildXmlSchema
  buildDAO
  echo
  echo "If there have been XML schema changes, then be sure to manually update the .sql files!"
}

##############################
function do_download() {
  if [ ! -d "$EXTROOT/packages" ]; then
    mkdir "$EXTROOT/packages"
  fi
  if [ ! -d "$EXTROOT/packages/mosaico" ]; then
    git clone -b "$DEFAULT_MOSAICO_BRANCH" https://github.com/civicrm/mosaico "$EXTROOT/packages/mosaico"
  fi
  pushd "$EXTROOT/packages/mosaico" >> /dev/null
    local currentBranch=$(basename /$(git symbolic-ref HEAD 2>/dev/null))
    if [ "$currentBranch" != "$DEFAULT_MOSAICO_BRANCH" ]; then
      echo "Error: packages/mosaico is not on expected branch ($DEFAULT_MOSAICO_BRANCH). You may either:"
      echo " (1) Manage the branch manualy using 'npm' and 'grunt', or"
      echo " (2) Checkout the branch '$DEFAULT_MOSAICO_BRANCH'"
      exit 1
    fi
    npm install
    grunt build
  popd >> /dev/null
}

##############################
## Build installable ZIP file
function do_zipfile() {
  local canary="$EXTROOT/packages/mosaico/dist/mosaico.min.css"
  if [ ! -f "$canary" ]; then
    echo "Error: File $canary missing. Are you sure the build is ready?"
    exit 1
  fi

  local zipfile="$EXTROOT/build/build.zip"
  [ -f "$zipfile" ] && rm -f "$zipfile"
  local basedir=$(basename "$EXTROOT")
  pushd "$EXTROOT/../" >> /dev/null
    zip "$zipfile" --exclude="*~" "$basedir"/{LICENSE*,README*,info.xml,mosaico*php}
    zip "$zipfile" --exclude="*~" -r "$basedir"/{CRM,api,bin,css,js,sql,templates,xml}
    zip "$zipfile" --exclude="*~" -r "$basedir"/packages/mosaico/{NOTICE,README,LICENSE,dist,templates}*
  popd >> /dev/null
  echo "Created: $zipfile"
}

##############################
## Main
HAS_ACTION=

while getopts "aDghz" opt; do
  case $opt in
    h)
      do_help
      HAS_ACTION=1
      ;;
    a)
      do_download
      do_gencode
      HAS_ACTION=1
      ;;
    D)
      do_download
      HAS_ACTION=1
      ;;
    g)
      do_gencode
      HAS_ACTION=1
      ;;
    z)
      do_zipfile
      HAS_ACTION=1
      ;;
    \?)
      do_help
      echo "Invalid option: -$OPTARG" >&2
      exit 1
      ;;
    :)
      echo "Option -$OPTARG requires an argument." >&2
      exit 1
      ;;
  esac
done

if [ -z "$HAS_ACTION" ]; then
  do_help
  exit 2
fi
