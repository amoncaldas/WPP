const config = {
  devBaseAPIUrl: 'http://localhost/wp-json/',
  prodBaseAPIUrl: '/wp-json/',
  baseWpApiPath: 'wp/v2/',
  mainMenuPrefix: 'primary_menu_',
  secondaryMenuPrefix: 'secondary_menu_',
  setCustomMenuIcons: true
}

config.getBaseUrl = () => {
  let env = process.env
  return env.NODE_ENV === 'production' ? config.prodBaseAPIUrl : config.devBaseAPIUrl
}

export default config
