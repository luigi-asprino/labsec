#!/bin/bash
HOME_DIR="/var/www/esercitazione3/data/gnupg"
PUBLIC_KEYRING="/var/www/esercitazione3/data/gnupg/pubring.gpg"
SECRET_KEYRING="/var/www/esercitazione3/data/gnupg/secring.gpg"
gpg --batch --yes --always-trust --no-default-keyring --homedir "$HOME_DIR" --keyring "$PUBLIC_KEYRING" --secret-keyring "$SECRET_KEYRING" --list-keys
