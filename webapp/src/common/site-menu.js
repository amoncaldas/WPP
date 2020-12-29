import menuManager from '@/support/menu-manager'
import appConfig from '@/config/config'
import store from '@/store/store'

/**
 * Load the primary menu by its slug defined app config
 * from remote server and then run the local customization over it
 */
const loadItems = (menuSlugPrefix) => {
  return new Promise((resolve) => {
    let menuSlug = menuSlugPrefix + store.getters.locale
    menuManager.getMenu(menuSlug).then((menu) => {
      runCustomization(menu)
      resolve(menu)
    })
    .catch(error => {
      console.log(error)
      resolve([])
    })
  })
}

/**
 * Run the local customizations over the menu loaded
 *
 * @param {*} menu
 */
const runCustomization = (menu) => {
  menuManager.injectAt(menu, 0, { 'header': 'Menu' })

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
    }

    if (item.items) {
      setIcons(item.items)
    }
  })
}

/**
 * Return Main menu
 */
const SiteMenu = {
  loadItems
}

export default SiteMenu
