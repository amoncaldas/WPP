import Vue from 'vue'
import VueI18n from 'vue-i18n'
import enUS from './en-us/all'
import ptBR from './pt-br/all'
import loader from '@/support/loader'
import appConfig from '@/config/config'

const get = () => {
  const i18n = {
    locale: appConfig.defaultLocale, // set locale
    messages: {
      'en-us': enUS,
      'pt-br': ptBR
    },
    fallbackLocale: 'en-us'
  }

  // load and get all EN messages from components *i18n.en.js default export using custom loader
  let enComponentMessages = loader.load(require.context('@/pages/', true, /\.i18n\.en\.js$/), true)
  addComponentKeys('en-us', enComponentMessages, i18n)

  // load and get all EN messages from shared parts *i18n.en.js default export using custom loader
  let enSharedPartsMessages = loader.load(require.context('@/fragments/', true, /\.i18n\.en\.js$/), true)
  addComponentKeys('en-us', enSharedPartsMessages, i18n)

  // load and get all DE messages from components *i18n.de.js default export using custom loader
  let deComponentMessages = loader.load(require.context('@/pages/', true, /\.i18n\.pt-br\.js$/), true)
  addComponentKeys('pt-br', deComponentMessages, i18n)

  // load and get all EN messages from shared parts *i18n.en.js default export using custom loader
  let deSharedPartsMessages = loader.load(require.context('@/fragments/', true, /\.i18n\.pt-br\.js$/), true)
  addComponentKeys('pt-br', deSharedPartsMessages, i18n)

  Vue.use(VueI18n)

  return new VueI18n(i18n)
}

export default {
  get
}

function addComponentKeys (languageKey, deSharedPartsMessages, i18nObj) {
  for (let messages in deSharedPartsMessages) {
    let translations = deSharedPartsMessages[messages]
    for (var key in translations) {
      // skip loop if the property is from prototype
      if (!translations.hasOwnProperty(key)) continue
      i18nObj.messages[languageKey][key] = translations[key]
    }
  }
}
