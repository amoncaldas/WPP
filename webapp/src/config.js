const config = {
  devBaseAPIUrl: 'http://localhost:5002/wp-json/',
  prodBaseAPIUrl: '/wp-json/',
  baseWpApiPath: 'wp/v2/',
  mainMenuSlug: 'primary_menu_',
  secondaryMenu: 'secondary_menu_',
  setCustomMenuIcons: true,
  defaultLocale: 'pt-br',
  validLocales: ['en-us', 'pt-br']
}

config.getBaseUrl = () => {
  let env = process.env
  return env.NODE_ENV === 'production' ? config.prodBaseAPIUrl : config.devBaseAPIUrl
}

export default config
