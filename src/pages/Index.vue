<template>
  <q-page class="flex items-start justify-center">
    <div v-if="$root.loadError" class="q-pa-md row justify-center items-center" style="min-height: 80vh;">
      <q-card class="my-card text-center q-pa-lg shadow-5" style="max-width: 400px; border-radius: 12px;">
        <q-card-section>
          <q-icon name="warning" color="warning" size="4rem" />
          <div class="text-h5 q-mt-md text-weight-bold">{{ $root.t('settingsNotFound') }}</div>
          <div class="text-subtitle2 q-mt-sm text-grey-8">
            {{ $root.t('couldNotLoadSettings') }}
          </div>
        </q-card-section>

        <q-card-actions align="center">
          <q-btn
            :label="$root.t('goToAdmin')"
            color="primary"
            to="/admin"
            class="full-width q-py-sm text-bold"
            unelevated
          />
        </q-card-actions>
      </q-card>
    </div>
    <template v-else>
      <!-- header -->
    <template v-if="!empty(cv.info)">
      <div class="full-width bg-primary text-accent" id="info">
        <div class="row q-my-md">
          <div class="col-sm-7 col-12">
            <q-list>
              <q-item>
                <q-item-section side v-if="!empty($root.rawCV.img)">
                  <q-avatar size="150px">
                    <img :src="$root.rawCV.img">
                  </q-avatar>
                </q-item-section>
                <q-item-section>
                  <q-item-label>
                    <h1 class="text-h3 text-weight-medium q-my-none">{{ $root.rawCV.name }}</h1>
                  </q-item-label>
                </q-item-section>
              </q-item>
              <q-item>
                <q-item-section>
                  <q-item-label>
                    <h2 class="text-h6 q-my-none">{{ cv.info.description }}</h2>
                  </q-item-label>
                </q-item-section>
              </q-item>
              <q-item>
                <q-item-section>
                  <q-item-label>
                    <h3 class="text-caption q-my-none">{{ cv.info.about }}</h3>
                  </q-item-label>
                </q-item-section>
              </q-item>
            </q-list>
          </div>
          <!-- info -->
          <template v-if="!empty(cv.info.data)">
          <div :class="['col-sm-5 col-12', $q.platform.is.mobile ? 'border-top-white' : 'border-left-white']">
            <q-list class="">
                <q-item v-if="$root.rawCV.birthDate">
                  <q-item-section>
                    <q-item-label class="text-bold">{{ $root.t('birthDateLabel') }}</q-item-label>
                    <q-item-label class="text-white text-caption">{{ $root.rawCV.birthDate }}</q-item-label>
                  </q-item-section>
                </q-item>
                <template v-for="(i, iindex) in cv.info.data" :key="'info_' + iindex">
                  <q-item v-if="!i.hidden" :clickable="isClickable(i)" class="" @click="openURLCallback(i)">
                    <q-item-section>
                      <q-item-label class="text-bold">{{ i.label }}</q-item-label>
                      <q-item-label class="text-white text-caption">
                        {{ i.value }}
                      </q-item-label>
                    </q-item-section>
                  </q-item>
                </template>
            </q-list>
          </div>
          </template>
          <!-- end info -->
        </div>
      </div>
    </template>
    <!-- end header -->

    <!-- Esperienze -->
    <template v-if="!empty(cv.exp)">
      <div class="full-width" id="exp">
        <div :class="['row q-my-md', $q.platform.is.mobile ? '' : 'border-bottom-grey']">
            <div class="full-width text-h6 q-px-md text-bold">
              {{ $root.t('experience') }}
            </div>
            <template v-if="!empty(cv.exp.data)">
              <template v-for="(e, eindex) in cv.exp.data" :key="'exp_' + eindex">
                <div v-if="!e.hidden" :class="['row full-width', $q.platform.is.mobile ? 'border-bottom-grey' : '']">
                  <div class="col-sm-4 col-12">
                    <q-item>
                      <q-item-section>
                        <q-item-label>{{ e.city }}</q-item-label>
                        <q-item-label>{{ e.from }} {{ e.separator }} {{ e.to }}</q-item-label>
                      </q-item-section>
                    </q-item>
                  </div>
                  <div :class="['col-sm-8 col-12', $q.platform.is.mobile ? '' : 'border-left-grey']">
                    <q-list class="full-width">
                      <q-item>
                        <q-item-section>
                          <q-item-label class="text-bold text-body1">{{ e.title }}</q-item-label>
                          <q-item-label class="text-bold text-body1 text-primary">{{ e.where }}</q-item-label>
                          <template v-if="!empty(e.description)">
                            <template v-for="(d, dindex) in e.description" :key="'exp_' + eindex + '_descr_' + dindex">
                              <q-list dense>
                                <q-item class="q-py-none">
                                  <q-item-section side>
                                    <q-icon name="fiber_manual_record" size="1em" color="primary"></q-icon>
                                  </q-item-section>
                                  <q-item-section>
                                    <q-item-label>{{ d.label }}</q-item-label>
                                  </q-item-section>
                                  <template v-if="isClickable(d)">
                                    <q-item-section side>
                                      <q-btn
                                        flat
                                        icon="mdi-open-in-new"
                                        color="primary"
                                        @click="openURLCallback(d)"
                                      ></q-btn>
                                    </q-item-section>
                                  </template>
                                </q-item>
                              </q-list>
                            </template>
                          </template>
                          <template v-if="!empty(e.skills)">
                            <div class="q-mt-sm q-gutter-xs">
                              <q-chip
                                v-for="(sk, skidx) in e.skills"
                                :key="'exp_sk_' + skidx"
                                size="sm"
                                class="custom-cv-chip text-bold"
                              >
                                {{ sk }}
                              </q-chip>
                            </div>
                          </template>
                        </q-item-section>
                      </q-item>
                    </q-list>
                  </div>
                </div>
              </template>
            </template>
        </div>
      </div>
    </template>
    <!-- end Esperienze -->
    <!-- Competenze  + lang -->
    <div :class="['full-width q-pb-md', $q.platform.is.mobile ? '' : 'border-bottom-grey']">
      <div class="row">
        <template v-if="!empty(cv.skill)">
          <div :class="['col-sm-8 col-12', $q.platform.is.mobile ? 'border-bottom-grey' : 'border-right-grey']" id="skill">
          <div class="full-width text-h6 q-px-md text-bold">
            {{ $root.t('skills') }}
          </div>
          <div class="full-width q-px-md">
            <template v-for="(s, sindex) in cv.skill.data" :key="'skill_' + sindex">
              <q-chip
                v-if="!s.hidden"
                @click.native="openURLCallback(s)"
                class="custom-cv-chip"
                :clickable="isClickable(s)"
                square
                :icon="s.icon"
                >
                  {{ s.label }}
              </q-chip>
            </template>
          </div>
        </div>
        </template>
        <template v-if="!empty(cv.lang)">
          <div :class="['col-sm-4 col-12', $q.platform.is.mobile ? 'border-bottom-grey q-py-md' : '']" id="lang">
          <div class="full-width text-h6 q-px-md text-bold">
            {{ $root.t('languages') }}
          </div>
          <div class="full-width">
            <template v-if="!empty(cv.lang.data)">
              <template v-for="(l, lindex) in cv.lang.data" :key="'lang_' + lindex">
                <q-list v-if="!l.hidden" dense>
                  <q-item class="q-py-none">
                    <q-item-section side v-if="!empty(l.countrycode)">
                      <span style="font-size:1.5em">{{ countryCodeToEmoji(l.countrycode) }}</span>
                    </q-item-section>
                    <q-item-section>
                      <q-item-label class="text-bold">{{ l.label }}</q-item-label>
                      <q-item-label>{{ l.level }}</q-item-label>
                    </q-item-section>
                  </q-item>
                </q-list>
              </template>
            </template>
          </div>
        </div>
        </template>
      </div>
    </div>
    <!-- end competenze + lang -->
    <!-- school -->
    <template v-if="!empty(cv.school)">
      <div class="full-width" ref="school" id="school">
        <div :class="['row q-my-md', $q.platform.is.mobile ? '' : 'border-bottom-grey']">
          <div class="full-width text-h6 q-px-md text-bold">
            {{ $root.t('education') }}
          </div>
          <template v-if="!empty(cv.school.data)">
            <template v-for="(s, sindex) in cv.school.data" :key="'school_' + sindex">
              <div v-if="!s.hidden" :class="['row full-width', $q.platform.is.mobile ? 'border-bottom-grey q-py-md' : '']">
                <div class="col-sm-4 col-12">
                  <q-item>
                    <q-item-section>
                      <q-item-label>{{ s.city }}</q-item-label>
                      <q-item-label>{{ s.from }} {{ s.separator }} {{ s.to }}</q-item-label>
                    </q-item-section>
                  </q-item>
                </div>
                <div :class="['col-sm-8 col-12', $q.platform.is.mobile ? '' : 'border-left-grey']">
                  <q-list class="full-width">
                    <q-item>
                      <q-item-section>
                        <q-item-label class="text-bold text-body1">{{ s.title }}</q-item-label>
                        <q-item-label class="text-bold text-body1 text-primary">{{ s.where }}</q-item-label>
                        <template v-if="!empty(s.description)">
                          <template v-for="(d, dindex) in s.description" :key="'school_' + sindex + '_descr_' + dindex">
                            <q-list dense>
                              <q-item class="q-py-none">
                                <q-item-section side>
                                  <q-icon name="fiber_manual_record" size="1em" color="primary"></q-icon>
                                </q-item-section>
                                <q-item-section>
                                  <q-item-label>{{ d.label }}</q-item-label>
                                </q-item-section>
                                <template v-if="isClickable(d)">
                                  <q-item-section side>
                                    <q-btn
                                      flat
                                      icon="mdi-open-in-new"
                                      color="primary"
                                      @click="openURLCallback(d)"
                                    ></q-btn>
                                  </q-item-section>
                                </template>
                              </q-item>
                            </q-list>
                          </template>
                        </template>
                        <template v-if="!empty(s.skills)">
                          <div class="q-mt-sm q-gutter-xs">
                            <q-chip
                              v-for="(sk, skidx) in s.skills"
                              :key="'school_sk_' + skidx"
                              size="sm"
                              class="custom-cv-chip text-bold"
                            >
                              {{ sk }}
                            </q-chip>
                          </div>
                        </template>
                      </q-item-section>
                    </q-item>
                  </q-list>
                </div>
              </div>
            </template>
          </template>
        </div>
      </div>
    </template>
    <!-- end school -->
    <!-- courses -->
    <template v-if="!empty(cv.courses)">
      <div class="full-width" id="courses">
        <div :class="['row q-my-md', $q.platform.is.mobile ? '' : 'border-bottom-grey']">
          <div class="full-width text-h6 q-px-md text-bold">
            {{ $root.t('certificates') }}
          </div>
          <template v-if="!empty(cv.courses.data)">
            <template v-for="(c, cindex) in cv.courses.data" :key="'courses_' + cindex">
              <div v-if="!c.hidden" :class="['row full-width', $q.platform.is.mobile ? 'border-bottom-grey q-py-md' : '']">
                <div class="col-sm-4 col-12">
                  <q-item>
                    <q-item-section>
                      <q-item-label>{{ c.city }}</q-item-label>
                      <q-item-label>{{ c.from }} {{ c.separator }} {{ c.to }}</q-item-label>
                    </q-item-section>
                  </q-item>
                </div>
                <div :class="['col-sm-8 col-12', $q.platform.is.mobile ? '' : 'border-left-grey']">
                  <q-list class="full-width">
                    <q-item>
                      <q-item-section>
                        <q-item-label class="text-bold text-body1">{{ c.title }}</q-item-label>
                        <q-item-label class="text-bold text-body1 text-primary">{{ c.where }}</q-item-label>
                        <template v-if="!empty(c.description)">
                          <template v-for="(d, dindex) in c.description" :key="'courses_' + cindex + '_descr_' + dindex">
                            <q-list dense>
                              <q-item class="q-py-none">
                                <q-item-section side>
                                  <q-icon name="fiber_manual_record" size="1em" color="primary"></q-icon>
                                </q-item-section>
                                <q-item-section>
                                  <q-item-label>{{ d.label }}</q-item-label>
                                </q-item-section>
                                <template v-if="isClickable(d)">
                                  <q-item-section side>
                                    <q-btn
                                      flat
                                      icon="mdi-open-in-new"
                                      color="primary"
                                      @click="openURLCallback(d)"
                                    ></q-btn>
                                  </q-item-section>
                                </template>
                              </q-item>
                            </q-list>
                          </template>
                        </template>
                        <template v-if="!empty(c.skills)">
                          <div class="q-mt-sm q-gutter-xs">
                            <q-chip
                              v-for="(sk, skidx) in c.skills"
                              :key="'courses_sk_' + skidx"
                              size="sm"
                              class="custom-cv-chip text-bold"
                            >
                              {{ sk }}
                            </q-chip>
                          </div>
                        </template>
                      </q-item-section>
                    </q-item>
                  </q-list>
                </div>
              </div>
            </template>
          </template>
        </div>
      </div>
    </template>
    <!-- end courses -->
    <!-- management -->
    <template v-if="!empty(cv.managementSkill)">
      <div :class="['full-width', $q.platform.is.mobile ? 'border-bottom-grey' : '']" id="managementSkill">
        <div :class="['row q-my-md', $q.platform.is.mobile ? '' : 'border-bottom-grey']">
          <div class="col-sm-5 col-12">
            <q-item class="q-pa-none">
              <q-item-section>
                <q-item-label class="text-h6 q-px-md text-bold">{{ $root.t('mgmtSkillsTitle') }}</q-item-label>
              </q-item-section>
            </q-item>
          </div>
          <div :class="['col-sm-7 col-12', $q.platform.is.mobile ? '' : 'border-left-grey']">
            <q-list class="full-width">
              <q-item>
                <q-item-section>
                  <template v-if="!empty(cv.managementSkill.data)">
                    <template v-for="(d, dindex) in cv.managementSkill.data" :key="'managementSkill_' + dindex">
                      <q-list v-if="!d.hidden" dense>
                        <q-item class="q-py-none">
                          <q-item-section side>
                            <q-icon name="fiber_manual_record" size="1em" color="primary"></q-icon>
                          </q-item-section>
                          <q-item-section>
                            <q-item-label>{{ d.label }}</q-item-label>
                          </q-item-section>
                          <template v-if="isClickable(d)">
                            <q-item-section side>
                              <q-btn
                                flat
                                icon="mdi-open-in-new"
                                color="primary"
                                @click="openURLCallback(d)"
                              ></q-btn>
                            </q-item-section>
                          </template>
                        </q-item>
                      </q-list>
                    </template>
                  </template>
                </q-item-section>
              </q-item>
            </q-list>
          </div>
        </div>
      </div>
    </template>
    <!-- end management -->
    <!-- communicative -->
    <template v-if="!empty(cv.comunicativeSkill)">
      <div :class="['full-width', $q.platform.is.mobile ? 'border-bottom-grey' : '']" id="comunicativeSkill">
        <div :class="['row q-my-md', $q.platform.is.mobile ? '' : 'border-bottom-grey']">
          <div class="col-sm-5 col-12">
            <q-item class="q-pa-none">
              <q-item-section>
                <q-item-label class="text-h6 q-px-md text-bold">{{ $root.t('commSkillsTitle') }}</q-item-label>
              </q-item-section>
            </q-item>
          </div>
          <div :class="['col-sm-7 col-12', $q.platform.is.mobile ? '' : 'border-left-grey']">
            <q-list class="full-width">
              <q-item>
                <q-item-section>
                  <template v-if="!empty(cv.comunicativeSkill.data)">
                    <template v-for="(d, dindex) in cv.comunicativeSkill.data" :key="'comunicativeSkill_' + dindex">
                      <q-list v-if="!d.hidden" dense>
                        <q-item class="q-py-none">
                          <q-item-section side>
                            <q-icon name="fiber_manual_record" size="1em" color="primary"></q-icon>
                          </q-item-section>
                          <q-item-section>
                            <q-item-label>{{ d.label }}</q-item-label>
                          </q-item-section>
                          <template v-if="isClickable(d)">
                            <q-item-section side>
                              <q-btn
                                flat
                                icon="mdi-open-in-new"
                                color="primary"
                                @click="openURLCallback(d)"
                              ></q-btn>
                            </q-item-section>
                          </template>
                        </q-item>
                      </q-list>
                    </template>
                  </template>
                </q-item-section>
              </q-item>
            </q-list>
          </div>
        </div>
      </div>
    </template>
    <!-- end communicative -->
    <!-- digital -->
    <template v-if="!empty(cv.digitalSkill)">
      <div :class="['full-width', $q.platform.is.mobile ? 'border-bottom-grey' : '']" id="digitalSkill">
        <div :class="['row q-my-md', $q.platform.is.mobile ? '' : 'border-bottom-grey']">
          <div class="full-width text-h6 q-px-md text-bold">
            {{ $root.t('digSkills') }}
          </div>
          <template v-if="!empty(cv.digitalSkill.data)">
            <template v-for="(ds, dsindex) in cv.digitalSkill.data" :key="'digitalSkill_' + dsindex">
              <div v-if="!ds.hidden" class="row full-width">
                <div class="col-sm-5 col-12">
                  &nbsp;
                </div>
                <div :class="['col-sm-7 col-12', $q.platform.is.mobile ? '' : 'border-left-grey']">
                  <q-list class="full-width">
                    <q-item>
                      <q-item-section>
                        <q-item-label class="text-bold text-body1">{{ ds.title }}</q-item-label>
                        <template v-if="!empty(ds.description)">
                          <template v-for="(d, dindex) in ds.description" :key="'digitalSkill_' + dsindex + '_descr_' + dindex">
                            <q-list dense>
                              <q-item class="q-py-none">
                                <q-item-section side>
                                  <q-icon name="fiber_manual_record" size="1em" color="primary"></q-icon>
                                </q-item-section>
                                <q-item-section>
                                  <q-item-label>{{ d.label }}</q-item-label>
                                </q-item-section>
                                <template v-if="isClickable(d)">
                                  <q-item-section side>
                                    <q-btn
                                      flat
                                      icon="mdi-open-in-new"
                                      color="primary"
                                      @click="openURLCallback(d)"
                                    ></q-btn>
                                  </q-item-section>
                                </template>
                              </q-item>
                            </q-list>
                          </template>
                        </template>
                      </q-item-section>
                    </q-item>
                  </q-list>
                </div>
              </div>
            </template>
          </template>
        </div>
      </div>
    </template>
    <!-- end digital -->
    <!-- digital -->
    <template v-if="!empty(cv.others)">
      <div :class="['full-width', $q.platform.is.mobile ? 'border-bottom-grey' : '']" id="others">
        <div :class="['row q-my-md', $q.platform.is.mobile ? '' : 'border-bottom-grey']">
          <div class="col-sm-5 col-12 text-h6 q-px-md text-bold">
            {{ $root.t('additionalInfoTitle') }}
          </div>
          <div :class="['col-sm-7 col-12', $q.platform.is.mobile ? '' : 'border-left-grey']">
          </div>
        <template v-if="!empty(cv.others.data)">
            <template v-for="(o, oindex) in cv.others.data" :key="'others_' + oindex">
              <div v-if="!o.hidden" class="row full-width">
                <div class="col-sm-5 col-12">
                  &nbsp;
                </div>
                <div :class="['col-sm-7 col-12', $q.platform.is.mobile ? '' : 'border-left-grey']">
                  <q-list class="full-width">
                    <q-item>
                      <q-item-section>
                        <q-item-label class="text-bold text-body1">{{ o.title }}</q-item-label>
                        <template v-if="!empty(o.description.length)">
                          <template v-for="(d, dindex) in o.description" :key="'others_' + oindex + '_descr_' + dindex">
                            <q-list dense>
                              <q-item class="q-py-none">
                                <q-item-section side>
                                  <q-icon name="fiber_manual_record" size="1em" color="primary"></q-icon>
                                </q-item-section>
                                <q-item-section>
                                  <q-item-label>{{ d.label }}</q-item-label>
                                </q-item-section>
                                <template v-if="isClickable(d)">
                                  <q-item-section side>
                                    <q-btn
                                      flat
                                      icon="mdi-open-in-new"
                                      color="primary"
                                      @click="openURLCallback(d)"
                                    ></q-btn>
                                  </q-item-section>
                                </template>
                              </q-item>
                            </q-list>
                          </template>
                        </template>
                        <template v-if="!empty(o.skills)">
                          <div class="q-mt-sm q-gutter-xs">
                            <q-chip
                              v-for="(sk, skidx) in o.skills"
                              :key="'others_sk_' + skidx"
                              size="sm"
                              class="custom-cv-chip text-bold"
                            >
                              {{ sk }}
                            </q-chip>
                          </div>
                        </template>
                      </q-item-section>
                    </q-item>
                  </q-list>
                </div>
              </div>
            </template>
          </template>
        </div>
      </div>
    </template>
    <!-- end digital -->
    <!-- privacy -->
    <template v-if="!empty(cv.privacy)">
      <div class="full-width" id="privacy">
        <div :class="['row q-my-md', $q.platform.is.mobile ? '' : 'border-bottom-grey']">
          <div class="col-sm-5 col-12">
            <q-item class="q-pa-none">
              <q-item-section>
                <q-item-label class="text-h6 q-px-md text-bold">{{ $root.t('privacyLabelTitle') }}</q-item-label>
              </q-item-section>
            </q-item>
          </div>
          <div :class="['col-sm-7 col-12', $q.platform.is.mobile ? '' : 'border-left-grey']">
            <q-item class="q-pa-none">
              <q-item-section>
                <q-item-label class=" q-px-md q-pb-md">{{ cv.privacy.description }}</q-item-label>
              </q-item-section>
            </q-item>
          </div>
        </div>
      </div>
    </template>
    <!-- end privacy -->
      <div class="q-pa-xl">&nbsp;</div>
    </template>
  </q-page>
</template>

<script>
import { openURL } from 'quasar'

export default {
  name: 'CV',
  data () {
    return {}
  },
  computed: {
    cv () {
      return this.$root.cv || {}
    }
  },
  methods:
    {
      openURL,
      openURLCallback (obj) {
        if (this.isClickable(obj)) {
          this.openURL(obj.link)
        }
      },
      isClickable (obj) {
        return obj.link !== null
      },
      countryCodeToEmoji (code) {
        return code.toUpperCase().replace(/./g, c => String.fromCodePoint(c.charCodeAt(0) + 127397))
      },
      empty (mixedVar) {
        //  discuss at: https://locutus.io/php/empty/
        // original by: Philippe Baumann
        //    input by: Onno Marsman (https://twitter.com/onnomarsman)
        //    input by: LH
        //    input by: Stoyan Kyosev (https://www.svest.org/)
        // bugfixed by: Kevin van Zonneveld (https://kvz.io)
        // improved by: Onno Marsman (https://twitter.com/onnomarsman)
        // improved by: Francesco
        // improved by: Marc Jansen
        // improved by: Rafał Kukawski (https://blog.kukawski.pl)
        //   example 1: empty(null)
        //   returns 1: true
        //   example 2: empty(undefined)
        //   returns 2: true
        //   example 3: empty([])
        //   returns 3: true
        //   example 4: empty({})
        //   returns 4: true
        //   example 5: empty({'aFunc' : function () { alert('humpty'); } })
        //   returns 5: false

        var undef
        var key
        var i
        var len
        var emptyValues = [undef, null, false, 0, '', '0']

        for (i = 0, len = emptyValues.length; i < len; i++) {
          if (mixedVar === emptyValues[i]) {
            return true
          }
        }

        if (typeof mixedVar === 'object') {
          for (key in mixedVar) {
            // eslint-disable-next-line no-prototype-builtins
            if (mixedVar.hasOwnProperty(key)) {
              return false
            }
          }
          return true
        }
        return false
      }

    },
  mounted () {
    mainVue.addRefs('index', this)
  }
}
</script>
