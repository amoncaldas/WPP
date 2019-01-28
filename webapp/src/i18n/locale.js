import Vue from 'vue'
import VueI18n from 'vue-i18n'
import enUS from './en-us/all'
import ptBR from './pt-br/all'
import loader from '@/support/loader'
import appConfig from '@/config'

Vue.use(VueI18n)

const i18n = {
  locale: appConfig.defaultLocale.defaultLocale, // set locale
  messages: {
    'en-us': enUS,
    'pt-br': ptBR
  },
  fallbackLocale: 'en-us'
}

// load and get all EN messages from components *i18n.en.js default export using custom loader
let enComponentMessages = loader.load(require.context('@/pages/', true, /\.i18n\.en\.js$/), true)
addComponentKeys('en-us', enComponentMessages)

// load and get all EN messages from core *i18n.en.js default export using custom loader
let enCoreMessages = loader.load(require.context('@/core/', true, /\.i18n\.en\.js$/), true)
addComponentKeys('en-us', enCoreMessages)

// load and get all EN messages from shared parts *i18n.en.js default export using custom loader
let enSharedPartsMessages = loader.load(require.context('@/fragments/', true, /\.i18n\.en\.js$/), true)
addComponentKeys('en-us', enSharedPartsMessages)

// load and get all DE messages from components *i18n.de.js default export using custom loader
let deComponentMessages = loader.load(require.context('@/pages/', true, /\.i18n\.pt-br\.js$/), true)
addComponentKeys('pt-br', deComponentMessages)

// load and get all EN messages from core *i18n.en.js default export using custom loader
let deCoreMessages = loader.load(require.context('@/core/', true, /\.i18n\.pt-br\.js$/), true)
addComponentKeys('pt-br', deCoreMessages)

// load and get all EN messages from shared parts *i18n.en.js default export using custom loader
let deSharedPartsMessages = loader.load(require.context('@/fragments/', true, /\.i18n\.pt-br\.js$/), true)
addComponentKeys('pt-br', deSharedPartsMessages)

export default new VueI18n(i18n)

function addComponentKeys (languageKey, i18nObject) {
  for (let messages in i18nObject) {
    let translations = i18nObject[messages]
    for (var key in translations) {
      // skip loop if the property is from prototype
      if (!translations.hasOwnProperty(key)) continue
      i18n.messages[languageKey][key] = translations[key]
    }
  }
}
