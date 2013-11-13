#!/usr/bin/env bash

set -e

TAG=$1
if ! shift; then
    echo "$0: Missing required tag parameter." >&2
    exit 1
fi

if [ -z "$TAG" ]; then
    echo "$0: Empty tag parameter." >&2
    exit 1
fi

cd /tmp

REPOPATH="http://simplesamlphp.googlecode.com/svn/tags/$TAG/"

if [ -a "$TAG" ]; then
    echo "$0: Destination already exists: $TAG" >&2
    exit 1
fi

umask 0022

svn export "$REPOPATH"
mkdir -p "$TAG/config" "$TAG/metadata"
cp -rv "$TAG/config-templates/"* "$TAG/config/"
cp -rv "$TAG/metadata-templates/"* "$TAG/metadata/"
tar --owner 0 --group 0 -cvzf "$TAG.tar.gz" "$TAG"
rm -rf "$TAG"

echo "Created: /tmp/$TAG.tar.gz"
