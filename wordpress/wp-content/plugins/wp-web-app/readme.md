# wP Web App plugin #

This plugin adds custom behaviors based on the Wp Web App needs. It relies on the existence of other plugins, like JWTAuthentication (they must be installed and active)

This plugins is responsible for:

- wordpress, jwt-authentication-for-wp-rest-api and profilepress and pp-mailchimp plugins behavior customization
- adding filters and actions regarding authentication and wp rest api returning data
- adding custom wp api endpoints for api doc, ors business data and user registration and password recovery

## Login via oauth providers ####

This plugins is responsible for processing the back-end stuff related to oauth authentication. Currently only github is supported.

*Important:* this plugin is intended to be used in conjunction with [jwt-authentication-for-wp-rest-api](https://wordpress.org/plugins/jwt-authentication-for-wp-rest-api/). It does depend (considering the code) on it, but it only allows the authentication and token generation, but it does not intercept subsequent requests to handle the already authenticated user case, which is done by the `jwt-authentication-for-wp-rest-api` plugin.

The flow works as following:

1. the user clicks on github button on the login page.
1. it is made a request a request to the back-end to retrieve the oauth provider `clientId` and set it to the corresponding provider `clientId` property. This and all others oauth authenticate requests are taken on the back-end by the custom `ors-oauth` plugin that we have created.
1. We call the custom `social-auth` authenticate function. It opens the corresponding social provider login pop-up using the `vue-authenticate` component and the `clientId` defined in the previous step and allows the user to authenticate on the service.
1. When it is finished, the component will resolves the promise and brings back a temporary `code`. We send this `code` to the back-end and there, the `ors-oauth` plugin, using this code we will get a token and then we use this token to get the user data, including the user email registered on the provider.
1. Still on the back-end, having the user's email, we locate the user account on wordpress and generate a standard `JWT token`.
1. In the response, we give back the oauth provider token  and an additional property named `user`, where we put the `JWT token` and other user's data. These data are used by the the clients to continue the authentication process.