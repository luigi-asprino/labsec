#!/bin/bash

#GPG_TTY="/dev/pts/0"
#export GPG_TTY

#echo $GPG_TTY

echo use-agent >> /var/www/html/esercitazione3/data/gnupg/gpg.conf
echo pinentry-mode loopback >> /var/www/html/esercitazione3/data/gnupg/gpg.conf
echo allow-loopback-pinentry >> /var/www/html/esercitazione3/data/gnupg/gpg-agent.conf
echo RELOADAGENT | gpg-connect-agen

chown -R www-data:www-data /var/www/html
chmod -R 777 /var/www/html/esercitazione3/cache
chmod -R 777 /var/www/html/esercitazione3/data

rm -rf /var/www/html/esercitazione3/data/gnupg/S.gpg-agent* /var/www/html/esercitazione3/data/gnupg/.gpg-v21-migrated

#whoami

#ls -la /var/www/html/esercitazione3/data/gnupg/

HOME_DIR="/var/www/html/esercitazione3/data/gnupg"
PUBLIC_KEYRING="/var/www/html/esercitazione3/data/gnupg/pubring.gpg"
SECRET_KEYRING="/var/www/html/esercitazione3/data/gnupg/secring.gpg"
PASSPHRASE_FILE="/var/www/html/esercitazione3/data/passphrase.txt"
RECIPIENT="$1"
INPUT_MESSAGE="$2"
OUTPUT_MESSAGE="$3"

gpg --batch --yes --no-tty --always-trust --no-default-keyring --homedir "$HOME_DIR" --keyring "$PUBLIC_KEYRING" --secret-keyring "$SECRET_KEYRING" --passphrase-fd 0 --sign --encrypt --recipient "$RECIPIENT" --output "$OUTPUT_MESSAGE" "$INPUT_MESSAGE" < "$PASSPHRASE_FILE"
