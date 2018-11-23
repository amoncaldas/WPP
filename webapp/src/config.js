const config = {
  devBaseAPIUrl: 'http://localhost:5002/wp-json/',
  prodBaseAPIUrl: '/wp-json/',
  mainMenuSlug: 'primary_menu',
  setCustomMenuIcons: true
}

config.getBaseUrl = () => {
  let env = process.env
  return env.NODE_ENV === 'production' ? config.prodBaseAPIUrl : config.devBaseAPIUrl
}

export default config
