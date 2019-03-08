# Docker Wordpress and webapp #

Basic structure for a docker-wordpress/webapp infrastructure that encompasses a Wordpress installation with a Single Page Application under the /dev url.

## Sections ##

- [Running locally](#running-locally)
- [Features](#features)
- [Highlighted features](#highlighted-features)
  - [Auto update WordPress URL](#auto-update-wordpress-url)
  - [Decoupled front-end dashboard](#decoupled-front-end-dashboard)
- [WPP plugins](#wpp-plugins)
- [Customization and updates](#customization-and-updates)
- [Continuous integration](#continuous-integration)
- [Setup environment](#setup-environment)
- [Debug](#debug)
- [Deploy](#deploy)
- [Options](#options)

## Running locally ##

1. First, make sure you have the requirements installed according [Setup environment](#setup-environment).

1. Clone the project from the [gitlab repository](git@gitlab.com:amoncaldas/wpp.git)

1. Go to the project root folder and run:

    ```sh
    # wordpress create files programmatically and therefore it needs the permissions to do that.
    sudo chmod 777 -R wordpress/wp-content

    # run the docker compose solution
    docker-compose -f local.docker-compose.yml up -d

    # then, when the docker-compose build and all the services are ready, run:
    docker exec wpp-website-local /bin/sh -c 'cd wp-content && sh update.sh'
    # important: it is necessary to be in the right folder, like `wp-content`,
    # so the wp-cli can use the right context
    ```

## Features ##

- Custom docker image based in wordpress:php7.1-apache
- Custom docker image based in mysql:5.7 with auto dump import
- wp-config.php mapped to an external file
- wp-content folder mapped to an external folder
- Customization and updates via update.sh and wp-cli
- Mapped /dev url to run /var/www/webapp/index.html
- Decoupled front-end dashboard with Vuejs
- Auto fix wordpress url to mach the one defined in the docker-compose yml file
- Continues integration with GitLab to deploy branches staging and master
- Auto update state via wp-cli

 The url /dev will point to a future SPA containing examples and developer dashboard page

## Highlighted features ##

Some important features are described in this section

### Auto update WordPress URL ###

WordPress store in db absolute urls and therefore we check if the url defined in the docker-compose yml file matches the one stored by WordPress. If not, we update it automatically. Like this we can run the same project and db in multiples environments, like `local`, `staging` and `production`. See [https://codex.wordpress.org/Changing_The_Site_URL](https://codex.wordpress.org/Changing_The_Site_URL)

### Decoupled front-end dashboard ###

This solution includes a decoupled front-end dashboard built using VueJS and a custom base front-end application. The front-end communicates with the wordpress rest json api (including the custom endpoints created by the [custom plugins](#custom-plugins)) to get and send data.

See more about it in the [Dashboard app readme](webapp/README.md)

## Wpp plugins ##

Some custom plugins were created and added to wordpress to achieve the desired functionalities. They are:

1. `wp-web-app` - This plugin is custom wpp solution and is intended to contain customizations to WordPress hooks/events, custom rest-api endpoints as well as customize third parties plugins that are supposed to be installed.
    - It registers custom wp api end-points related to user registration and custom wpp data regarding the business logic, like sectors and usernames available (in `wp-api` folder). The **dashboard app uses these endpoints** to communicate with the back-end during user registration and profile update.
    - It also register custom actions for wordpress rest_api_init  to customize the response and return custom user data as well as custom error messages when the user api is called. In addition it also adds a filter to the jwt_auth_token_before_dispatch event thrown by the `jwt-authentication-for-wp-rest-api` plugin (in includes/users.php).

## Customization and updates ##

We keep a file in the `wp-content` mapped volume/folder named `update.sh`. This file is responsible for update the current installation, including anything related to wordpress world, like plugins, themes and etc and the stuff related to the decoupled front-end(s). This update.sh file is an ordinary shell script file and any linux command can be executed there, considering we are inside the docker-container.

To manage and update the WordPress stuff we use the pre-installed WP CLI tool that allows to run several tasks in a programmatic way in a wordpress installation, like add, activate and delete plugins, add users, change user's password, update options, change menu and so on. check the full list at [WP CLI commands](https://developer.wordpress.org/cli/commands/)

Example to add and activate a plug-in:

1. Open the file **wordpress/wp-content/update.sh** in the local installation and add the desired command, like:

    ```sh
    wp plugin install <plugin-name> --activate --allow-root
    # --allow-root is always necessary because inside the container the command is been ran as root
    ```
    **Important:** the existing lines must be kept to keep the correct state. If a plugin is already installed, it will not be changed.

1. Run the updater locally:

    ```sh
    # important: it is necessary to be in the right folder, like wp-content, so the wp-cli can use the right context
    docker exec wpp-website-local /bin/sh -c 'cd wp-content && sh update.sh'
    ```

1. Commit in the **develop** branch (this branch has no deploy CI integration)

**Important:** the updater is automatically ran by the CI integration during the deploy to **staging** and **production** server

**Important:** the auto update does not run locally. See [Running locally](#running-locally)

## Continuous integration ##

The entire solution has a continuous integration setup using the gitlab-ci. The tasks related to the CI are defined in the [.gitlab-ci.yml](#.gitlab-ci.yml). The basic idea is: when a commit to specific branches occur, a set of actions are triggered automatically, including:

- Connect to the target server and run git pull on the linked branch to update the local files on the target server
- Run the `docker exec <target-container-name> /bin/sh -c 'cd wp-content && sh update.sh'`.

The followings triggers are configured:

- Deploy to **staging** server by committing in the *STAGING* branch (the Gitlab CI will install it on the staging server)
- Check if everything is as desired accessing the staging instance at [http://129.206.7.40:8081](http://129.206.7.40:8081)

- Deploy to **production** server by committing in the *MASTER* branch (the Gitlab CI will install it on the production server)
- Check if everything is as desired accessing the production server at [http://openrouteservice.org](http:/openrouteservice.org)

**Important:** as the triggers will run automatically **you have to be sure about what you are committing**, specially to the master branch.

**Important:** if changes in the decoupled front-end are made, you have to build a new version of the front-end and then run git add/commit/push. To make sure that the update of staging/production environment is as fast as possible, it was decided that the front-end builds should be run locally and then pushed. The build output files of the front-end are versioned, so they are part of the repository.

## Setup environment ##

 To install `docker`, `docker-compose` and `docker-machine` you can use the setup-docker.sh script in the scripts folder.

 ```sh
 # give it execution permission
 sudo chmod +x ./scripts/setup-docker.sh

 # run the script
 ./scripts/setup-docker.sh
 ```

 If you encounter any problems with the installation script, you can still open the `setup-docker.sh` and run every command line for line.

 If you run into a `docker-compose Version ... is not supported` error upon running the project, try to install a newer docker-composer version:

 ```sh
 # remove current `docker-compose`
 which docker-compose
 # e.g /usr/local/bin/docker-compose
 sudo rm /usr/local/bin/docker-compose

 # install newer docker-compose version
 sudo curl -L https://github.com/docker/compose/releases/download/1.20.0/docker-compose-`uname -s`-`uname -m` -o /usr/bin/docker-compose

 # give it execution permission
 sudo chmod +x /usr/bin/docker-compose
 ```

 Should docker not be able to get the `wp-cli.phar` from the remote source you have enable this for docker:

 ```sh
 # create daemon.json file
 sudo touch /etc/docker/daemon.json

 # edit the file with a editor e.g. vim
 sudo vim /etc/docker/daemon.json
 ```

 In here you need to insert your DNS addresses in the following format:

 ```json
 {
    "dns": ["your-primary-dns-ip", "your-secondary-dns-ip"]
 }
 ```

 You can look up your DNS addresses for example with the `nmcli` command. (DNS configuration: servers: IPv4 IPv6)

 Afterwards restart the docker container: `service docker restart`

 If you need to run the web-application in production or development mode:

 ```sh
 # access the docker container
 docker exec -it wpp-website-local bash

 # go to folder
 cd ../webapp

 # install npm and run build (or dev for development mode)
 npm install
 npm run build
 ```

## Auto update theme post meta urls ##

In the Pursuit theme functions.php it was implemented some functions that verifies and updates the theme specific post meta values
that points to absolute image urls. So, when the SITE_URL is changed on a *docker-compose.yml, the image urls will be updated so they are displayed correctly running the site in any domain/ip/port. This is a Wordpress/Pursuit theme bug and we had to implement this fix to be able to run the same solution in multiple environments (local, staging, production)

## Auto update state via WP CLI ##

WP CLI allows to run several tasks in a programmatic way in a wordpress installation, like add, activate and delete plugins, add users, change user's password, update options, change menu and so on. check the full list at [WP CLI commands](https://developer.wordpress.org/cli/commands/)

Example to add and activate a plug-in:

1. Open the file **wordpress/wp-content/update.sh** in the local installation and add a line, like:

    ```sh
    wp plugin install <plugin-name> --activate --allow-root
    # --allow-root is always necessary because inside the container the command is been ran as root
    ```
    **Important:** the existing lines must be kept to keep the correct state. If a plugin is already installed, it will not be changed.

1. Run the updater locally:

    ```sh
    docker exec wpp-website-local /bin/sh -c 'cd wp-content && sh update.sh'
    ```

1. Commit in the **develop** branch (this branch has no deploy CI integration)

1. Deploy to **staging** server by committing in the *staging* branch (the Gitlab CI will install it on the staging server)
1. Check if everything is as desired accessing the staging instance at [http://129.206.7.40:8081](http://129.206.7.40:8081)
1. Deploy to **production** server by committing in the *master* branch (the Gitlab CI will install it on the production server)

**Important:** the updater is automatically ran by the CI integration during the deploy to **staging** and **production** server

**Important:** the auto update does not run locally. See [Running locally](#running-locally)

## Running locally ##

To run the project locally, go to the project root folder and run:

```sh
docker-compose -f local.docker-compose.yml up -d
```

**Important:** when all the services are ready, run

```sh
docker exec wpp-website-local /bin/sh -c 'cd wp-content && sh update.sh'
```

## Debug ##

 Check the [how to debug readme](docs/debug.md) to set up the php debug for the php code.

## Deploy ##

The project deployment is done via continuous integration with Gitlab CI. So, when you push a commit to the `staging` branch the changes in this branch will be deployed to the STAGING server and when you push a commit to the `master` branch the changes in this branch will be deployed to the MASTER server.

Remember that if you make changed on the dashboard app, if is necessary to build it locally before commit/deploy. Check the [build-and-deploy app section](webapp/README.md#build-and-deploy) to understand more.

Check the [Continuous integration](#continuous-integration) section to see the details.

## Options ##

Options without `wpp_` prefix:

- git_hub_client_secret
- recaptcha_secret

Options with `wpp_` prefix that are not mandatory:

- signup_with_github
