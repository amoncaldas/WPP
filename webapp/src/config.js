const config = {
  devBaseAPIUrl: 'http://localhost:5003/wp-json/',
  prodBaseAPIUrl: '/wp-json/',
  baseWpApiPath: 'wp/v2/',
  mainMenuSlug: 'primary_menu_',
  secondaryMenu: 'secondary_menu_',
  setCustomMenuIcons: true
}

config.getBaseUrl = () => {
  let env = process.env
  return env.NODE_ENV === 'production' ? config.prodBaseAPIUrl : config.devBaseAPIUrl
}

export default config
