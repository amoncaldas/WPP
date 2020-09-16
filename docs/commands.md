# Commands to configure, run and manipulate environment and content #

## Dump the latest db to the file system ##

```sh
docker exec wpp-mysql-local /bin/sh -c "mysqldump -u root -padmin wordpress > wpp-dump.sql"
docker cp wpp-mysql-local:/wpp-dump.sql $PWD/mysql/db-backup.sql
```

## Update database in container from dump ##

```sh
docker cp $PWD/mysql/db-backup.sql wpp-mysql-local:/wpp-dump.sql
docker exec wpp-mysql-local /bin/sh -c "mysql -uroot -padmin wordpress < wpp-dump.sql"
```

## Copy remote file to local file system ##

```sh
scp  user@server:~/folder/file.ext ~/local-folder
```

## Run docker compose ##

```sh
cd ~/apps/wpp
docker-compose -f docker-compose.yml up

# Starting already created:
docker-compose -f docker-compose.yml start
```

## Import database from local backup ##

```sh
# import from file `db-backup.sql` in `mysql` folder
docker exec -i wpp-mysql-local  mysql -uroot -padmin wordpress < mysql/db-backup.sql
```

## create and import database file in staging ##

```sh
# dump database
docker exec wpp-mysql-staging /bin/sh -c "mysqldump -u root -padmin wordpress > wpp-dump.sql"
docker cp wpp-mysql-staging:/wpp-dump.sql $PWD/wpp-dump.sql

# connect and show databases
docker exec wpp-mysql-staging /bin/sh -c "echo 'show databases' | mysql -uroot -padmin"
# create database
docker exec wpp-mysql-staging /bin/sh -c "echo 'create database wordpress' | mysql -uroot -padmin"
# import database
docker exec -i wpp-mysql-staging mysql -uroot -padmin wordpress < mysql/db-backup.sql
```

## create and import database file in production ##

```sh
# connect and show databases
docker exec wpp-mysql-production /bin/sh -c "echo 'show databases' | mysql -uroot -padmin"
# create database
docker exec wpp-mysql-production /bin/sh -c "echo 'create database wordpress' | mysql -uroot -padmin"
# import database
docker exec -i wpp-mysql-staging mysql -uroot -padmin wordpress < mysql/db-backup.sql
```

## backup old and rebuild with restored db in production ##

```sh
# dump database
docker exec wpp-mysql-production /bin/sh -c "mysqldump -u root -padmin wordpress > wpp-dump.sql"
docker cp wpp-mysql-production:/wpp-dump.sql $PWD/wpp-dump.sql

# stop docker compose
docker-compose -f master.docker-compose.yml down

# empty database files (to be able to restore backup)
sudo rm -rf ~/wpp_mapped_folder/wordpress/db/*

# move to auto restore location
sudo mv wpp-dump.sql ~/wpp/mysql/db-backup.sql
docker-compose -f master.docker-compose.yml up -d
```

## backup and download database in production ##

```sh
# dump database
docker exec wpp-mysql-production /bin/sh -c "mysqldump -u root -padmin wordpress > wpp-dump.sql"
docker cp wpp-mysql-production:/wpp-dump.sql $PWD/wpp-dump.sql

# copy from remote to local
scp -i keys/ssh_access.pem ubuntu@129.206.7.180:~/wpp-dump.sql ~/apps/wpp-dump.sql
```

## Copy wp-config from container ##

```sh
docker cp 0a0:/var/www/html/wp-config.php $PWD/wp-config.php
```

## User management via wp-cli ##

```sh
# create a user as admin
docker exec --user root wpp-website-staging /bin/sh -c "wp user create <user-login> user@domain.tld --role=administrator --allow-root"
# get the password printed out!

# update existing user password:
docker exec --user root wpp-website-staging /bin/sh -c 'wp user update 1005 --user_pass="123456" --allow-root'

# activate user:
docker exec --user root wpp-website-staging /bin/sh -c 'wp user meta update <username-or-id> pending 0 --allow-root'

# list user metas #
docker exec --user root wpp-website-staging /bin/sh -c 'wp user meta list <username-or-id> --allow-root'
```

## Install a plugin via wp-cli ##

```sh
docker exec --user root wpp-website-local /bin/sh -c 'wp plugin install <plugin-sanitized-name> --allow-root'
```

## Write permission on upload/download folder ##

```sh
sudo chmod -R 777 wordpress/wp-content/uploads
sudo chmod -R 777 wordpress/wp-content/downloaded
```

## Add container to existing network ##

```sh
docker network connect <network-name> <container-name-or-id>
```

## DB backups location in production ##

```sh
/home/ubuntu/wp_backups
```

## Commit container changes to save it as an image ##

```sh
docker commit <container-name-or-id> <new-image-ame>
```

## Manage user data via WP CLI ##

```sh
# inside the docker container
wp user meta update <user-name-or-id> pp_email_verified true --allow-root

# delete user token
wp user meta delete <user-name-or-id> tyk_access_token --allow-root

# delete tyk_last_token_request
wp user meta delete <user-name-or-id> tyk_last_token_request --allow-root
```