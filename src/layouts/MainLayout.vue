<template>
  <q-layout view="lHh Lpr lFf">
    <q-header elevated class="print-hide">
      <q-toolbar v-if="cv !== null">
        <q-btn
          flat
          dense
          round
          icon="menu"
          aria-label="Menu"
          @click="leftDrawerOpen = !leftDrawerOpen"
        />

        <q-toolbar-title class="">
          <span class="">{{ cv.info.title }}</span>
          &nbsp;
          <span class="text-caption">{{ $root.t('myCV') }}</span>
        </q-toolbar-title>

        <q-btn
          flat
          dense
          round
          icon="mdi-file-download-outline"
          aria-label="Download"
          @click="download()"></q-btn>
        <q-btn
          flat
          dense
          round
          icon="print"
          aria-label="Print"
          @click="print()"></q-btn>
        <q-btn
          flat
          dense
          round
          icon="share"
          aria-label="share"
          @click="share()"></q-btn>

        <!-- Language Switcher Dropdown (Frontend) -->
        <q-btn-dropdown
          v-if="$root.rawCV && $root.rawCV.languages && $root.rawCV.languages.length > 1"
          flat
          dense
          no-caps
          color="white"
          :label="currentLangLabel"
          class="q-mr-sm"
        >
          <q-list>
            <q-item
              v-for="l in activeLanguages"
              :key="l.code"
              clickable
              v-close-popup
              @click="changeLanguage(l.code)"
            >
              <q-item-section>
                <q-item-label>{{ l.emoji }} {{ l.label }}</q-item-label>
              </q-item-section>
            </q-item>
          </q-list>
        </q-btn-dropdown>
      </q-toolbar>
    </q-header>

    <q-drawer
      class="print-hide"
      v-model="leftDrawerOpen"
      show-if-above
      bordered
      content-class="bg-grey-1"
    >
      <q-list>
        <q-item-label
          header
          class="text-grey-8"
        >
          {{ $root.t('myCV') }}
        </q-item-label>
        <template v-if="cv">
          <q-list>
            <template v-for="(s, key, sindex) in cv" :key="'section_' + sindex">
              <q-item v-if="s.title && !s.hidden" clickable @click="goto(key)">
                <q-item-section side v-if="s.icon">
                  <q-icon :name="s.icon" size="2em" color="primary"></q-icon>
                </q-item-section>
                <q-item-section>
                  <q-item-label>{{ s.title }}</q-item-label>
                </q-item-section>
              </q-item>
            </template>
          </q-list>
        </template>
      </q-list>
    </q-drawer>

    <q-page-container>
      <router-view/>
    </q-page-container>

    <app-footer />
  </q-layout>
</template>

<script>
import html2pdf from 'html2pdf.js'
import AppFooter from 'components/AppFooter.vue'

export default {
  name: 'MainLayout',
  components: {
    AppFooter
  },
  data () {
    return {
      leftDrawerOpen: false
    }
  },
  computed: {
    cv () {
      return this.$root.cv
    },
    activeLanguages () {
      if (!this.$root.rawCV || !this.$root.rawCV.languages) return []
      return this.$root.languagesList.filter(l => this.$root.rawCV.languages.includes(l.code))
    },
    currentLangLabel () {
      if (!this.$root.languagesList) return ''
      const found = this.$root.languagesList.find(l => l.code === this.$root.currentLang)
      if (!found) return ''
      return this.$q.screen.lt.sm ? found.emoji : `${found.emoji} ${found.label}`
    }
  },
  mounted () {
    mainVue.addRefs('menu', this)
  },
  methods: {
    changeLanguage (code) {
      this.$root.setLanguage(code)
    },
    goto (key) {
      if (mainVue.$q.platform.is.mobile) {
        this.leftDrawerOpen = false
      }
      setTimeout(function () {
        let target = document.querySelector('#' + key)
        // smooth scroll to element and align it at the bottom
        target.scrollIntoView({ behavior: 'smooth', block: 'start' })
      }, 500)
    },
    print () {
      this.leftDrawerOpen = false
      setTimeout(function () {
        window.print()
      }, 500)
    },
    async download () {
      this.leftDrawerOpen = false
      const prefix = this.$root.t('myCV').replaceAll(' ', '_')
      const name = prefix + '_di_' + this.cv.info.title + '_' + this.cv.info.description
      const el = document.querySelector('.q-page')
      html2pdf(el, {
        margin: 0,
        filename: name.replaceAll(' ', '_') + '.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2, useCORS: true, logging: false },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
      })
    },
    share () {
      var text = this.$root.t('shareText') + this.cv.info.title + ', ' + this.cv.info.description + ' ' + location.href
      const self = this
      navigator.clipboard.writeText(text).then(function () {
        console.log('Async: Copying to clipboard was successful!')
        mainVue.$q.notify({
          icon: 'info',
          message: self.$root.t('copySuccess'),
          color: 'positive'
        })
      }, function (err) {
        console.error('Async: Could not copy text: ', err)
        mainVue.$q.notify({
          icon: 'warning',
          message: self.$root.t('copyError') + err,
          color: 'negative'
        })
      })
    }
  }
}
</script>
