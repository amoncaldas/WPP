
import menuService from '@/shared-services/menu-service'
import Vue from 'vue'

/**
 * Get a menu from remove service by its slug
 * As the menu in the back-end are managed, we can not be sure about the
 * id of the menu we want to load, but we expect that the menu has an specific
 * slug. Unfortunately, the menu api does not allow getting a single menu based in its
 * slug. So, we load a list of menus, iterate over them and get the id of the menu
 * that has as slug the slug passed and then make a second request to get the desired menu
 *
 * @param string slug of the menu
 */
const getMenu = (slug) => {
  return new Promise((resolve, reject) => {
    menuService.query()
    .then(resources => {
      if (resources.raw && resources.data) {
        resources = resources.data
      }
      let menuBySlug = Vue.lodash.find(resources, (menu) => {
        return menu.slug === slug
      })
      menuService.get(menuBySlug.term_id)
      .then(resource => {
        resolve(parseMenu(resource.items))
      })
    })
    .catch(error => {
      resolve([])
      console.log(error)
    })
  })
}

/**
 * Parse a collection of menu items to a structure expected locally
 *
 * @param {*} items
 */
const parseMenu = (items) => {
  let menu = []
  Vue.lodash.each(items, (item) => {
    let parsedItem = parseItem(item)
    menu.push(parsedItem)
  })
  return menu
}

/**
 * Parse a menu item to a structure expected locally
 *
 * @param {*} item
 */
const parseItem = (item) => {
  var parser = document.createElement('a')
  // Make sure all themenu items have a single page url
  if (item.url.startsWith('http') && item.url.indexOf('/#/') === -1) {
    parser.href = item.url
    item.url = parser.pathname
  }

  let parsedItem = {
    href: item.url,
    external: isExternalLink(item.url),
    title: item.title,
    icon: 'link',
    notInHeader: false,
    requiresBeAuthenticated: false,
    active: false
  }

  setItemActiveStatus(parsedItem)

  /* If the menu item has children, parse the children too */
  if (item.children && Array.isArray(item.children)) {
    parsedItem.items = []
    Vue.lodash.each(item.children, (child) => {
      parsedItem.items.push(parseItem(child))
    })
  }
  return parsedItem
}

/**
 * Define if the a menu item is active or not
 * @param {*} item
 * @param {} router to object
 */
const setItemActiveStatus = (item, to) => {
  if (item.href !== undefined && item.href !== null) {
    let activeRoute = (to === undefined) ? location.hash.replace('#', '') : to.path
    if (activeRoute.indexOf('?') !== -1) {
      activeRoute = activeRoute.split('?')[0]
    }
    let itemRoute = item.href === '/' ? '/' : item.href.replace('/dev/#', '')

    // initialize as not active
    item.active = false

    // Include customization to set current dashboard main menu item as active
    if (activeRoute === itemRoute) {
      item.active = true
    } else {
      // Include customization to set current dashboard main menu item as active
      if (item.href === '/dev' && (activeRoute === '/login' || activeRoute === '/' || activeRoute === '/home')) {
        item.active = true
      }
    }
  }
}

/**
 * Run over each menu item and set the active status of each one
 * @param {*} menuItems
 * @param {} router to object
 */
const setMenuActiveStatus = (menuItems, to) => {
  Vue.lodash.each(menuItems, (item) => {
    setItemActiveStatus(item, to)
  })
}

/**
 * Replace a menu item by a replacement according its ending href string
 * @param {*} menu
 * @param string itemEnding
 * @param {*} replacement
 */
const replaceItemEndingWith = (menu, itemEnding, replacement) => {
  let replaceItemIndex = Vue.lodash.findIndex(menu, (item) => {
    if (!item.href) return false
    let href = item.href.endsWith('/') ? item.href.substr(0, (item.href.length - 1)) : item.href
    return href && href.endsWith(itemEnding)
  })
  if (replaceItemIndex !== undefined && replaceItemIndex !== null) {
    menu[replaceItemIndex] = replacement
  } else {
    menu.push(replacement)
  }
}

/**
 * Checks if a given url points to an external website
 */
const isExternalLink = (url) => {
  if (url.startsWith('/') || url.startsWith('#')) {
    return false
  }
  let withouProtocol = url.replace('https://').replace('http://')
  if (withouProtocol.startsWith(location.hostname)) {
    return false
  }
  return true
}

/**
 * Replace a menu item by a replacement by its starting href string
 * @param {*} menu
 * @param string itemStart
 * @param {*} replacement
 */
const replaceItemStartingWith = (menu, itemStart, replacement) => {
  let replaceItemIndex = Vue.lodash.findIndex(menu, (item) => {
    if (!item.href) return false
    let href = item.href.replace('http://', '').replace('https://', '')
    return href && href.startsWith(itemStart)
  })
  if (replaceItemIndex > -1) {
    menu[replaceItemIndex] = replacement
  } else {
    menu.push(replacement)
  }
}

/**
 * Inject a menu item before another item, identified by its ending href string
 * @param {*} menu
 * @param {*} beforeItemEnding
 * @param {*} injectItem
 */
const injectBeforeItemEndingWith = (menu, beforeItemEnding, injectItem) => {
  let beforeItemIndex = Vue.lodash.findIndex(menu, (item) => {
    if (!item.href) return false
    let href = item.href.endsWith('/') ? item.href.substr(0, (item.href.length - 1)) : item.href
    return href && href.endsWith(beforeItemEnding)
  })
  if (beforeItemIndex > 1) {
    menu.splice(beforeItemIndex, 0, injectItem)
  }
}

/**
 * Inject a menu item in a given index
 * @param {*} menu
 * @param {*} atIndex
 * @param {*} injectItem
 */
const injectAt = (menu, atIndex, injectItem) => {
  menu.splice(atIndex, 0, injectItem)
}

/**
 * Remove a menu item by its ending href string
 * @param {*} menu
 * @param {*} itemEnding
 */
const removeItemEndingWith = (menu, itemEnding) => {
  let removeItemIndex = Vue.lodash.findIndex(menu, (item) => {
    if (!item.href) return false
    let href = item.href.endsWith('/') ? item.href.substr(0, (item.href.length - 1)) : item.href
    return href && href.endsWith(itemEnding)
  })
  if (removeItemIndex > -1) {
    menu.splice(removeItemIndex, 1)
  }
}

/**
 * Return menu manager
 */
const MenuManager = {
  getMenu,
  replaceItemEndingWith,
  removeItemEndingWith,
  injectBeforeItemEndingWith,
  replaceItemStartingWith,
  injectAt,
  setMenuActiveStatus
}

export default MenuManager
