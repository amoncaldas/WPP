import store from '@/store/store'
import main from '@/main'

const showMenuItem = (item) => {
  // a custom show menu item function can be passed
  // if passed, use the custom one. If not, use the default
  if (this.showMenuItemFn) {
    return this.showMenuItemFn(item)
  } else {
    if (store.getters.isAuthenticated) {
      return !item.requiresNotBeAuthenticated
    } else {
      return !item.requiresBeAuthenticated
    }
  }
}
const navigate = (to) => {
  if (to.href !== '#') {
    if (to.external) {
      window.open(to.href, '_blank')
    } else {
      let VueInstance = main.getInstance()
      VueInstance.routeToLink(to.href)
    }
  }
}

const getHref = (to) => {
  return to.href
}

const isItemActive = (item) => {
  item.active = false
  let activeRoute = location.hash.replace('#', '')
  let itemRoute = item.href.replace('/dev/#', '')
  if (activeRoute === itemRoute || (item.href === '/dev' && (activeRoute === '/login' || activeRoute === '/'))) {
    item.active = true
  }
  return item.active
}

export {showMenuItem}
export {navigate}
export {isItemActive}
export {getHref}
