/**
 * Set user data and redirect to home
 * @param {*} context
 * @param {*} userData
 */
const setUserAndRedirect = (context, userData, onAuthenticate) => {
  userData = parseUserData(userData)
  context.$store.dispatch('login', userData)
  .then((user) => {
    if (onAuthenticate) {
      onAuthenticate()
    }
  }).catch(error => {
    console.log('login error', error)
  })
}

/**
 * Parse a returned user data object to a local expected format
 *
 * @param {*} userData
 */
const parseUserData = (userData) => {
  // set the default display name
  userData.displayName = userData.user_display_name

  // if display name is empty, tries to get from user nice name
  if (!userData.displayName) {
    userData.displayName = userData.user_nicename
  }

  // in the end, if still empty, use the user's email as display name
  if (!userData.displayName) {
    userData.displayName = userData.user_email
  }

  // set the user email in the appropriate/expected local attribute
  userData.userEmail = userData.user_email

  // set the user id
  userData.id = userData.id

  // return user data
  return userData
}

let auth = {
  setUserAndRedirect,
  parseUserData
}

export default auth
