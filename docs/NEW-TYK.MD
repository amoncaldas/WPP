/** Tyk.io setup */
define( 'TYK_CONFIGURATION', 'on-premise' ); // the tyk setup you're using ('cloud', 'on-premise' or 'hybrid')
define( 'TYK_API_ENDPOINT', 'http://129.206.5.107:3000/api' ); // or the url to your Tyk installation
define( 'TYK_API_KEY', '9a21f102cb52458a55de9357a0bf8d13' ); // access token you created for the management user
define( 'TYK_AUTO_APPROVE_KEY_REQUESTS', true ); // read more below // these are only needed when TYK_CONFIGURATION is 'on-premise' or 'hybrid'
define( 'TYK_GATEWAY_URL', 'http://129.206.7.231/tyk' ); // the url of your tyk gateway
define( 'TYK_GATEWAY_SECRET', '352d20ee67be67f6340b4c0605b044b7' ); // the gateway secret // optional
define( 'TYK_FORCE_DISABLE_BOOTSTRAP', true ); // do not include bootstrap styles