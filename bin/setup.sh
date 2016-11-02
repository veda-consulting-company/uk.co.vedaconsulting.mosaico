#!/usr/bin/env bash
set -e

DEFAULT_MOSAICO_BRANCH="v0.15-civicrm-1"
EXTROOT=$(cd `dirname $0`/..; pwd)
CIVIROOT=$(cv ev 'echo $GLOBALS["civicrm_root"];')
XMLBUILD="$EXTROOT/build/xml/schema"

if [ -z "$CIVIROOT" -o ! -d "$CIVIROOT" ]; then
  do_help
  echo ""
  echo "ERROR: invalid civicrm-dir: [$CIVIROOT]"
  exit
fi


#echo "[$EXTROOT] [$CIVIROOT]"; exit

##############################
function do_help() {
  echo "usage: $0 [options]"
  echo "example: $0"
  echo "  -h     (Help)           Show this help screen"
  echo "  -a     (All)            Implies -Dg (default)"
  echo "  -D     (Download)       Download dependencies"
  echo "  -g     (GenCode)        Generate DAO files, SQL files, etc"
}

##############################
## Make a tempdir, $ext/build/xml/schema; compile full XML tree
function buildXmlSchema() {
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
  pushd $CIVIROOT/xml > /dev/null
    php GenCode.php $XMLBUILD/Schema.xml
  popd > /dev/null

  [ ! -d "$EXTROOT/CRM/Mosaico/DAO/" ] && mkdir -p "$EXTROOT/CRM/Mosaico/DAO/"
  cp -f "$CIVIROOT/CRM/Mosaico/DAO"/* "$EXTROOT/CRM/Mosaico/DAO/"
}

##############################
function cleanup() {
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
    git fetch --all
    git checkout "$DEFAULT_MOSAICO_BRANCH"
    npm install
    grunt build
  popd >> /dev/null
}

##############################
## Main
while getopts "aDgh" opt; do
  case $opt in
    h)
      do_help
      exit 0
      ;;
    a)
      do_download
      do_gencode
      exit 0
      ;;
    D)
      do_download
      exit 0
      ;;
    g)
      do_gencode
      exit 0
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

do_help
exit 2
