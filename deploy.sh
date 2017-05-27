#!/bin/bash

# Update DB
php app/console doctrine:schema:update --force
echo -e "Database was updated successfully."

# Clear cache
rm -R app/cache/*
echo -e "Clearing the cache was successfully done."

# Update CSS, JS, Image styles
php app/console assets:install web --symlink
echo -e "Symlinks were updated successfully."

# Installing CSS, JS and Image styles
php app/console assetic:dump 
echo -e "Symlinks were installed successfully." 

# Set needed permissions for app folders
chmod 777 -R app/cache/ app/logs/
echo -e "All post deploy procedures are finished successfully!"
