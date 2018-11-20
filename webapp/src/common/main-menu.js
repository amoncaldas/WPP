import menuManager from '@/support/menu-manager'
import appConfig from '@/config'

/** Local menu item definition **/

const menuHeader = { 'header': 'Links' }

const homeMenuItem = {
  'href': '/dev/#/', 'title': 'Home', 'icon': 'home', notInHeader: true, external: true
}

const loginMenuItem = {
  'title': 'Access',
  'icon': 'lock',
  startOpen: true,
  requiresNotBeAuthenticated: true,
  items: [
    { 'href': '/dev/#/signup', 'title': 'Sign up', 'icon': 'assignment', external: true, requiresNotBeAuthenticated: true },
    { 'href': '/dev/#/login', 'title': 'Log in', 'icon': 'lock' }
  ]
}

const logoutMenuItem = {
  'href': '/dev/#/logout', 'title': 'Logout', 'icon': 'power_settings_new', requiresBeAuthenticated: true, showIcon: true
}

const dashboardMenuItem = {
  'header': 'Dashboard', requiresNotBeAuthenticated: true
}

const servicesMenuItem = {
  'title': 'Services',
  'icon': 'domain',
  'group': 'pages',
  'items': [
    { 'href': '/services', 'title': 'All services', icon: 'domain', external: true },
    { 'href': '/directions', 'title': 'Directions', icon: 'directions', external: true },
    { 'href': '/geocoding', 'title': 'Geocoding', icon: 'location_searching', external: true },
    { 'href': '/locations', 'title': 'Locations', icon: 'location_on', external: true },
    { 'href': '/isochrones', 'title': 'Isochrones', icon: 'graphic_eq', external: true }
  ]
}

/**
 * This is the default menu used in case of fail to load the menu from remote server
 */
const fallBackMenu = [
  menuHeader,
  homeMenuItem,
  servicesMenuItem,
  { divider: true },
  { 'header': 'Resources' },
  { 'href': '/news', 'title': 'News', 'icon': 'library_books', external: true },
  { 'href': '/maps', 'title': 'Maps', 'icon': 'map', external: true },
  { 'href': '/plans', 'title': 'Plans', 'icon': 'payment', external: true },
  { 'href': '/apis', 'title': 'APIs', 'icon': 'cloud', external: true },
  { divider: true, requiresNotBeAuthenticated: true },
  { 'header': 'Dashboard', requiresNotBeAuthenticated: true },
  loginMenuItem,
  { divider: true, requiresBeAuthenticated: true },
  logoutMenuItem
]

/* Methods starts here */

/**
 * Load the primary menu by its slug defined app config
 * from remote server and then run the local customization over it
 */
const loadItems = () => {
  return new Promise((resolve, reject) => {
    menuManager.getMenu(appConfig.primaryMenuSlug).then((menu) => {
      // if an empty menu was returned, use the fall back one
      if (menu.length === 0) {
        resolve(fallBackMenu)
      }
      // before resolve, we run the local customizations
      // hidden items, setting default icons and so on
      runCustomization(menu)
      resolve(menu)
    })
    .catch(error => {
      console.log(error)
      resolve(fallBackMenu)
    })
  })
}

/**
 * Ruj the local customizations over the menu loaded
 *
 * @param {*} menu
 */
const runCustomization = (menu) => {
  menuManager.removeItemEndingWith(menu, '/log-in')
  menuManager.replaceItemEndingWith(menu, '/dev-dashboard', dashboardMenuItem)
  menuManager.removeItemEndingWith(menu, '?action=logout')
  menu.push(logoutMenuItem)
  menuManager.injectAt(menu, 0, menuHeader)
  menuManager.injectAt(menu, 0, homeMenuItem)

  /* replace the simple /service menu by a container with services and other 4 subitems */
  if (appConfig.replaceSimpleServicesMeniItemForCollection === true) {
    menuManager.replaceItemEndingWith(menu, '/services', servicesMenuItem)
  }

  // replace the signup item for a custom one that requires a non authenticated user to be displayed
  menuManager.replaceItemEndingWith(menu, '/signup', loginMenuItem.items[0])

  // we can control if the custom icons are applied in the app config
  if (appConfig.setCustomMenuIcons === true) {
    setIcons(menu)
  }
}

/**
 * Set the default icons based in the menu item's url endpoint
 *
 * @param {*} items
 */
const setIcons = (items) => {
  items.forEach(item => {
    if (!item.href) {
      return
    }

    let href = item.href.endsWith('/') ? item.href.substr(0, (item.href.length - 1)) : item.href
    let hrefEnding = href.substr(href.lastIndexOf('/'))

    switch (hrefEnding) {
      case '/':
        item.icon = 'home'
        break
      case '/services':
        item.icon = 'domain'
        break
      case '/directions':
        item.icon = 'directions'
        break
      case '/geocoding':
        item.icon = 'location_searching'
        break
      case '/locations':
        item.icon = 'location_on'
        break
      case '/isochrones':
        item.icon = 'graphic_eq'
        break
      case '/news':
        item.icon = 'library_books'
        break
      case '/maps':
        item.icon = 'map'
        break
      case '/plans':
        item.icon = 'payment'
        break
      case '/documentation':
        item.icon = 'cloud'
        break
    }

    if (item.items) {
      setIcons(item.items)
    }
  })
}

/**
 * Return Main menu
 */
const MainMenu = {
  loadItems
}

export default MainMenu
