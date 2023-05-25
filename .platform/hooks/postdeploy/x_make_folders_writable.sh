#!/bin/sh

# Make Folders Writable

# After the deployment finished, give the full 0777 permissions
# to some folders that should be writable, such as the storage/
# or bootstrap/cache/, for example.

sudo chmod -R 775 storage/
sudo chmod -R 775 bootstrap/cache/
php artisan optimize

# Storage Symlink Creation

# php artisan storage:link
# sudo nohup php artisan queue:listen --timeout=10000000 --daemon > storage/logs/laravel.log &
# exit 0
