# Creating ORS site environment #

## Dump the latest db to the file system ##

```sh
docker exec fam-mysql-local /bin/sh -c "mysqldump -u root -padmin wordpress > fam-dump.sql"
docker cp fam-mysql-local:/fam-dump.sql $PWD/mysql/db-backup.sql
```

## Access STAGING server ##

```sh
sudo ssh -i keys/ssh_ors_pelias.key ubuntu@129.206.7.40
```

## Access remove PRODUCTION server ##

```sh
sudo ssh -i keys/ssh_access.pem ubuntu@129.206.7.180
```

## copy remote wp-content.zip ##

```sh
scp -i keys/ssh_access.pem ubuntu@129.206.7.180:~/page/page-wordpress/wp-content.zip ~/apps/ors
```

## copy remote wp db backup ##

```sh
scp -i keys/ssh_access.pem ubuntu@129.206.7.180:~/wp_backups/go.openrouteservice.org.2018-01-29-0625.tar.gz ~/apps/ors
```

## run docker compose ##

```sh
cd ~/apps/ors
docker-compose -f docker-compose.yml up

# starting already created:
docker-compose -f docker-compose.yml start
```

## Import database from local backup ##

```sh
# import from file `db-backup.sql` in `mysql` folder
docker exec -i ors-mysql-local  mysql -uroot -padmin wordpress < mysql/db-backup.sql
```

## create and import database file in staging ##

```sh
# dump database
docker exec ors-website-mysql-staging /bin/sh -c "mysqldump -u root -padmin wordpress > ors-dump.sql"
docker cp ors-website-mysql-staging:/ors-dump.sql $PWD/ors-dump.sql

# connect and show databases
docker exec ors-website-mysql-staging /bin/sh -c "echo 'show databases' | mysql -uroot -padmin"
# create database
docker exec ors-website-mysql-staging /bin/sh -c "echo 'create database wordpress' | mysql -uroot -padmin"
# import database
docker exec -i ors-website-mysql-staging mysql -uroot -padmin wordpress < mysql/db-backup.sql
```

## create and import database file in production ##

```sh
# connect and show databases
docker exec ors-website-mysql-production /bin/sh -c "echo 'show databases' | mysql -uroot -padmin"
# create database
docker exec ors-website-mysql-production /bin/sh -c "echo 'create database wordpress' | mysql -uroot -padmin"
# import database
docker exec -i ors-website-mysql-staging mysql -uroot -padmin wordpress < mysql/db-backup.sql
```

## backup old and rebuild with restored db in production ##

```sh
# dump database
docker exec ors-website-mysql-production /bin/sh -c "mysqldump -u root -padmin wordpress > ors-dump.sql"
docker cp ors-website-mysql-production:/ors-dump.sql $PWD/ors-dump.sql

# stop docker compose
docker-compose -f master.docker-compose.yml down

# empty database files (to be able to restore backup)
sudo rm -rf ~/ors_web/wordpress/db/*

# move to auto restore location
sudo mv ors-dump.sql ~/ors_web/mysql/db-backup.sql
docker-compose -f master.docker-compose.yml up -d
```

## backup and download database in production ##

```sh
# dump database
docker exec ors-website-mysql-production /bin/sh -c "mysqldump -u root -padmin wordpress > ors-dump.sql"
docker cp ors-website-mysql-production:/ors-dump.sql $PWD/ors-dump.sql

# copy from remote to local
scp -i keys/ssh_access.pem ubuntu@129.206.7.180:~/ors-dump.sql ~/apps/ors-dump.sql
```

## Copy wp-config from container ##

```sh
docker cp 0a0:/var/www/html/wp-config.php $PWD/wp-config.php
```

## User management via wp-cli ##

```sh
# Create a user as admin
docker exec --user root ors-website-staging /bin/sh -c "wp user create amon amon@openrouteservice.org --role=administrator --allow-root"
# get the password printed out!

# update existing user password:
docker exec --user root ors-website-staging /bin/sh -c 'wp user update 1005 --user_pass="123456" --allow-root'

# activate user:
docker exec --user root ors-website-staging /bin/sh -c 'wp user meta update <username-or-id> pending 0 --allow-root'
docker exec --user root ors-website-staging /bin/sh -c 'wp user meta update <username-or-id> pp_email_verified true --allow-root'

# list user metas #
docker exec --user root ors-website-staging /bin/sh -c 'wp user meta list <username-or-id> --allow-root'
```

## Install a plugin via wp-cli ##

```sh
docker exec --user root ors-website-local /bin/sh -c 'wp plugin install easy-modal --allow-root'
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