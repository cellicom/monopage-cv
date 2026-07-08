<template>
  <div id="q-app">
    <router-view />
  </div>
</template>

<script>
import { setCssVar } from 'quasar'
import translations from './i18n.js'

const themes = {
  default: {
    primary: '#1976D2',
    secondary: '#26A69A',
    accent: '#ffffff',
    chipBg: '#e0e0e0',
    chipText: '#212121'
  },
  navy: {
    primary: '#1F3864',
    secondary: '#333333',
    accent: '#ffffff',
    chipBg: '#E1EBF5',
    chipText: '#1F3864'
  },
  forest: {
    primary: '#1E4D2B',
    secondary: '#8F754F',
    accent: '#ffffff',
    chipBg: '#E2EDE4',
    chipText: '#1E4D2B'
  },
  burgundy: {
    primary: '#6E1F2E',
    secondary: '#A68A56',
    accent: '#ffffff',
    chipBg: '#F7EBEF',
    chipText: '#6E1F2E'
  },
  teal: {
    primary: '#168F8B',
    secondary: '#4A5568',
    accent: '#ffffff',
    chipBg: '#E2F1F0',
    chipText: '#168F8B'
  },
  slate: {
    primary: '#2D3748',
    secondary: '#DD6B20',
    accent: '#ffffff',
    chipBg: '#EDF2F7',
    chipText: '#2D3748'
  },
  pink: {
    primary: '#E91E63',
    secondary: '#F48FB1',
    accent: '#ffffff',
    chipBg: '#FCE4EC',
    chipText: '#C2185B'
  },
  magenta: {
    primary: '#9C27B0',
    secondary: '#BA68C8',
    accent: '#ffffff',
    chipBg: '#F3E5F5',
    chipText: '#7B1FA2'
  },
  darcula: {
    primary: '#2B2B2B',
    secondary: '#A9B7C6',
    accent: '#FFCB42',
    chipBg: '#3C3F41',
    chipText: '#FFCB42'
  },
  vaporwave: {
    primary: '#FF71CE',
    secondary: '#01CDFE',
    accent: '#ffffff',
    chipBg: '#FFF3FA',
    chipText: '#B967C7'
  }
}

export default {
  name: 'App',
  data () {
    const browserLang = (typeof navigator !== 'undefined' && (navigator.language || navigator.userLanguage) || '').split('-')[0].toLowerCase()
    const defaultLang = ['it', 'en', 'fr', 'de', 'es', 'ru', 'ja', 'zh'].includes(browserLang) ? browserLang : 'en'
    return {
      childRefs: [],
      loadError: false,
      currentLang: defaultLang,
      translations,
      languagesList: [
        { code: 'it', label: 'Italiano', emoji: '🇮🇹' },
        { code: 'en', label: 'English', emoji: '🇬🇧' },
        { code: 'fr', label: 'Français', emoji: '🇫🇷' },
        { code: 'de', label: 'Deutsch', emoji: '🇩🇪' },
        { code: 'es', label: 'Español', emoji: '🇪🇸' },
        { code: 'ru', label: 'Русский', emoji: '🇷🇺' },
        { code: 'ja', label: '日本語', emoji: '🇯🇵' },
        { code: 'zh', label: '中文', emoji: '🇨🇳' }
      ],
      rawCV: {
        languages: ['it'],
        name: '',
        img: '',
        birthDate: '',
        theme: 'default',
        defaultLanguage: null,
        data: {},
        adminPassword: ''
      },
      cv: {
        info: {},
        exp: {},
        skill: {},
        lang: {},
        school: {},
        courses: {},
        managementSkill: {},
        comunicativeSkill: {},
        digitalSkill: {},
        others: {},
        privacy: {}
      }
    }
  },
  created () {
    window.mainVue = this
    this.childRefs = []

    if (this.$q && this.$q.notify) {
      const originalNotify = this.$q.notify.bind(this.$q)
      this.$q.notify = (options) => {
        const opt = typeof options === 'string' ? { message: options } : { ...options }
        const id = Math.random().toString(36).substring(2, 9)
        opt.classes = (opt.classes || '') + ' clickable-toast-' + id
        const dismiss = originalNotify(opt)
        setTimeout(() => {
          const el = document.querySelector('.clickable-toast-' + id)
          if (el) {
            el.style.cursor = 'pointer'
            el.addEventListener('click', () => {
              dismiss()
            })
          }
        }, 100)
        return dismiss
      }
    }

    this.$axios.get('/cv.json')
      .then(response => {
        const raw = response.data
        if (raw && !raw.data) {
          // Automatic migration of legacy flat structure
          const adminPassword = raw.adminPassword || ''
          const langData = { ...raw }
          delete langData.adminPassword
          
          this.rawCV = {
            languages: ['it'],
            name: langData.info ? (langData.info.title || '') : '',
            img: langData.info ? (langData.info.img || '') : '',
            birthDate: '',
            data: {
              it: langData
            },
            adminPassword: adminPassword
          }
          // Remove img/title from the per-lang copy
          if (this.rawCV.data.it && this.rawCV.data.it.info) {
            delete this.rawCV.data.it.info.img
            delete this.rawCV.data.it.info.title
          }
        } else if (raw) {
          this.rawCV = raw
          if (!this.rawCV.languages) {
            this.rawCV.languages = ['it']
          }
          if (!this.rawCV.data) {
            this.rawCV.data = {}
          }
          // Migration: if global fields missing, extract from first lang
          if (!this.rawCV.name && !this.rawCV.img) {
            const firstLang = this.rawCV.languages[0]
            const firstInfo = firstLang && this.rawCV.data[firstLang] && this.rawCV.data[firstLang].info
            if (firstInfo) {
              this.rawCV.name = firstInfo.title || ''
              this.rawCV.img = firstInfo.img || ''
              // Remove from all language copies
              this.rawCV.languages.forEach(l => {
                if (this.rawCV.data[l] && this.rawCV.data[l].info) {
                  delete this.rawCV.data[l].info.title
                  delete this.rawCV.data[l].info.img
                }
              })
            }
          }
          if (this.rawCV.birthDate === undefined) {
            this.rawCV.birthDate = ''
          }
          // Strip any legacy 'Date of birth / Data di nascita' entries from per-lang arrays
          this.rawCV.languages.forEach(l => {
            const info = this.rawCV.data[l] && this.rawCV.data[l].info
            if (info && info.data) {
              info.data = info.data.filter(d => !/(date of birth|data di nascita)/i.test(d.label))
            }
          })
        }

        if (this.rawCV.defaultLanguage && this.rawCV.languages.includes(this.rawCV.defaultLanguage)) {
          this.currentLang = this.rawCV.defaultLanguage
        } else {
          const browserLang = (typeof navigator !== 'undefined' && (navigator.language || navigator.userLanguage) || '').split('-')[0].toLowerCase()
          if (browserLang && this.rawCV.languages.includes(browserLang)) {
            this.currentLang = browserLang
          } else if (this.rawCV.languages.includes('en')) {
            this.currentLang = 'en'
          } else {
            this.currentLang = this.rawCV.languages[0] || 'en'
          }
        }
        if (!this.rawCV.data[this.currentLang]) {
          this.rawCV.data[this.currentLang] = {
            info: { title: '', description: '', about: '', data: [] },
            exp: { title: 'Esperienze', data: [] },
            skill: { title: 'Competenze', data: [] },
            lang: { title: 'Lingue', data: [] },
            school: { title: 'Istruzione', data: [] },
            courses: { title: 'Certificati', data: [] },
            managementSkill: { title: 'Competenze Gestionali', data: [] },
            comunicativeSkill: { title: 'Competenze Comunicative', data: [] },
            digitalSkill: { title: 'Competenze Digitali', data: [] },
            others: { title: 'Informazioni Addizionali', data: [] },
            privacy: { title: 'Privacy', description: '' }
          }
        }
        if (this.rawCV.theme === undefined) {
          this.rawCV.theme = 'default'
        }
        this.applyTheme(this.rawCV.theme)

        this.cv = this.rawCV.data[this.currentLang]
        this.loadError = false
      })
      .catch(err => {
        console.error('Failed to load settings:', err)
        this.loadError = true
      })
  },
  methods: {
    addRefs (name, component) {
      if (typeof this.childRefs !== 'undefined') {
        this.childRefs[name] = component
      }
    },
    setLanguage (lang) {
      if (this.rawCV && this.rawCV.data && this.rawCV.data[lang]) {
        this.currentLang = lang
        this.cv = this.rawCV.data[lang]
      }
    },
    t (key) {
      const dict = this.translations[this.currentLang] || this.translations['it']
      return dict[key] || this.translations['it'][key] || key
    },
    applyTheme (themeKey) {
      const t = themes[themeKey] || themes.default
      setCssVar('primary', t.primary)
      setCssVar('secondary', t.secondary)
      setCssVar('accent', t.accent)
      if (typeof document !== 'undefined') {
        document.documentElement.style.setProperty('--chip-bg-color', t.chipBg)
        document.documentElement.style.setProperty('--chip-text-color', t.chipText)
      }
    }
  }
}
</script>
