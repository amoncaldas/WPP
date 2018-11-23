import menuManager from '@/support/menu-manager'
import appConfig from '@/config'

/**
 * Load the primary menu by its slug defined app config
 * from remote server and then run the local customization over it
 */
const loadItems = () => {
  return new Promise((resolve, reject) => {
    menuManager.getMenu(appConfig.mainMenuSlug).then((menu) => {
      runCustomization(menu)
      resolve(menu)
    })
    .catch(error => {
      console.log(error)
      resolve([])
    })
  })
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

/**
 * Ruj the local customizations over the menu loaded
 *
 * @param {*} menu
 */
const runCustomization = (menu) => {
  menuManager.removeItemEndingWith(menu, '/log-in')
  menuManager.replaceItemEndingWith(menu, '/dev-dashboard', { 'header': 'Dashboard', requiresNotBeAuthenticated: true })
  menuManager.removeItemEndingWith(menu, '?action=logout')
  menu.push({ 'href': '/dev/#/logout', 'title': 'Logout', 'icon': 'power_settings_new', requiresBeAuthenticated: true, showIcon: true })
  menuManager.injectAt(menu, 0, { 'header': 'Links' })
  menuManager.injectAt(menu, 0, { 'href': '/dev/#/', 'title': 'Home', 'icon': 'home', notInHeader: true, external: true })

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
