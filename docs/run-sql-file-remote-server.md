# How to run a local sql file in remote server #

1. Send the file via scp

```sh
# example how to sent to staging server
scp ~/apps/ors/mysql/tyk-user-update.sql ubuntu@129.206.7.40:~/tyk-user-update.sql

# production example:
# sudo scp -i keys/ssh_access.pem ~/apps/ors/mysql/tyk-user-update.sql ubuntu@129.206.7.180:~/tyk-user-update.sql
```

2. Access the remove server via ssh

```sh
# example accessing staging
ssh ubuntu@129.206.7.40
```

3. Copy the file to the mysql container

```sh
# copying file to mysql staging container
docker cp tyk-user-update.sql ors-website-mysql-staging:tyk-user-update.sql
```

4. Go to mysql container on the server

```sh
# example how to to connect to mysql container on staging
docker exec -it ors-website-mysql-staging bash
```

5. Backup the database

```sh
# backing up the staging database
mysqldump -u root -padmin wordpress > ors-dump.sql
```

6. Run the sql file

```sh
# running the sql update on mysql staging container
mysql -u root -padmin wordpress < tyk-user-update.sql
```