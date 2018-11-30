# Steps to run a local sql file in remote server #

1. Send the file via scp

```sh
# example how to sent to staging server
scp ~/local-folder/sql-file-name.sql user@server:~/sql-file-name.sql

# production example:
# sudo scp -i keys/ssh_access.pem ~/apps/ors/mysql/tyk-user-update.sql ubuntu@129.206.7.180:~/tyk-user-update.sql
```

1. Access the remove server via ssh

```sh
# example accessing staging
ssh user@server
```

1. Copy the file to the mysql container

```sh
# copying file to mysql staging container
docker cp sql-file-name.sql fam-mysql-staging:sql-file-name.sql
```

1. Go to mysql container on the server

```sh
# example how to to connect to mysql container on staging
docker exec -it ors-mysql-staging bash
```

1. Backup the database

```sh
# backing up the staging database
mysqldump -u root -padmin wordpress > fam-dump.sql
```

1. Run the sql file

```sh
# running the sql update on mysql staging container
mysql -u root -padmin wordpress < sql-file-name.sql
```