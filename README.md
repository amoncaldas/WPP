# Docker Wordpress and webapp #

Basic structure for a docker-wordpress/webapp infrastructure that encompasses a Wordpress installation with a Single Page Application under the /dev url.

## Sections ##

- [Running locally](#running-locally)
- [Features](#features)
- [Highlighted features](#highlighted-features)
  - [Auto update WordPress URL](#auto-update-wordpress-url)
  - [Decoupled front-end](#decoupled-front-end)
  - [Contact form](#contact-form)
  - [Visitor dashboard](#visitor-dashboard)
  - [Map component](#map-component)
  - [Members](#members)
  - [Image slider and gallery](#image-slider-and-gallery)
  - [Site sections](#site-sections)
- [WPP plugins](#wpp-plugins)
- [Customization and updates](#customization-and-updates)
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

- Custom docker image based in wordpress:5.3-php7.1-apache
- Custom docker image based in mysql:5.7 with auto dump import
- wp-config.php mapped to an external file
- wp-content folder mapped to an external folder
- Customization and updates via update.sh and wp-cli
- Decoupled front-end with Vuejs and SPA
- Auto fix wordpress url to match the one defined in the docker-compose yml file
- Auto update state via wp-cli

## Highlighted features ##

Some important features are described in this section

### Auto update WordPress URL ###

WordPress store in db absolute urls and therefore we check if the url defined in the docker-compose yml file matches the one stored by WordPress. If not, we update it automatically. Like this we can run the same project and db in multiples environments, like `local`, `staging` and `production`. See [https://codex.wordpress.org/Changing_The_Site_URL](https://codex.wordpress.org/Changing_The_Site_URL)

### Decoupled front-end ###

This solution includes a decoupled front-end dashboard built using VueJS and a custom base front-end application. The front-end communicates with the wordpress rest json api (including the custom endpoints created by the [custom plugins](#custom-plugins)) to get and send data.

### Contact form ###

This solution includes a decoupled front-end contact form with captcha and the back-end services to process it

### Visitor dashboard ###

This solution includes a decoupled front-end visitor dashboard and the back-end services to process it

### Map component ###

This solution includes a map component. Several map can be created on the admin to display a place, list of places and static routes. The icon of the place can be customized and each place displayed on the map can be linked to a content and is navigable (clickable)

### Image slider and gallery ###

This solution includes a slider component and a gallery component

### Members ###

This solution includes member component that allows creating members and listing them on any page or post. It is also possible to link a member to a user/author

### Site sections ###

This solution includes a component that allows creating/editing sections. Each section can have its appearance customized and content can be created under it.


See more about the front-end on the [Front-end app readme](webapp/README.md)

## Wpp plugin ##

Some custom plugins were created and added to wordpress to achieve the desired functionalities. They are:

1. `wpp` - This plugin is custom wpp solution and is intended to contain customizations to WordPress hooks/events, custom rest-api endpoints as well as customize third parties plugins that are supposed to be installed.
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
1. Check if everything is as desired accessing the staging url
1. Deploy to **production** server by committing in the *master* branch (the Gitlab CI will install it on the production server)

**Important:** If CI integration is set up the updater is automatically ran by the CI integration during the deploy to **staging** and **production** server

**Important:** the auto update does not run locally. See [Running locally](#running-locally)

## Running locally the back end ##

To run the project locally, go to the project root folder and run:

```sh
docker-compose -f local.docker-compose.yml up -d
```

**Important:** when all the services are ready, run

```sh
docker exec wpp-website-local /bin/sh -c 'cd wp-content && sh update.sh'
```

## Debug ##

 Check the [how to debug readme](docs/debug-wordpress.md) to set up the php debug for the php code.

## Deploy ##

The project deployment can be done via git push/pull or via continuous integration with Gitlab CI (rename the `gitlab-ci-deactiveated.yml` to `gitlabp-ci.yml` and adjust the credentials). In the second case, when you push a commit to the `staging` branch the changes in this branch will be deployed to the STAGING server and when you push a commit to the `master` branch the changes in this branch will be deployed to the MASTER server.

Remember that if you make changed on the dashboard app, if is necessary to build it locally before commit/deploy. Check the [build-and-deploy app section](webapp/README.md#build-and-deploy) to understand more.

## Options ##

All WPP Options starts with `wpp_` prefix, except:

- git_hub_client_secret
- recaptcha_secret

Options with `wpp_` prefix that are not present by default:

- wpp_signup_with_github
- wpp_meta_*
- wpp_meta_name_*
- archive_sections_sidebar
- wpp_meta_theme-color
- wpp_search_title_translation
- hide_developedby
- wpp_slider_transition - for transition options see: https://deulos.github.io/vue-flux/
- wpp_site_title_translations
- wpp_site_description_translations
- wpp_search_title_translation
- wpp_use_and_data_policy_url_<locale> (like wpp_use_and_data_policy_url_en-us)

## Custom post type supports used on back-end ##

- parent_section (if the post type must have a parent section)
- section_in_permalink (if the parent section must appear in the permalink)
- notification (if the content must generate a notification)
- feed (if the content must be listed in feed)
- auto_related (if the post type must be listed as related to other posts automatically, based on section and categories)

## ACF extras ##

Possible post extras:

- no_link (boolean)
- resizable (boolean)
- target_blank (boolean)
- not_searchable (boolean)
- medias
- custom_link (string/url)
- imported_id (integer)
- available (boolean)
- available_at (date, format `Ymd`)
- has_places (boolean)
- sponsored (boolean)
- places (array of posts)
- has_route (boolean)
- polyline (textarea/string representing a pplyline array)
- hide_author_bio (boolean) hide author bio in single mode
- show_sidebar (boolean)
- sidebar_post_types (array of strings)
- max_in_side_bar (integer)
- custom_post_date (date, format `Ymd`)
- prepend (integer, adding prepend post id)
- append (integer, adding append post id)
- hide_newsletter_sidebar (boolean)
- tile_provider_id (`osm`, `satellite` or `cycling`)
- zoom (a value from `1` to `18`)

Only for pages:

- has_top_highlighted
- has_middle_highlighted
- has_bottom_highlighted
- highlighted_top_title
- highlighted_middle
- highlighted_bottom
- highlighted_top
- highlighted_middle_title
- highlighted_bottom_title

Possible section extras:

- has_image_slides (boolean)
- html_content (text/html)
- list_posts (boolean)
- list_post_endpoints (array of strings)
- compact_list_posts (boolean)
- compact_list_post_endpoints (array of strings)
- set_custom_appearance (boolean)
- bg_image (string/url)
- bg_color (html color string)
- bg_repeat (css repeat string)
- bg_position (css repeat string)
- has_places (boolean)
- places (array of posts)
- has_section_map (boolean)
- max_listing_posts (integer)
- not_listed (boolean)
- has_top_highlighted
- has_middle_highlighted
- has_bottom_highlighted
- highlighted_top_title
- highlighted_middle
- highlighted_bottom
- highlighted_top
- highlighted_middle_title
- highlighted_bottom_title

Attachment extras:

- video_url (message string)

Place extras:

- location (OpenStreetMaps field)
- is_country (boolean)
- flag (image array)

Follower extras:

- email (string)
- ip (string)
- user_agent (string)
- activated (boolean)
- mail_list (string)

*Date ACF fields must have the return format `Ymd` to be correctly displayed*

## Admin credentials ##

username: wppadmin
pass: T9y6jGS3C9^7#VvNYXzAMuw#
