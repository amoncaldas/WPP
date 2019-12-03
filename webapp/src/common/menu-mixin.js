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
  var regex = new RegExp('/', 'g')
  let cleanHref = to.href.replace(regex, '')
  let locationParts = cleanHref.split('/')

  let lastPathPart = locationParts[locationParts.length - 1]
  let sectionsRoutes = store.getters.sectionsRoutes

  //  Pages must be opened in a way that the whole app is reloaded
  // we identify a page yrl by checking if does not end with number and is not a section path
  if (to.external || (isNaN(lastPathPart) && !sectionsRoutes.includes(to.href))) {
    window.location = to.href
  } else {
    let VueInstance = main.getInstance()
    VueInstance.$router.push({path: to.href})
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
