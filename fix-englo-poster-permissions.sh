#!/bin/bash
# Fix engloPoster folder: make it owned by www-data so the web server can write uploads.
# Run with: sudo bash fix-englo-poster-permissions.sh

cd "$(dirname "$0")"
DIR="storage/app/public/engloPoster"
if [ -d "$DIR" ]; then
  chown -R www-data:www-data "$DIR"
  chmod -R 775 "$DIR"
  echo "Done. $DIR is now www-data:www-data and 775."
else
  mkdir -p "$DIR"
  chown -R www-data:www-data "$DIR"
  chmod -R 775 "$DIR"
  echo "Created $DIR and set www-data:www-data, 775."
fi
