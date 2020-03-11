#!/bin/bash
HOME_DIR="/var/www/esercitazione3/data/gnupg"
PUBLIC_KEYRING="/var/www/esercitazione3/data/gnupg/pubring.gpg"
SECRET_KEYRING="/var/www/esercitazione3/data/gnupg/secring.gpg"
PASSPHRASE_FILE="/var/www/esercitazione3/data/passphrase.txt"
INPUT_MESSAGE="$1"
OUTPUT_MESSAGE="$2"
gpg --batch --yes --always-trust --no-default-keyring --homedir "$HOME_DIR" --keyring "$PUBLIC_KEYRING" --secret-keyring "$SECRET_KEYRING" --passphrase-fd 0 --sign --output "$OUTPUT_MESSAGE" "$INPUT_MESSAGE" < "$PASSPHRASE_FILE"
