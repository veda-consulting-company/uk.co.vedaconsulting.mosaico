#!/usr/bin/env bash
EXTROOT=$(cd `dirname $0`/..; pwd)
CIVIROOT="$1"
XMLBUILD="$EXTROOT/build/xml/schema"

if [ -z "$CIVIROOT" -o ! -d "$CIVIROOT" ]; then
  echo "ERROR: invalid civicrm-dir: [$CIVIROOT]"
  echo ""
  echo "usage: $0 <civicrm-dir>"
  echo "example: $0 /var/www/drupal/sites/all/modules/civicrm"
  exit
fi

#echo "[$EXTROOT] [$CIVIROOT]"; exit

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
## Main
set -e
cleanup
buildXmlSchema
buildDAO
echo
echo "If there have been XML schema changes, then be sure to manually update the .sql files!"
