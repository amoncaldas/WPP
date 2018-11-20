# How is the flow to request a list of tokens #

1. The first step is get the user tokens stored in the local wordpress database. This is done using the get_user_meta wp function.

2. The second step is to get the user tyk id stored locally, linked to the wordpress user, like "5b56e86be2e2f90001b28c95". This tyk user id is used to make a request to http://129.206.5.107:3000/api/portal/developers/`<tyk_user_id>` to get the developer data.  This developer data has the "subscription" attribute, which contains a list of hashs.

3. It is checked if each token's hash got in the first step is present in the list of hashs as attribute of the developer data returned in the step 2. If is present, the token "is_valid" attribute is set as true, otherwise is set as false

4. For each valid token is made a request to GET http://129.206.7.231/tyk/keys/`<token-key>` to get a response that is expected to contain an object with the followings attributes:  "quota_remaining" and "quota_max"

## How to test the requests ##

To start the process it is necessary to have the `tyk_user_id` (stored in the WP database) and the list of user `tokens`, that is also stored locally and contains, each one, the `key` attribute. so, to do so it is necessary to go wordpress admin, search the user by email and locate the `tyk_user_id`.

To get the `tyk_user_id` and the user `tyk_access_token` object run, in the cloud front production server:

```sh
docker exec --user root ors-website-production-c /bin/sh -c 'wp user meta list <user-email> --allow-root'
# check the output for the keys `tyk_user_id` and `tyk_access_token`
```

The `tyk_user_id` is an alphanumeric string and the `tyk_access_token` must be an array as the following example:

```json
[
    {
        "api_id":"59db8ee0af295d0001a052f8",
        "token_name":"http:localhost/velovod.ru",
        "hash":"40373d9c",
        "key":"58d904a497c67e00015b45fc4b14714dc73144db8235395b958e1b3e"
    }
]
```


1. GET http://129.206.5.107:3000/api/portal/developers/`<tyk_user_id>` **(brings an object representing the developer user, having an attribute named `subscriptions` that contains a list of token's hash) linked to the user**
1. For each token,  GET http://129.206.7.231/tyk/keys/`<token-key>` **(brings the token usage data)**


