#!/bin/bash

echo "PrivateAsk update script started"

baseGitDir=$PWD

function usage {
  cat << EOT

  Usage :  $0 [options]

  Options: 
  -h -? --help    Display this message
  -y --yes        Do not ask any questions, auto respond yes to all

  This script is part of PrivateAsk, an open source project by Anestis Varsamidis
  Source at http://github.com/anestv/pa
EOT
}

# Handle command line arguments
for opt in $*; do
  case "$opt" in
    
    -h|-\?|--help) usage; exit 0;;
    
    -y|--yes) alwaysYes="y";;
    
    *) echo "Unknown option: $opt Run $0 --help for help"; exit 1;;
    
  esac
done


# Args: 1: (string) Promt / question to user
#  2: (int) 1 to run $3 if user said yes, 0 to run $3 if user didn't say yes
#  3: (string) commands to execute
# Example: ask_user_yes_no "Build Semantic?" 1 "echo Building"
# Example: ask_user_yes_no "Do you really want to continue?" 0 "echo Exiting; exit 0"
function ask_user_yes_no {
  
  if [ "$alwaysYes" = "y" ]; then
    true
  else
    echo -e "$1 (y/n)"
    
    read ans
    [[ "$ans" =~ ^[yY]([eE][sS])?$ ]] # we will use its retrn code
  fi
  
  if (( $2 ^ $? )); then
    eval $3
  fi
}


# warning, we only tested on Ubuntu
source /etc/lsb-release
if [ "$DISTRIB_ID" != "Ubuntu" ]; then
  ask_user_yes_no "This script has only been tested in Ubuntu 14.04. Continue?"\
    0 "exit 0"
fi

echo

# prints error if program not installed
function check_program_installed {
  # set to 0 initially
  local error=0
  # set to 1 if not found
  type $1 >/dev/null 2>&1 || { local error=1; }
  
  if [ $error = 1 ]; then
    echo "ERROR: $1 is not installed. Please install $1 and try again"
    exit 1
  fi
}

# check dependencies
check_program_installed git
check_program_installed npm
check_program_installed composer

# check for uncommited changes
git diff --exit-code --quiet
if [ $? != 0 ]; then
  ask_user_yes_no "It seems some files at your working directory have uncommitted changes.
It is recommended to commit your changes. Continue without committing?"\
    0 "exit 0"
fi

echo "Running composer install..."
composer install --no-interaction


# install (or update) Semantic UI
echo "Installing Seamntic UI ..."
npm install semantic-ui


cd node_modules/semantic-ui

echo "Running npm install..."
npm install


ask_user_yes_no "npm install usually resets src/site/ and dist/. Do you want to git checkout?"\
  1 "git checkout dist/ src/site/ src/theme.config semantic.json"

ask_user_yes_no "Remove unneeded folders (examples, src/_site, test)?"\
  1 "rm -rf examples src/_site test"

cd src/themes
ask_user_yes_no "Do you want to remove Semantic's unused theme folders?"\
  1 "find * -maxdepth 0 ! -name 'default' -type d -exec rm -rf {} +"
# remove all except default
cd ../..


ask_user_yes_no "Do you want to build Semantic UI? Recommended if updated"\
  1 "gulp build"

# note, we are still on node_modules/semantic-ui

echo
echo "Script complete!"
echo "Some useless files may have been created (e.g. composer.lock)"
