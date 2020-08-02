const config = {
  devBaseAPIUrl: 'https://fam.eco/wp-json/',
  prodBaseAPIUrl: '/wp-json/',
  baseWpApiPath: 'wp/v2/',
  mainMenuPrefix: 'primary_menu_',
  secondaryMenuPrefix: 'secondary_menu_',
  orsApiKey: '5b3ce3597851110001cf624858626cdecd4f4ffd8bc346b827384e12',
  setCustomMenuIcons: true
}

config.getBaseUrl = () => {
  let env = process.env
  return env.NODE_ENV === 'production' ? config.prodBaseAPIUrl : config.devBaseAPIUrl
}

export default config
