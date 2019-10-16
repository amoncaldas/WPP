# WPP Front-end #

This application encompasses two main needs:

- Creation of a base Vue SPA with common features/components to be used in multiple front-end apps

The base application is built using VueJS, vuetify and a set of custom components, directives and services. The created structure allows the creation of specific needs to be contained in a feature folder, following a feature-by-folder
approach. So, all the business specific logic for the developer's dashboard are contained in the src/page's folder.
This app uses single file components and others non native javascript code that are transpilled to native javascript during the build process. That is way this needs to be compiled before run, either in dev mode or production mode. This VueJS single file components allows a better code organization, week and clear coupling between components and an easier code understanding.

## Sections ##

- [Set up and run locally](#set-up-and-run-locally)
- [Features](#features)
  - [Core/CRUD](#core-crud)
  - [Feature-by-folder functionality with auto loaders](#feature-by-folder-functionality-with-auto-loaders)
  - [Authentication and authorization](#authentication-and-authorization)
  - [Registration](#registration)
- [Architecture and structure](#architecture-and-structure)
- [Dashboard pages](#dashboard-pages)
- [Debug](#debug)
- [Build and deploy](#build-and-deploy)

## Set up and run locally ##

To run the application it is needed to install all the webapp specific dependencies first. These dependencies are managed/installed using npm/nodejs, which is by default installed in the docker image/container created when you first run the local.docker-compose.yml file. So, to run the app in dev mode (having the docker-compose running) execute:

```sh
# Don't forget that it is needed to run first `docker-compose -f local.docker-compose.yml up -d` as described in the main README.md
# in the very first run and every time you pull from the repository:
docker exec ors-website-local /bin/sh -c 'cd /var/www/webapp && npm install && npm run build'
# After the (re)build it will be accessible via localhost:<port>/dev
```

To be able to run the webapp in dev mode, as a standalone app, using a node http server, it is necessary to install node in your local dev machine. To do so, run:

```sh
curl -sL https://deb.nodesource.com/setup_6.x | bash - && \
apt-get update && \
apt-get install -y nodejs && \
npm install -g npm && \
npm update -g
```

Then, go to the dashboard app root folder (normally `<project-root-folder>`/webapp), and run:

```sh
npm run dev
# This will start a standalone http node server and the host and port to access it will be displayed
```

*Important:* be aware that the GitHub oauth feature is linked to a [GitHub app](https://developer.github.com/apps/building-oauth-apps/authorizing-oauth-apps/#web-application-flow) . The current app created on GitHub to be used locally points to the whole app solution, which is http://localhost:5002/. This url is accessible when you run the `docker-compose -f local.docker-compose.yml up -d` command and you can access the dashboard webapp pointing your browser to http://localhost:5002/dev.

If you run the dashboard webapp in dev/standalone mode, it runs on the url `http://localhost:8080` and then the Github callback url will mismatch the one configured in the github app (which is http://localhost:5002 ). So, if you need to use the GitHub oauth feature running the webapp in dev mode you must:

1. create a [new GitHub app](https://github.com/settings/applications/new) and register the fields `Homepage URL` and `Authorization callback URL` with the value `http://localhost:8080`.
1. using the new GitHub app data change the GitHub `Client ID` and `Client Secret` on the [local ProfilePress Wordpress admin](http://localhost:5002/wp-admin/admin.php?page=pp-social-login).

## Features ##

What is ready for use and included in the base Vue SPA:

- Base app structure, including header, footer, box, and sidebar
- Custom components/fragments
  - `box` (a component to be used as a default wrapper for pages and other components)
  - `charts` (Chart-vue-js configured charts ready to be used)
  - `date-picker` (encapsulated date-picker component, with translations and options to be used easily)
  - `dialogs/confirm` dialog
  - `footer` fragment
  - `h-menu` - horizontal multi-level menu
  - `header` fragment (which incorporates the horizontal menu and sidebar fragments)
  - `v-menu` - vertical multi-level menu
  - `sidebar` fragment (which incorporates the vertical menu)
  - `toaster` (to notify the user with nice overflow corner modal)
  - `welcome` (small component to show hi + user name, when logged in)
- Feature-by-folder functionality with auto loaders
- `Model service` - representation of back-end repository to retrieve/send data from/to back-end
- `CRUD` actions, with complete flow to create/destroy/update/get/index resources from back-end
- `Toaster` to show messages to the user
- `Transitions/animations` when a route changes
- `Authentication via JWT`
- Filters
  - `capitalize`
  - `uppercase`
- Directives
  - `bg.js` (to background color to component using theme color identifier or html color)
  - `top-border` (to add an small border to the element using theme color identifier or html color)

### Core/CRUD ###

The generic crud solution allows the communication with a back-end api with minimum code. You just need to define the endpoint of a resource and add the curd to a component by instantiating it to a vue.js component. You can also use directly a model instance or the model service to retrieve/send data not implementing the crud behaviors in your VueJS component/page. The solution is composed of four main classes/files:

- [core/crud.js](#src/core/crud.js)
- [core/form.js](#src/core/form.js)
- [core/model-service.js](#src/core/model-service.js)
- [core/model.js](#src/core/model.js)

#### Model Service ####

Model service class that allows the running of REST api actions in a remote server for a defined endpoint resource.
It is intended to be used in conjunction with the Model class @see @/core/model to read more

Params:

1. @param string - `endPoint` the relative url of the resource
1. @param string - `resourceName` the resource name (used to build the default confirmation messages)
1. @param {} options - `optional` options that allows to customize the model service behavior

The options object may contain the following attributes:

- `transformRequest` (function): executed before the request is made. Useful to change data in special circumstances.
    This function will receive an object with the endpoint and filters (when available) attributes. It is not intended to replace the axios
    request interceptor!
- `transformResponse` (function): executed after the request is made, passing the original response object received.
    Useful if it necessary to modify the data returned before transforming them in a Model instance
- `raw` (boolean): defines if the default transformation of the results into Model instances must be skipped.
    If it is true, the active record will not work with the returned items. Useful when you just want to get data, and not destroy/update them
- `pk` (string): overwrites the default primary key attribute, that is 'id'. Use it if your model on the remote server uses a different field as primary key

How to create a model service to represent a resource in the back-end:

```js
// file my-model-service.js

import ModelService from '@/core/model-service'

let options = {
  pk: 'my-pk-field-name' // necessary only if different of `id`
}
const myModelService = new ModelService('relative/url/to/resource/endpoint', 'My resource nice name', options)

export default myModelService
```

After this my-model-service.js file is created, you can import it anywhere in the app, and use the following methods:

- `getName` - returns the model nice name

- `getEndPoint` - returns the endpoint defined for the model service

- `query(filters)` - receives an array of query filters and returns a promise, that when resolved, passes a collection of resources

- `customQuery(customOptions, endPoint)` - receives an endpoint, an array of query filters and an object with custom options and returns a promise, that when resolved, passes a collection of resources. The customOptions allows the overwrite of the instance options only for the executed request. The `customOptions` and `endPoint` parameters are optional and will be replaced by the instance options endpoint if are null. The following options attributes can defined:
  - `query` (object): containing key -> value attributes to be used as query string)
  - `data` (object): containing key -> value attributes to be used as post data)
  - `verb` (string): verb to be used - default is 'get'
  - `transformRequest`: function to be called back on transformRequest event

- `get(pkValue)` - get a resource identified by its primary key value

Example of model service usage:

```js
import myModelService from './my-model-service'

export default {
  created () {
    // Get the available policies (type of tokens) and store on the data tokenType's key
    myModelService.query().then((models) => {
      this.myModels = models
      // by default, each model is an instance of Model @/core/model
      // having the $save, $update, $destroy methods
    })
  }
}
```

#### Form validation ####

The crud solution uses the form.js to validate a form before submitting the form. But, it is possible to disable this by passing the `skipFormValidation:true` in the options object passed to the crud constructor. Is the default behavior is on, the slution will look for a form reference, in your component context, named `form` *(like vm.$refs.form, where vm is the component context passed to the crud)*. Iy is also possible to specify a alternative form ref name, by setting the `formRef:<my-form-ref-name>(string)` in the options object passed to the constructor of crud.


It is also possible to use the form validation apart from the crud component. You just have to import it, create a new instance passing:

- the `formRef` object,
- the `context` object (the component **this**)
- the optional `options` object.

 Then, just run the `validate` method. It will run the default form object passed in the formRef validation and also check for the `required` attribute in each input and validate it. If any field is invalid, it will highlight it, set the `valid` status as false and also add a string to the inputs the error bucket using input label and crud translations for `required`.

#### Adding CRUD functionalities to a component ####

The CRUD class allows to add common extended CRUD actions (get, index, save, update, destroy)
to a component that uses the RESTFull API pattern. It is intended to be used in conjunction with the class ModelService (required by the constructor)

This crud class implements the full cycle to get and send data to/from a remote server, including before destroy confirmation dialog,
refresh listed data after save, destroy and update and success and confirmation messages.

EXPORTS: this javascript module exports two objects: CRUDData and CRUD.

The crud setter expects the following parameters:

1. @param {} `vm` - the component instance, that can be passed using `this`
1. @param {} `modelService`  - an instance of the ModelService class representing the service that provides the data service to a resource. @see @/core/model-service
1. @param {} `options` - object with optional parameters that allows to customize the CRUD behavior

The options object may contain the following attributes:

- `queryOnStartup` (boolean): if the index action must be ran on the first CRUD run
- `indexFailedMsg` (string): custom message to be displayed on index action failure
- `getFailedMsg` (string): custom message to be displayed on get single item action failure
- `saveFailedMsg` (string): custom message to be displayed on save action failure
- `updatedMsg` (string): custom message to be displayed on update action failure
- `confirmDestroyTitle` (string): custom title to be displayed on the confirm dialog shown before destroy action
- `confirmDestroyText` (string): custom text to be displayed on the confirm dialog shown before destroy action
- `destroyedMsg` (string): custom message to be displayed after an resource has been destroyed
- `destroyFailedMsg` (string): custom message to be displayed on destroy action failure
- `destroyAbortedMsg` (string): custom message to be displayed when a destroy is aborted
- `skipFormValidation` (boolean): skips the auto form validation
- `skipFormValidation` (boolean): skips the auto form validation
- `skipAutoIndexAfterAllEvents` (boolean) : skips the auto resources reload after data change events (update, destroy and save)
- `skipAutoIndexAfterSave` (boolean) : skips the auto resources reload after save
- `skipAutoIndexAfterUpdate` (boolean) : skips the auto resources reload after update
- `skipAutoIndexAfterDestroy` (boolean) : skips the auto resources reload after destroy
- `skipServerMessages` (boolean) : skip using server returned message and use only front end messages do display toasters
- `skipShowValidationMsg` (boolean) : skit showing the validation error message via toaster when a form is invalid
- `formRef` (string, optional) : the alternative name of the form ref you are using in the template. Used to auto validate the form. If not provided, it is assumed that the form ref name is `form`
- `[http-error-status-code-number]` : defines the message to be used when an http error status code is returned by a request (only available fot status code from `300` to `505`)

Example of adding CRUD to a component:

```js
import {CRUD, CRUDData} from '@/core/crud'
import myModelService from './my-model-service'

// Then, inside your default export

export default {
  data: () => ({
    // create the crud data objects (resource, resources and modelService) using three dots notation
    ...CRUDData
  })

  // The second one must be used to instantiate the crud class on the vue created event, like this:
  created () {
    // extend this component, adding CRUD functionalities
    let options = {...}
    CRUD.set(this, myModelService, options)
  }
}
```

A toast  message is shown after each action using the following priority: server response message,
custom message specified in options or the default one (defined @crud/i18n/crud.i18n.en.js)

Crud events optional functions:

If the vue `component` to which you are adding the CRUD has one of the following defined methods, it is gonna be called by the CRUD. If it returns false, the execution will be rejected and stopped

- `beforeIndex`
- `beforeGet`
- `beforeSave`
- `beforeUpdate`
- `beforeDestroy`
- `beforeShowError`

If the vue `component` to which you are adding the CRUD has one of the following defined methods, it is gonna be called by the CRUD passing the related data

- `afterIndex`
- `afterGet`
- `afterSave`
- `afterUpdate`
- `afterDestroy`
- `afterError`

Form validation:
If the vue `component` to which you are adding the CRUD has a `$ref` named `form` and it does not have the option `skipFormValidation` defined as `true`, the auto form validation will be ran before saving and updating.

### Authentication and authorization ###

A custom authentication/authorization feature is included in the app. The logic related to this is mainly placed inside the `/pages/auth`.

Every time the app runs, it is checked if an user is already authenticated. If yes, s/he is redirected to the `/home`. The `/home` page has a `route guard` defined, so if the user tries to go direct to it and s/he is not authenticated, s/he will be redirected to `/`, where s/he can log in. If you want to add a guard to you page, just add the following code in the page/component route file:

```js
import store from '@/store/store'
import MyPage from '@/pages/my-page/MyPage'

export default {
  path: '/my-page-path',
  name: 'My page nice name',
  component: MyPage,
  beforeEnter: (to, from, next) => {
    if (store.getters.isAuthenticated) {
      next() // if is authenticated, continue to the target route
    } else {
      next({name: 'Login'}) // if not, redirect to the login page
    }
  }
}
```

#### Login via username or email and password ####

The user enters his/her user name or e-mail and it is sent to the back-end, where the [jwt-authentication-for-wp-rest-api](https://wordpress.org/plugins/jwt-authentication-for-wp-rest-api/) plugin will take the request,
verify if the user exists and generate a [JWT token](https://jwt.io/) based on it and return this token, that will be stored on the browser local storage and added to each subsequent request.
As we are using the [vuex](https://vuex.vuejs.org/) store concept/component in VueJS, this token is stored/retrieved using the `store` approach defined in [@/pages/auth/auth.store.js](src/pages/auth/auth.store.js).
To do this we have created a request interceptor in our [@/common/http-api.js](src/common/http-api.js) definition.

##### Authentication flow steps #####

1. the user enters her/his username/email and password and hit `login`.
1. the credentials are sent to the back-end and taken by the jwt-authentication-for-wp-rest-api plugin
1. the plugin tries checks if the credentials are valid and if so, builds JWT token and send it back with the user id.
1. the JWT token is added automatically in the Authorization header in each future request
1. the [@/pages/home/tabs/profile/profile.js](src/pages/home/tabs/profile/profile.js) uses the script [@/pages/home/tabs/profile/user-service.js](src/pages/home/tabs/profile/user-service.js) to make a request to `wp-json/wp/v2/users/:user-id` and retrieve all the user data.
1. having the user data, the `hi user name` is displayed.

*Important:*

The [jwt-authentication-for-wp-rest-api](https://wordpress.org/plugins/jwt-authentication-for-wp-rest-api/) plugin will intercept each request made to wp rest api and as a first step. It will check if the request contains a Authorization header and, if so, it will tries to authenticate the user using the token contained in the header. The token is reversed to an object and it is verified if the token is not malformed, is not expired and, if so, it automatically defines the current logged user based on it.
After that, the original request continues and wordpress will behave considering an user is logged in.

Example:

1. the client makes a request to `/wp-json/tyk-api/v1/tokens`
1. the `jwt-authentication-for-wp-rest-api` plugin is first processed and tries to set the logged user if the header contains a token
1. the original request is passed to `/wp-json/tyk-api/v1/tokens`
1. `tyk plugin` retrieves the tokens considering the logged user

Endpoints:

- The endpoint to authenticate a user (sending username/email and password) is defined at [@/pages/auth/auth.js](src/pages/auth/auth.js)
- The endpoint to retrieve the user data based on the token is defined at [@/pages/home/tabs/profile/user-service.js](src/pages/home/tabs/profile/user-service.js).

#### Login via github oauth ####

The app also includes an authentication via github, using the oauth github service and the third party VueJS [vue-authenticate](https://github.com/dgrubelic/vue-authenticate) component.

The flow works as following:

1. the user clicks on github button on the login page. The `socialAuth.oauthViaRedirect` method is fired, passing the provider to be used and the `action` desired. In this case the action is `login`.
1. we clear the local storage and save the desired action in our vuex store with the key `socialOauthAction`.
1. a request is made to the back-end passing the `provider` and the `action` to retrieve the oauth provider `clientId` and set it to the corresponding provider `clientId` and get the oauth redirect url from the providerConf via the `getOauthUrl` method. The provider id is also stored in our vuex store with the key `socialOauthProvider`.  The above mentioned request and all others oauth authenticate requests are taken on the back-end by the custom `wpp` plugin that we have created.
1. the user is redirected to the corresponding social oauth provider and when s/he finishes the authentication there s/he is redirected back to our app root route with the `code` query string value. (eg.: `/?code=xyz12345`).
1. In the router a `beforeEnter` function is attached to the `/` route. So, the `socialAuth.runOauthCallBackCheck` is run to handle the redirection from a oauth provider.
1. the `socialAuth.runOauthCallBackCheck` method checks if the `code` query string is present. If it is,it saves it in the vuex store with the key `socialOauthCode` and redirects the user to the route/component linked to the action. For example, the route to the `login` action will load the `Auth.vue` component.
1. The `Auth.vue` component is loaded and on the `created` event runs the `socialAuth.checkAndProceedOAuth`, that asynchronously returns the userData from the back-end. On the back-end, after exchanging the `code` by a token and then the token by the the github user data the WordPress user is located by the github email attribute and a `JWT token` is generate and returned. After receiving the response the `auth.setUserAndRedirect` is run as a callback by the `socialAuth.checkAndProceedOAuth` and receives the context and the userData returned asynchronously and finally the user is authenticated and redirected to the home.

**Important:** the `socialOauthProvider`, `socialOauthProvider` and `socialOauthCode` are stored using a defined vuex store in the @/pages/auth/auth.store.js and internally this storage is saved in the browser local storage, so it is kept across requests/redirections and it is intentional because this oauth is based in a redirection flow and if we didn't do that that current state of the authentication would be lost.

**Important:** on the step 6, the method `runOauthCallBackCheck`, using the `socialOauthAction` stored in the step `2` defined the target endpoint to the request to be used. For `login` action is `/wp-json/wpp/v1/oauth/github/login` and for `signup` action is `/wp-json/wpp/v1/oauth/github/signup`.

### Registration ###

#### Registration via form filling ####

This is the standard flow to the user register her/his self.

- the user must fill the required fields (marked with a `*`) and be identified by the invisible captcha as a human. In case the captcha can't identify the user as a human it will ask the user two answer interactive questions.
- the username and the user mail are validated as the user types it. So, if one of then are not valid (for example, already taken) the user will be notified by a input bottom message and by a right corner error icon.
- the password and password confirmation must be identical.
- the user must hit the `send` button.

#### Registration via github oauth ####

The app also includes an registration via github, using the oauth github service and the third party VueJS [vue-authenticate](https://github.com/dgrubelic/vue-authenticate) component. The registration flow is very similar to the [Login via github oauth](#login-via-github-oauth) flow. The differences are in the following steps:

- 1. the user must click on the "Sign up with GitHub" button on the `/#/signup` page and the action passed is `signup`, instead of `login`.
- 6. the the component where the application will be routed to is the `Signup.vue`
- 7. the `Signup.vue` component is loaded and on the `created` event it runs the the `socialAuth.checkAndProceedOAuth`. Internally, using the `socialOauthAction` stored in the step `2` a different endpoint receives the request (in this case `/wp-json/wpp/v1/oauth/github/signup`) and the back-end will register a new user. At this point two ways are possible:
  - if the user already exists (checked by the e-mail), the user will be logged in and redirected to the `home` component.
  - if it does not exists it will be registered and the request will return the user data, the vuex store `login` event will be dispatch and finally the application will be routed to the `home` component with the profile tab active.

#### Email validation ####

If the user succeed in creating a new account via form filling s/he will receive an e-mail asking for an ownership e-mail validation by clicking in the provided link. In the case of account creation via github, no e-mail is sent because we only allows the account creation if the github account has a already verified email. Clicking in the provided link will lead the user to activate his/her account and then redirect him/her to the dashboard login page.

## Architecture and structure ##

This app uses single file components and predefined folders where the business code should be put.

The app scaffold has the following structure:

- `assets` - where the static assets, like images should be put.
- `common` - where application wide scripts exports should be put.
- `core` - where the model, crud and crud service solution are.
- `directives` - where custom directives should be put.
- `filters` - where custom filters should be put.
- `fragments` - where all the partial components, like menu component, footer etc should be put.
  - Inside the fragments folder there is a sub-folder called `forms` where form components must be stored.
  - **highlighted components**:
    - `user` is a form component that is used in user registration and user profile update.
- `i18n` - where the lang/culture resources and the lang loader resides (each page or fragment can have its own  i18n files)
- `pages` - where the app pages that are rendered based in a route should be put. The structure and files of a page inside the pages folder is:
  - my-page-name (folder)
    - MyPageName.vue (main VueJS component file)
    - my-page-name.css (styles for the page, included by the MyPageName.vue component)
    - my-page-name`.store.js` (Vuex store module for the page, included by the store/store.js loader)
    - my-page-name`.route.js` (route to reach this page, included by the router/index loader)
    - i18n (folder)
      - my-page-name`.i18n.en.js` (containing the EN lang resources for the page)
- `router` - main router folder with index.js router file that loads all additional routes in the `pages` folder
- `store` - main store folder with and modules folder and store.js file that loads all additional routes in the `pages` folder
- `support` - folder where support files, like the loader lib and some other services are put.
- `App.vue` - root VueJS component, that included the header, menu, footer and a `<router-view></router-view>` where the page will be loaded depending on the route fired.
- `app.js` - root VueJS script file. This js file is included by the App.vue component.
- `app.scss` - root VueJS scss file where sass and css styles are be put.
- `config.js` - app config file, where we put global definitions, like rest api base url.
- `main.js` - main js file and the first one loaded, where we load the Vue app, its dependencies and the libs used in the whole app.

The app load cycle follow these steps:

1. Execute the `main.js` file and add global extensions, mixins  components and external libs.
1. The `main.js` also includes the main router script, the main vuex store and the main i18n file, that will internally, each one, load all the additional `.router.js` files, `.store.js` files and `.i18n..js` files.
1. `Main.js` file will create a VueJS app instance and load the `App.vue`.
1. `App.vue` includes all basic navigation components, like menu, sidebar, footer and etc.
1. As soon as all the routes are loaded, including the ones in the `pages` sub folder, the page with the `/` or `/home` route will also be rendered in the `<router-view></router-view>` in `App.vue` component, considering authentication state.

### Reserved methods and accessor ###

All the VueJS components created (including the fragments) will have, by default, the following methods/accessors define din the main vue instance app:

- `showMessage (msg, theme, options)` - shows a message using the toaster with specified theme and options

- `showError (msg, options)` - shows an error message using the toaster with the specified options

- `showWarning (msg, options)` - shows an warning message using the toaster with the specified options

- `showInfo (msg, options)` - shows an info message using the toaster with the specified options

- `showSuccess (msg, options)` - shows an success message using the toaster with the specified options

- `confirmDialog (title, text, options)` - shows an confirm dialog with the specified title, text and options and returns a promise. If the user clicks on `yes`, the promise will be resolved, if s/he clicks on `no`, the promise will be rejected.

- `eventBus` - accessor to global event bus object, that allows to broadcast and get events in all components

- `$http` - accessor to custom wrapped axios http client, encapsulating authentication and loading bar status

- `$store` - accessor to app store that used vuex

- `lodash` - accessor to lodash lib, useful for manipulate arrays an objects.

## Dashboard ##

### Pages ###

- `activate` - the page land when the user clicks in the activation link sent to the email. It gets the data from the url and and run a request to the back-end to activate the user account. If the user id is wrong or the activation code is not valid any more, it will show a corresponding error.
- `api-docs` - the API documentation parser and interface builder that creates the interactive documentation playground, allowing a user to explore all the API endpoints, resources and responses on the fly, live. This page is composed of several sub-components, specialized services and adapters (responsible for adapting a provided API doc for the interface builder). Among others, this page uses the `@/fragments/ors-map` and `@/fragments/ors-table` components
- `Auth` - page where the authentication component resides
- `Home` - page where the user is redirected to after login. This page contains a tab component, with two tabs:
  - `tokens` - where the user token a listed and where s/he can remove/create tokens and also see the token usage and quota
  - `profile` - where the user can see and edit his/her own profile, including password. It uses the `@/fragments/forms/User.vue` form component.
- `password` a folder that contains the password request and reset components
  - `request` - allow the user to request a password reset link
  - `reset` - allow the user to redefine the password after clicking the email link sent after requesting a password reset.
- `signup` - where the user can create an account filling the form or via single-click using a github account. It uses the `@/fragments/forms/User.vue` form component.

### Menu ###

The menu displayed in the header and in the sidebar (low resolution and mobile devices) is loaded from the back-end server and adjusted to be show according the app status (user authenticated or not).
The menu items retrieval is fetched by the on the `created` event of the `@/fragments/Header` component. So, it dispatch the store `fetchMainMenu` and the menu is retrieved by the `@/common/main-menu.js` that internally uses the `@/support/menu-manager.js` and the `@/support/model-service.js`. Once the items form the back-end are loaded, they are treated to add/remove custom items and define sidebar items icons in the `@/common/main-menu.js`.

## Debug ##

To debug the application you must run it in `dev` mode (so you need to read the [set up and run locally](#set-up-and-run-locally). After run the app in dev mode it is recommended to use the Chrome browser and instal the [VueJS devtools](https://chrome.google.com/webstore/detail/vuejs-devtools/nhdogjmejiglipccpnnnanhbledajbpd?hl=en) extension.
After doing that, open the application in the browser and press F12 and select the tab `Console`, `Vue` or `Sources` (and then expand `webpack://src`), according the need.

## Build and deploy ##

The dashboard app must be built before it is deployed to `staging` or `master` branch. To do so, run:

```sh
cd <project-root-folder>/webapp
npm run build
# then point your browser to localhost:<port>/dev
```

We adopted this approach because the build process takes a considerable time and the app may be not available during the build. As we want to minimize the time the app may be kept unavailable, we decided to add the production/output files that are result of the build to the repository/git and commit/push the app ready to run.

For a detailed explanation on how webpack works, check out the [guide](http://vuejs-templates.github.io/webpack/) and [docs for vue-loader](http://vuejs.github.io/vue-loader).
