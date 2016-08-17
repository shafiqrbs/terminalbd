#!/bin/sh

mkdir -p app/cache
mkdir -p app/logs
mkdir -p web/cache
mkdir -p web/images
mkdir -p web/uploads
mkdir -p web/uploads/files
mkdir -p web/uploads/user

echo "Removing old cache if any"
rm -rf app/cache/*
rm -rf app/logs/*

echo "Generating bootstrap cache"
php vendor/sensio/distribution-bundle/Sensio/Bundle/DistributionBundle/Resources/bin/build_bootstrap.php

echo "(Re)Creating assets symlink"
rm web/assets
ln -s ../app/Resources/assets/ web/assets

echo "Dumping assets"
app/console  assets:install --symlink --relative
app/console  assetic:dump --env=prod --no-debug

echo "Make directory writtable"
chmod -R 0777 app/cache app/logs web/cache web/uploads web/images