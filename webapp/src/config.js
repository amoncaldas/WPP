const config = {
  devBaseAPIUrl: 'http://localhost:5002/wp-json/',
  prodBaseAPIUrl: '/wp-json/',
  devBaseAdminAjaxUrl: 'http://localhost:5002/wp-admin/admin-ajax.php',
  prodBaseAdminAjaxUrl: '/wp-admin/admin-ajax.php',
  primaryMenuSlug: 'primary_menu',
  setCustomMenuIcons: true,
  replaceSimpleServicesMeniItemForCollection: false,

  // Reset password  ProfilePress plugin config
  resetAction: 'pp_ajax_passwordreset',
  passwordResetFormId: 10,
  resetUserLoginKey: 'user_login',
  passwordResetFormKey: 'passwordreset_form_id'
}

config.getBaseUrl = () => {
  let env = process.env
  return env.NODE_ENV === 'production' ? config.prodBaseAPIUrl : config.devBaseAPIUrl
}

export default config
