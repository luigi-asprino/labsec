#!/bin/bash
HOME_DIR="./data/gnupg"
PUBLIC_KEYRING="/var/www/html/esercitazione3/data/gnupg/pubring.gpg"
SECRET_KEYRING="/var/www/html/esercitazione3/data/gnupg/secring.gpg"
KEYID="$1"
gpg --batch --yes --always-trust --no-default-keyring --homedir "$HOME_DIR" --keyring "$PUBLIC_KEYRING" --secret-keyring "$SECRET_KEYRING" --delete-key "$KEYID"
