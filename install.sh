#!/bin/bash

echo "PrivateAsk installation script started"

baseGitDir=$PWD

function usage {
  cat << EOT

  Usage :  $0 [options]

  Options: 
  -h -? --help    Display this message

  This script is part of PrivateAsk, an open source project by Anestis Varsamidis
  Source at http://github.com/anestv/pa
EOT
}

# Handle command line arguments
for opt in $*; do
  case "$opt" in
    
    -h|-\?|--help) usage; exit 0;;
    
    *) echo "Unknown option: $opt Run $0 --help for help"; exit 1;;
    
  esac
done

sudo apt-get update -q

sudo apt-get install apache2 -q
sudo a2enmod actions
sudo a2enmod rewrite
echo "export PATH=/home/vagrant/.phpenv/bin:$PATH" | sudo tee -a /etc/apache2/envvars > /dev/null
echo "$(curl -fsSL https://gist.github.com/roderik/16d751c979fdeb5a14e3/raw/gistfile1.txt)" | sudo tee /etc/apache2/conf.d/phpconfig > /dev/null
echo "$(curl -fsSL https://gist.github.com/roderik/2eb301570ed4a1f4c33d/raw/gistfile1.txt)" | sed -e "s,PATH,`pwd`/web,g" | sudo tee /etc/apache2/sites-available/default > /dev/null

sudo service apache2 restart

mysql -u root -e "CREATE DATABASE IF NOT EXISTS privateask;"
mysql -u root -e "GRANT ALL PRIVILEGES ON *.* TO 'privateask'@'localhost' WITH GRANT OPTION;"

mysql -u root -e "SOURCE privateask.sql"


composer install --no-interaction


sudo apt-get install python3 -q
sudo apt-get autoremove

/bin/cat <<SECRETS > "app/core/secrets.ini"

address = "127.0.0.1"
username = "root"
password = ""
database = "privateask"

contactUrl = "https://www.github.com/anestv"

baseDir = "/"

SECRETS

echo "PrivateAsk installation script is complete!"
