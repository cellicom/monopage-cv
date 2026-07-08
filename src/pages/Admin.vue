<template>
  <q-layout class="bg-grey-1">
    <q-page-container>
      <q-page class="q-pa-md bg-grey-1">
    <!-- Authorization Lock -->
    <div v-if="!isAuthorized" class="flex flex-center" style="min-height: 80vh">
      <q-card style="width: 400px; max-width: 90vw" class="q-pa-md shadow-3">
        <!-- Language Selector -->
        <div class="row justify-end q-mb-sm">
          <q-select
            v-model="$root.currentLang"
            :options="languagesOptions"
            map-options
            emit-value
            outlined
            dense
            :style="$q.screen.lt.sm ? 'width: 80px; min-width: 80px;' : 'width: 140px;'"
            :label="$q.screen.lt.sm ? undefined : $root.t('languages')"
          >
            <template v-slot:selected-item="scope">
              <span>
                {{ getSelectedLangEmoji(scope.opt) }}
              </span>
            </template>
          </q-select>
        </div>

        <q-card-section class="text-center">
          <q-avatar icon="lock" color="primary" text-color="white" size="60px" class="q-mb-md" />
          <div class="text-h6 text-bold">{{ $root.t('adminArea') }}</div>
          <div class="text-caption text-grey-6">{{ $root.t('loginInstructions') }}</div>
        </q-card-section>

        <q-card-section>
          <q-input
            v-model="password"
            :type="showLoginPwd ? 'text' : 'password'"
            :label="$root.t('passwordTab')"
            outlined
            dense
            @keydown.enter="authorize"
            autofocus
          >
            <template v-slot:append>
              <q-icon
                :name="showLoginPwd ? 'visibility_off' : 'visibility'"
                class="cursor-pointer"
                @click="showLoginPwd = !showLoginPwd"
              />
            </template>
          </q-input>
        </q-card-section>

        <q-card-actions align="center" class="column q-gutter-y-sm">
          <q-btn :label="$root.t('loginBtn')" color="primary" @click="authorize" class="full-width text-bold" />
          <q-btn :label="$root.t('backToCV')" icon="arrow_back" color="grey-8" text-color="white" @click="goBackWithCheck" class="full-width text-bold" />
        </q-card-actions>
      </q-card>
    </div>

    <!-- Admin Panel Content -->
    <div v-else class="max-width-container">
      <!-- Header -->
      <div class="row items-center justify-between q-mb-md bg-white q-pa-md rounded-borders shadow-1">
        <div :class="['col-12 col-sm-auto q-mb-sm q-mb-sm-none', $q.screen.lt.md ? 'full-width row no-wrap justify-between items-center' : '']">
          <div>
            <h1 class="text-h5 text-bold q-my-none">{{ $root.t('adminTitle') }}</h1>
            <div class="text-caption text-grey-6">{{ $root.t('adminSubtitle') }}</div>
            <q-btn
              v-if="!$q.screen.lt.md"
              :label="$root.t('backToCV')"
              icon="arrow_back"
              color="grey-8"
              text-color="white"
              @click="goBackWithCheck"
              class="q-mt-sm text-bold"
            />
          </div>
          
          <!-- Mobile Language Selector & Delete Button -->
          <div v-if="$q.screen.lt.md" class="column items-end q-gutter-y-xs">
            <q-select
              v-model="selectedLang"
              :options="languagesOptions"
              map-options
              emit-value
              outlined
              dense
              bg-color="white"
              style="width: 80px; min-width: 80px;"
              @update:model-value="onLangChange"
            >
              <template v-slot:selected-item="scope">
                <span>
                  {{ getSelectedLangEmoji(scope.opt) }}
                </span>
              </template>
            </q-select>
            <q-btn
              v-if="canDeleteCurrentLang"
              dense
              color="negative"
              text-color="white"
              icon="delete"
              @click="confirmDeleteLang"
              style="width: 80px; min-width: 80px;"
            >
              <q-tooltip>{{ $root.t('deleteBtn') }}</q-tooltip>
            </q-btn>
          </div>
        </div>

        <!-- Desktop/Tablet Action Buttons & Language Selector -->
        <div v-if="!$q.screen.lt.md" class="col-12 col-sm-auto row no-wrap q-gutter-sm justify-end items-center">
          <q-select
            v-model="selectedLang"
            :options="languagesOptions"
            map-options
            emit-value
            outlined
            dense
            bg-color="white"
            style="min-width: 140px;"
            class="col-xs col-sm-auto"
            :label="$root.t('languages')"
            @update:model-value="onLangChange"
          >
            <template v-slot:selected-item="scope">
              <span>
                {{ getSelectedLangEmoji(scope.opt) }}
              </span>
            </template>
          </q-select>
          <q-btn
            v-if="canDeleteCurrentLang"
            :label="$root.t('deleteBtn')"
            icon="delete"
            color="negative"
            @click="confirmDeleteLang"
            class="col-xs col-sm-auto text-bold"
          />
          <q-btn :label="$root.t('save')" icon="save" color="primary" @click="saveChanges" class="col-xs col-sm-auto text-bold" />
        </div>

        <!-- Mobile Action Buttons (Back and Save) -->
        <div v-else class="col-12 row no-wrap q-gutter-sm justify-end items-center q-mt-sm">
          <q-btn :label="$root.t('backToCV')" icon="arrow_back" color="grey-8" @click="goBackWithCheck" class="col-xs col-sm-auto" />
          <q-btn :label="$root.t('save')" icon="save" color="primary" @click="saveChanges" class="col-xs col-sm-auto text-bold" />
        </div>
      </div>

      <!-- Main Tabs Layout -->
      <div class="row q-col-gutter-md">
        <div class="col-12 col-md-2">
          <q-card class="shadow-1">
            <q-tabs
              v-model="activeTab"
              :vertical="!$q.screen.lt.md"
              class="bg-primary text-white"
              active-bg-color="blue-9"
              indicator-color="white"
              outside-arrows
              mobile-arrows
            >
              <q-tab name="pwd" icon="mdi-cog" :label="$root.t('settingsTab')" align="left" class="q-py-md text-bold" />
              <q-tab name="info" icon="mdi-account" :label="$root.t('personalInfo')" align="left" class="q-py-md text-bold" />
              <q-tab name="exp" icon="mdi-briefcase" :label="$root.t('experience')" align="left" class="q-py-md text-bold" />
              <q-tab name="skill" icon="mdi-tools" :label="$root.t('skills')" align="left" class="q-py-md text-bold" />
              <q-tab name="lang" icon="mdi-translate" :label="$root.t('languages')" align="left" class="q-py-md text-bold" />
              <q-tab name="school" icon="mdi-school" :label="$root.t('education')" align="left" class="q-py-md text-bold" />
              <q-tab name="courses" icon="mdi-teach" :label="$root.t('certificates')" align="left" class="q-py-md text-bold" />
              <q-tab name="skills_extra" icon="mdi-account-group" :label="$root.t('otherSkills')" align="left" class="q-py-md text-bold" />
              <q-tab name="digital" icon="mdi-laptop" :label="$root.t('digSkills')" align="left" class="q-py-md text-bold" />
              <q-tab name="privacy" icon="mdi-draw" :label="$root.t('privacyExtra')" align="left" class="q-py-md text-bold" />
            </q-tabs>
          </q-card>
        </div>

        <div class="col-12 col-md-10">
          <q-card class="q-pa-md shadow-1 min-height-card">
            <q-tab-panels v-model="activeTab" animated keep-alive>
              
              <!-- Tab 0: Password -->
              <q-tab-panel name="pwd">
                <div class="text-h6 text-bold q-mb-md">{{ $root.t('pwdManage') }}</div>

                <!-- Banner: nessuna password impostata -->
                <q-banner v-if="!localCV.adminPassword" rounded class="bg-orange-1 text-orange-9 q-mb-md">
                  <template v-slot:avatar>
                    <q-icon name="mdi-alert" color="orange" />
                  </template>
                  <strong>{{ $root.t('noCustomPwdSet') }}</strong><br>
                  {{ $root.t('usingDefaultPwd') }}
                </q-banner>

                <div class="row q-col-gutter-sm" style="max-width:400px">
                  <div class="col-12">
                    <q-input v-model="pwForm.current" :type="showPwdForm ? 'text' : 'password'" :label="$root.t('currentPwd')" outlined dense>
                      <template v-slot:append>
                        <q-icon :name="showPwdForm ? 'visibility_off' : 'visibility'" class="cursor-pointer" @click="showPwdForm = !showPwdForm" />
                      </template>
                    </q-input>
                  </div>
                  <div class="col-12">
                    <q-input v-model="pwForm.next" :type="showPwdNext ? 'text' : 'password'" :label="$root.t('newPwd')" outlined dense>
                      <template v-slot:append>
                        <q-icon :name="showPwdNext ? 'visibility_off' : 'visibility'" class="cursor-pointer" @click="showPwdNext = !showPwdNext" />
                      </template>
                    </q-input>
                  </div>
                  <div class="col-12">
                    <q-input v-model="pwForm.confirm" :type="showPwdConfirm ? 'text' : 'password'" :label="$root.t('confirmNewPwd')" outlined dense>
                      <template v-slot:append>
                        <q-icon :name="showPwdConfirm ? 'visibility_off' : 'visibility'" class="cursor-pointer" @click="showPwdConfirm = !showPwdConfirm" />
                      </template>
                    </q-input>
                  </div>
                  <div class="col-12">
                    <q-btn :label="$root.t('changePwdBtn')" icon="mdi-lock-reset" color="primary" @click="changePassword" />
                  </div>
                </div>
                <q-separator class="q-my-lg" />

                <!-- Theme Section -->
                <div class="text-h6 text-bold q-mb-md">{{ $root.t('themeLabel') }}</div>
                <div class="row q-col-gutter-sm q-mb-lg" style="max-width:400px">
                  <div class="col-12">
                    <q-select
                      v-model="$root.rawCV.theme"
                      :options="themeOptions"
                      emit-value
                      map-options
                      outlined
                      dense
                      @update:model-value="onThemeChange"
                    >
                      <!-- Selected item preview inside the input box -->
                      <template v-slot:selected-item="scope">
                        <div class="row items-center q-gutter-xs">
                          <div :style="`width: 12px; height: 12px; background-color: ${scope.opt.colors ? scope.opt.colors.primary : '#ccc'}; border: 1px solid #ccc; border-radius: 2px;`" />
                          <div :style="`width: 12px; height: 12px; background-color: ${scope.opt.colors ? scope.opt.colors.secondary : '#ccc'}; border: 1px solid #ccc; border-radius: 2px;`" />
                          <div :style="`width: 12px; height: 12px; background-color: ${scope.opt.colors ? scope.opt.colors.chipBg : '#ccc'}; border: 1px solid #ccc; border-radius: 2px;`" />
                          <span class="q-ml-sm">{{ scope.opt.label }}</span>
                        </div>
                      </template>
                      <!-- Options preview inside the dropdown list -->
                      <template v-slot:option="scope">
                        <q-item v-bind="scope.itemProps">
                          <q-item-section avatar class="q-pr-none" style="min-width: auto;">
                            <div class="row q-gutter-xs items-center q-mr-sm">
                              <div :style="`width: 14px; height: 14px; background-color: ${scope.opt.colors ? scope.opt.colors.primary : '#ccc'}; border: 1px solid #ccc; border-radius: 2px;`" />
                              <div :style="`width: 14px; height: 14px; background-color: ${scope.opt.colors ? scope.opt.colors.secondary : '#ccc'}; border: 1px solid #ccc; border-radius: 2px;`" />
                              <div :style="`width: 14px; height: 14px; background-color: ${scope.opt.colors ? scope.opt.colors.chipBg : '#ccc'}; border: 1px solid #ccc; border-radius: 2px;`" />
                            </div>
                          </q-item-section>
                          <q-item-section>
                            <q-item-label>{{ scope.opt.label }}</q-item-label>
                          </q-item-section>
                        </q-item>
                      </template>
                    </q-select>
                  </div>
                </div>

                <div class="row q-col-gutter-sm q-mb-lg" style="max-width:400px">
                  <div class="col-12">
                    <q-select
                      v-model="$root.rawCV.defaultLanguage"
                      :options="activeLanguagesOptions"
                      emit-value
                      map-options
                      outlined
                      dense
                      clearable
                      :label="$root.t('defaultLanguageLabel')"
                    />
                  </div>
                </div>

                <q-separator class="q-my-lg" />

                <!-- Backup Section -->
                <div class="text-h6 text-bold q-mb-md">{{ $root.t('backupSection') }}</div>
                <div class="row q-col-gutter-sm" style="max-width:400px">
                  <div class="col-12">
                    <q-btn
                      :label="$root.t('downloadSettings')"
                      icon="mdi-download"
                      color="primary"
                      @click="downloadSettings"
                      class="full-width"
                    />
                  </div>
                  <div class="col-12">
                    <q-btn
                      :label="$root.t('importSettings')"
                      icon="mdi-upload"
                      color="secondary"
                      @click="$refs.importSettingsInput.click()"
                      class="full-width"
                    />
                    <input
                      ref="importSettingsInput"
                      type="file"
                      accept=".json"
                      style="display:none"
                      @change="importSettings"
                    />
                  </div>
                </div>
              </q-tab-panel>

              <!-- Tab 1: Info Personali -->
              <q-tab-panel name="info">
                <div class="text-h6 text-bold q-mb-md">{{ $root.t('generalInfo') }}</div>

                <!-- Centered Profile Image (First Field) -->
                <div class="row justify-center q-mb-lg">
                  <div class="text-center">
                    <div class="q-mb-xs text-caption text-grey-7">{{ $root.t('profileImg') }}</div>
                    <div class="cursor-pointer" style="display:inline-block" @click="$refs.avatarInput.click()" :title="$root.t('clickToChangeImg')">
                      <q-avatar v-if="$root.rawCV.img" size="100px" style="border:2px dashed #ccc">
                        <img :src="$root.rawCV.img + '?t=' + avatarTs">
                        <q-tooltip>{{ $root.t('clickToChangeImg') }}</q-tooltip>
                      </q-avatar>
                      <q-btn v-else round icon="add" color="grey-4" text-color="grey-8" size="lg" unelevated>
                        <q-tooltip>{{ $root.t('addProfileImg') }}</q-tooltip>
                      </q-btn>
                    </div>
                    <input ref="avatarInput" type="file" accept="image/*" style="display:none" @change="e => uploadAvatar(e.target.files[0])">
                  </div>
                </div>

                <div class="row q-col-gutter-sm">
                  <div class="col-12 col-sm-6">
                    <q-input v-model="$root.rawCV.name" :label="$root.t('fullName')" outlined dense />
                  </div>
                  <div class="col-12 col-sm-6">
                    <q-input v-model="birthDate" :label="$root.t('birthDateLabel')" outlined dense>
                      <template v-slot:append>
                        <q-icon name="event" class="cursor-pointer" @click="openDatePicker(null, 'birthDate')" />
                      </template>
                    </q-input>
                  </div>
                  <div class="col-12">
                    <q-input v-model="localCV.info.description" :label="$root.t('professionalTitle')" outlined dense />
                  </div>
                  <div class="col-12">
                    <q-input v-model="localCV.info.about" type="textarea" rows="3" :label="$root.t('professionalProfile')" outlined dense />
                  </div>
                </div>

                <q-separator class="q-my-lg" />

                <div class="text-subtitle1 text-bold q-mb-sm">{{ $root.t('contactDetails') }}</div>
                
                <q-list bordered separator class="rounded-borders">
                  <template v-for="(item, index) in localCV.info.data" :key="index">
                    <q-item v-if="item.label !== 'Data di nascita'" class="q-py-sm">
                      <q-item-section>
                        <div class="row q-col-gutter-xs">
                          <div class="col-12 col-sm-4">
                            <q-input v-model="item.label" :label="$root.t('labelPlaceholder')" outlined dense />
                          </div>
                          <div class="col-12 col-sm-4">
                            <q-input v-model="item.value" :label="$root.t('valueLabel')" outlined dense />
                          </div>
                          <div class="col-12 col-sm-4">
                            <q-input v-model="item.link" :label="$root.t('linkLabel')" outlined dense />
                          </div>
                        </div>
                      </q-item-section>
                      <q-item-section side>
                        <div class="column items-center">
                          <div class="row">
                            <q-btn icon="arrow_upward" color="grey-7" flat round dense :disabled="index === 0" @click="moveUp(localCV.info.data, index)" />
                            <q-btn icon="arrow_downward" color="grey-7" flat round dense :disabled="index === localCV.info.data.length - 1" @click="moveDown(localCV.info.data, index)" />
                          </div>
                          <div class="row items-center">
                            <q-toggle
                              :model-value="!item.hidden"
                              @update:model-value="val => item.hidden = !val"
                              checked-icon="visibility"
                              unchecked-icon="visibility_off"
                              :color="!item.hidden ? 'primary' : 'negative'"
                              keep-color dense
                            >
                              <q-tooltip>{{ !item.hidden ? $root.t('visible') : $root.t('hidden') }}</q-tooltip>
                            </q-toggle>
                            <q-btn icon="delete" color="negative" flat round dense @click="removeInfoData(index)" />
                          </div>
                        </div>
                      </q-item-section>
                    </q-item>
                  </template>
                </q-list>
                <q-btn :label="$root.t('addData')" icon="add" color="primary" outline class="full-width q-mt-md" @click="addInfoData" />
              </q-tab-panel>

              <!-- Tab 2: Esperienze -->
              <q-tab-panel name="exp">
                <div class="text-h6 text-bold q-mb-md">{{ $root.t('workExperience') }}</div>

                <q-expansion-item
                  v-for="(e, index) in localCV.exp.data"
                  :key="index"
                  v-model="e.expanded"
                  class="q-mb-md border rounded-borders bg-grey-1 shadow-1"
                  header-class="q-pa-xs bg-white rounded-borders"
                >
                  <template v-slot:header>
                    <div class="row items-center full-width no-wrap">
                      <div class="row items-center no-wrap q-mr-sm" @click.stop>
                        <q-btn icon="arrow_upward" color="grey-7" flat round dense :disabled="index === 0" @click="moveUp(localCV.exp.data, index)" />
                        <q-btn icon="arrow_downward" color="grey-7" flat round dense :disabled="index === localCV.exp.data.length - 1" @click="moveDown(localCV.exp.data, index)" />
                      </div>
                      <div class="col text-subtitle2 text-bold text-grey-8 q-px-sm ellipsis-2-lines" style="line-height: 1.2;">
                        {{ e.title ? e.title : $root.t('newExperience') }} {{ e.where ? $root.t('atCompany') + ' ' + e.where : '' }}
                      </div>
                      <div class="row items-center q-gutter-xs" @click.stop>
                        <q-toggle
                          :model-value="!e.hidden"
                          @update:model-value="val => e.hidden = !val"
                          checked-icon="visibility"
                          unchecked-icon="visibility_off"
                          :color="!e.hidden ? 'primary' : 'negative'"
                          keep-color
                          dense
                          class="q-ml-sm"
                        >
                          <q-tooltip>{{ !e.hidden ? $root.t('visible') : $root.t('hidden') }}</q-tooltip>
                        </q-toggle>
                        <q-btn icon="delete" color="negative" flat round dense @click="removeExperience(index)" />
                      </div>
                    </div>
                  </template>

                  <div class="q-pa-md">
                    <div class="row q-col-gutter-sm">
                      <div class="col-12 col-sm-4">
                        <q-input v-model="e.title" :label="$root.t('roleTitle')" outlined dense />
                      </div>
                      <div class="col-12 col-sm-4">
                        <q-input v-model="e.where" :label="$root.t('companyLabel')" outlined dense />
                      </div>
                      <div class="col-12 col-sm-4">
                        <q-input v-model="e.city" :label="$root.t('cityLabel')" outlined dense />
                      </div>
                      <div class="col-12 col-sm-4">
                        <q-input v-model="e.from" :label="$root.t('fromMonthYear')" outlined dense>
                          <template v-slot:append>
                            <q-icon name="event" class="cursor-pointer" @click="openDatePicker(e, 'from')" />
                          </template>
                        </q-input>
                      </div>
                      <div class="col-12 col-sm-4">
                        <q-input v-model="e.to" :label="$root.t('toOrCurrent')" outlined dense>
                          <template v-slot:append>
                            <q-icon name="event" class="cursor-pointer" @click="openDatePicker(e, 'to')" />
                          </template>
                        </q-input>
                      </div>
                      <div class="col-12 col-sm-4">
                        <q-input v-model="e.separator" :label="$root.t('dateSeparator')" outlined dense />
                      </div>
                      <div class="col-12">
                        <q-select
                          v-model="e.skills"
                          :label="$root.t('skillsChipsLabel')"
                          use-input
                          use-chips
                          chip-class="custom-cv-chip"
                          multiple
                          hide-dropdown-icon
                          input-debounce="0"
                          new-value-mode="add-unique"
                          outlined
                          dense
                          class="bg-white"
                          @new-value="createValue"
                        />
                      </div>
                    </div>

                    <div class="text-bold q-mt-md q-mb-xs">{{ $root.t('activitiesDetails') }}</div>
                    <q-list bordered separator class="bg-white rounded-borders">
                      <q-item v-for="(d, dindex) in e.description" :key="dindex" class="q-py-xs">
                        <q-item-section side class="flex-row">
                          <q-btn icon="arrow_upward" color="grey-7" flat round dense :disabled="dindex === 0" @click="moveUp(e.description, dindex)" />
                          <q-btn icon="arrow_downward" color="grey-7" flat round dense :disabled="dindex === e.description.length - 1" @click="moveDown(e.description, dindex)" />
                        </q-item-section>
                        <q-item-section>
                          <q-input v-model="d.label" :label="$root.t('activityDescription')" outlined dense />
                        </q-item-section>
                        <q-item-section side>
                          <q-btn icon="delete" color="negative" flat round dense @click="removeExperienceDetail(index, dindex)" />
                        </q-item-section>
                      </q-item>
                      <q-item class="q-py-xs flex justify-end">
                        <q-btn :label="$root.t('addActivity')" icon="add" color="primary" dense flat @click="addExperienceDetail(index)" />
                      </q-item>
                    </q-list>
                  </div>
                </q-expansion-item>
                <q-btn :label="$root.t('addExperience')" icon="add" color="primary" outline class="full-width q-mt-md" @click="addExperience" />
              </q-tab-panel>

              <!-- Tab 3: Competenze -->
              <q-tab-panel name="skill">
                <div class="text-h6 text-bold q-mb-md">{{ $root.t('technicalSkills') }}</div>

                <q-list bordered separator class="rounded-borders">
                  <q-item v-for="(s, index) in localCV.skill.data" :key="index" class="q-py-sm">
                    <q-item-section>
                      <div class="row q-col-gutter-xs">
                        <div class="col-12 col-sm-6">
                          <q-input v-model="s.label" :label="$root.t('skillLabel')" outlined dense />
                        </div>
                        <div class="col-12 col-sm-6">
                          <q-input v-model="s.link" :label="$root.t('skillLink')" outlined dense />
                        </div>
                      </div>
                    </q-item-section>
                    <q-item-section side>
                      <div class="column items-center">
                        <div class="row">
                          <q-btn icon="arrow_upward" color="grey-7" flat round dense :disabled="index === 0" @click="moveUp(localCV.skill.data, index)" />
                          <q-btn icon="arrow_downward" color="grey-7" flat round dense :disabled="index === localCV.skill.data.length - 1" @click="moveDown(localCV.skill.data, index)" />
                        </div>
                        <div class="row items-center">
                          <q-toggle
                            :model-value="!s.hidden"
                            @update:model-value="val => s.hidden = !val"
                            checked-icon="visibility"
                            unchecked-icon="visibility_off"
                            :color="!s.hidden ? 'primary' : 'negative'"
                            keep-color dense
                          >
                            <q-tooltip>{{ !s.hidden ? $root.t('visible') : $root.t('hidden') }}</q-tooltip>
                          </q-toggle>
                          <q-btn icon="delete" color="negative" flat round dense @click="removeSkill(index)" />
                        </div>
                      </div>
                    </q-item-section>
                  </q-item>
                </q-list>
                <q-btn :label="$root.t('addSkill')" icon="add" color="primary" outline class="full-width q-mt-md" @click="addSkill" />
              </q-tab-panel>

              <!-- Tab 4: Lingue -->
              <q-tab-panel name="lang">
                <div class="text-h6 text-bold q-mb-md">{{ $root.t('knownLanguages') }}</div>

                <q-list bordered separator class="rounded-borders">
                  <q-item v-for="(l, index) in localCV.lang.data" :key="index" class="q-py-sm">
                    <q-item-section>
                      <div class="row q-col-gutter-xs">
                        <div class="col-12 col-sm-4">
                          <q-input v-model="l.label" :label="$root.t('languageLabel')" outlined dense />
                        </div>
                        <div class="col-12 col-sm-4">
                          <q-input v-model="l.level" :label="$root.t('levelLabel')" outlined dense />
                        </div>
                        <div class="col-12 col-sm-4">
                          <q-input v-model="l.countrycode" :label="$root.t('countryCodeFlag')" outlined dense />
                        </div>
                      </div>
                    </q-item-section>
                    <q-item-section side>
                      <div class="column items-center">
                        <div class="row">
                          <q-btn icon="arrow_upward" color="grey-7" flat round dense :disabled="index === 0" @click="moveUp(localCV.lang.data, index)" />
                          <q-btn icon="arrow_downward" color="grey-7" flat round dense :disabled="index === localCV.lang.data.length - 1" @click="moveDown(localCV.lang.data, index)" />
                        </div>
                        <div class="row items-center">
                          <q-toggle
                            :model-value="!l.hidden"
                            @update:model-value="val => l.hidden = !val"
                            checked-icon="visibility"
                            unchecked-icon="visibility_off"
                            :color="!l.hidden ? 'primary' : 'negative'"
                            keep-color dense
                          >
                            <q-tooltip>{{ !l.hidden ? $root.t('visible') : $root.t('hidden') }}</q-tooltip>
                          </q-toggle>
                          <q-btn icon="delete" color="negative" flat round dense @click="removeLanguage(index)" />
                        </div>
                      </div>
                    </q-item-section>
                  </q-item>
                </q-list>
                <q-btn :label="$root.t('addLanguage')" icon="add" color="primary" outline class="full-width q-mt-md" @click="addLanguage" />
              </q-tab-panel>

              <!-- Tab 5: Istruzione -->
              <q-tab-panel name="school">
                <div class="text-h6 text-bold q-mb-md">{{ $root.t('educationAndTraining') }}</div>

                <q-expansion-item
                  v-for="(s, index) in localCV.school.data"
                  :key="index"
                  v-model="s.expanded"
                  class="q-mb-md border rounded-borders bg-grey-1 shadow-1"
                  header-class="q-pa-xs bg-white rounded-borders"
                >
                  <template v-slot:header>
                    <div class="row items-center full-width no-wrap">
                      <div class="row items-center no-wrap q-mr-sm" @click.stop>
                        <q-btn icon="arrow_upward" color="grey-7" flat round dense :disabled="index === 0" @click="moveUp(localCV.school.data, index)" />
                        <q-btn icon="arrow_downward" color="grey-7" flat round dense :disabled="index === localCV.school.data.length - 1" @click="moveDown(localCV.school.data, index)" />
                      </div>
                      <div class="col text-subtitle2 text-bold text-grey-8 q-px-sm ellipsis-2-lines" style="line-height: 1.2;">
                        {{ s.title ? s.title : $root.t('newEducation') }} {{ s.where ? $root.t('atCompany') + ' ' + s.where : '' }}
                      </div>
                      <div class="row items-center q-gutter-xs" @click.stop>
                        <q-toggle
                          :model-value="!s.hidden"
                          @update:model-value="val => s.hidden = !val"
                          checked-icon="visibility"
                          unchecked-icon="visibility_off"
                          :color="!s.hidden ? 'primary' : 'negative'"
                          keep-color
                          dense
                          class="q-ml-sm"
                        >
                          <q-tooltip>{{ !s.hidden ? $root.t('visible') : $root.t('hidden') }}</q-tooltip>
                        </q-toggle>
                        <q-btn icon="delete" color="negative" flat round dense @click="removeSchool(index)" />
                      </div>
                    </div>
                  </template>

                  <div class="q-pa-md">
                    <div class="row q-col-gutter-sm">
                      <div class="col-12 col-sm-6">
                        <q-input v-model="s.title" :label="$root.t('degreeTitle')" outlined dense />
                      </div>
                      <div class="col-12 col-sm-6">
                        <q-input v-model="s.where" :label="$root.t('schoolInstitution')" outlined dense />
                      </div>
                      <div class="col-12 col-sm-4">
                        <q-input v-model="s.city" :label="$root.t('cityLabel')" outlined dense />
                      </div>
                      <div class="col-12 col-sm-4">
                        <q-input v-model="s.from" :label="$root.t('fromYear')" outlined dense>
                          <template v-slot:append>
                            <q-icon name="event" class="cursor-pointer" @click="openDatePicker(s, 'from')" />
                          </template>
                        </q-input>
                      </div>
                      <div class="col-12 col-sm-4">
                        <q-input v-model="s.to" :label="$root.t('toYear')" outlined dense>
                          <template v-slot:append>
                            <q-icon name="event" class="cursor-pointer" @click="openDatePicker(s, 'to')" />
                          </template>
                        </q-input>
                      </div>
                      <div class="col-12">
                        <q-select
                          v-model="s.skills"
                          :label="$root.t('skillsChipsLabel')"
                          use-input
                          use-chips
                          chip-class="custom-cv-chip"
                          multiple
                          hide-dropdown-icon
                          input-debounce="0"
                          new-value-mode="add-unique"
                          outlined
                          dense
                          class="bg-white"
                          @new-value="createValue"
                        />
                      </div>
                    </div>

                    <div class="text-bold q-mt-md q-mb-xs">{{ $root.t('educationDetails') }}</div>
                    <q-list bordered separator class="bg-white rounded-borders">
                      <q-item v-for="(d, dindex) in s.description" :key="dindex" class="q-py-xs">
                        <q-item-section side class="flex-row">
                          <q-btn icon="arrow_upward" color="grey-7" flat round dense :disabled="dindex === 0" @click="moveUp(s.description, dindex)" />
                          <q-btn icon="arrow_downward" color="grey-7" flat round dense :disabled="dindex === s.description.length - 1" @click="moveDown(s.description, dindex)" />
                        </q-item-section>
                        <q-item-section>
                          <q-input v-model="d.label" :label="$root.t('educationDetailLabel')" outlined dense />
                        </q-item-section>
                        <q-item-section side>
                          <q-btn icon="delete" color="negative" flat round dense @click="removeSchoolDetail(index, dindex)" />
                        </q-item-section>
                      </q-item>
                      <q-item class="q-py-xs flex justify-end">
                        <q-btn :label="$root.t('addEducationDetail')" icon="add" color="primary" dense flat @click="addSchoolDetail(index)" />
                      </q-item>
                    </q-list>
                  </div>
                </q-expansion-item>
                <q-btn :label="$root.t('addEducation')" icon="add" color="primary" outline class="full-width q-mt-md" @click="addSchool" />
              </q-tab-panel>

              <!-- Tab 6: Certificati -->
              <q-tab-panel name="courses">
                <div class="text-h6 text-bold q-mb-md">{{ $root.t('certificatesAndCourses') }}</div>

                <q-expansion-item
                  v-for="(c, index) in localCV.courses.data"
                  :key="index"
                  v-model="c.expanded"
                  class="q-mb-md border rounded-borders bg-grey-1 shadow-1"
                  header-class="q-pa-xs bg-white rounded-borders"
                >
                  <template v-slot:header>
                    <div class="row items-center full-width no-wrap">
                      <div class="row items-center no-wrap q-mr-sm" @click.stop>
                        <q-btn icon="arrow_upward" color="grey-7" flat round dense :disabled="index === 0" @click="moveUp(localCV.courses.data, index)" />
                        <q-btn icon="arrow_downward" color="grey-7" flat round dense :disabled="index === localCV.courses.data.length - 1" @click="moveDown(localCV.courses.data, index)" />
                      </div>
                      <div class="col text-subtitle2 text-bold text-grey-8 q-px-sm ellipsis-2-lines" style="line-height: 1.2;">
                        {{ c.title ? c.title : $root.t('newCertificate') }} {{ c.where ? $root.t('atCompany') + ' ' + c.where : '' }}
                      </div>
                      <div class="row items-center q-gutter-xs" @click.stop>
                        <q-toggle
                          :model-value="!c.hidden"
                          @update:model-value="val => c.hidden = !val"
                          checked-icon="visibility"
                          unchecked-icon="visibility_off"
                          :color="!c.hidden ? 'primary' : 'negative'"
                          keep-color
                          dense
                          class="q-ml-sm"
                        >
                          <q-tooltip>{{ !c.hidden ? $root.t('visible') : $root.t('hidden') }}</q-tooltip>
                        </q-toggle>
                        <q-btn icon="delete" color="negative" flat round dense @click="removeCourse(index)" />
                      </div>
                    </div>
                  </template>

                  <div class="q-pa-md">
                    <div class="row q-col-gutter-sm">
                      <div class="col-12 col-sm-6">
                        <q-input v-model="c.title" :label="$root.t('certificateName')" outlined dense />
                      </div>
                      <div class="col-12 col-sm-6">
                        <q-input v-model="c.where" :label="$root.t('issuerLabel')" outlined dense />
                      </div>
                      <div class="col-12 col-sm-4">
                        <q-input v-model="c.city" :label="$root.t('cityLabel')" outlined dense />
                      </div>
                      <div class="col-12 col-sm-4">
                        <q-input v-model="c.from" :label="$root.t('fromLabel')" outlined dense>
                          <template v-slot:append>
                            <q-icon name="event" class="cursor-pointer" @click="openDatePicker(c, 'from')" />
                          </template>
                        </q-input>
                      </div>
                      <div class="col-12 col-sm-4">
                        <q-input v-model="c.to" :label="$root.t('toLabel')" outlined dense>
                          <template v-slot:append>
                            <q-icon name="event" class="cursor-pointer" @click="openDatePicker(c, 'to')" />
                          </template>
                        </q-input>
                      </div>
                      <div class="col-12">
                        <q-select
                          v-model="c.skills"
                          :label="$root.t('skillsChipsLabel')"
                          use-input
                          use-chips
                          chip-class="custom-cv-chip"
                          multiple
                          hide-dropdown-icon
                          input-debounce="0"
                          new-value-mode="add-unique"
                          outlined
                          dense
                          class="bg-white"
                          @new-value="createValue"
                        />
                      </div>
                    </div>

                    <div class="text-bold q-mt-md q-mb-xs">{{ $root.t('certificateDetails') }}</div>
                    <q-list bordered separator class="bg-white rounded-borders">
                      <q-item v-for="(d, dindex) in c.description" :key="dindex" class="q-py-xs">
                        <q-item-section side class="flex-row">
                          <q-btn icon="arrow_upward" color="grey-7" flat round dense :disabled="dindex === 0" @click="moveUp(c.description, dindex)" />
                          <q-btn icon="arrow_downward" color="grey-7" flat round dense :disabled="dindex === c.description.length - 1" @click="moveDown(c.description, dindex)" />
                        </q-item-section>
                        <q-item-section>
                          <q-input v-model="d.label" :label="$root.t('educationDetailLabel')" outlined dense />
                        </q-item-section>
                        <q-item-section side>
                          <q-btn icon="delete" color="negative" flat round dense @click="removeCourseDetail(index, dindex)" />
                        </q-item-section>
                      </q-item>
                      <q-item class="q-py-xs flex justify-end">
                        <q-btn :label="$root.t('addCertificateDetail')" icon="add" color="primary" dense flat @click="addCourseDetail(index)" />
                      </q-item>
                    </q-list>
                  </div>
                </q-expansion-item>
                <q-btn :label="$root.t('addCertificate')" icon="add" color="primary" outline class="full-width q-mt-md" @click="addCourse" />
              </q-tab-panel>

              <!-- Tab 7: Altre Competenze (Gestionali e Comunicative) -->
              <q-tab-panel name="skills_extra">
                <!-- Management Skills -->
                <div class="text-h6 text-bold q-mb-sm">{{ $root.t('mgmtSkillsTitle') }}</div>
                
                <q-list bordered separator class="rounded-borders q-mb-lg bg-white">
                  <q-item v-for="(item, index) in localCV.managementSkill.data" :key="index" class="q-py-sm">
                    <q-item-section>
                      <q-input v-model="item.label" :label="$root.t('technicalSkills')" outlined dense />
                    </q-item-section>
                    <q-item-section side>
                      <div class="column items-center">
                        <div class="row">
                          <q-btn icon="arrow_upward" color="grey-7" flat round dense :disabled="index === 0" @click="moveUp(localCV.managementSkill.data, index)" />
                          <q-btn icon="arrow_downward" color="grey-7" flat round dense :disabled="index === localCV.managementSkill.data.length - 1" @click="moveDown(localCV.managementSkill.data, index)" />
                        </div>
                        <div class="row items-center">
                          <q-toggle
                            :model-value="!item.hidden"
                            @update:model-value="val => item.hidden = !val"
                            checked-icon="visibility"
                            unchecked-icon="visibility_off"
                            :color="!item.hidden ? 'primary' : 'negative'"
                            keep-color dense
                          >
                            <q-tooltip>{{ !item.hidden ? $root.t('visible') : $root.t('hidden') }}</q-tooltip>
                          </q-toggle>
                          <q-btn icon="delete" color="negative" flat round dense @click="removeManagementSkill(index)" />
                        </div>
                      </div>
                    </q-item-section>
                  </q-item>
                </q-list>
                <q-btn :label="$root.t('addMgmtSkill')" icon="add" color="primary" outline class="full-width q-mt-md q-mb-lg" @click="addManagementSkill" />

                <!-- Communicative Skills -->
                <div class="text-h6 text-bold q-mb-sm q-mt-lg">{{ $root.t('commSkillsTitle') }}</div>
                
                <q-list bordered separator class="rounded-borders bg-white">
                  <q-item v-for="(item, index) in localCV.comunicativeSkill.data" :key="index" class="q-py-sm">
                    <q-item-section>
                      <q-input v-model="item.label" :label="$root.t('technicalSkills')" outlined dense />
                    </q-item-section>
                    <q-item-section side>
                      <div class="column items-center">
                        <div class="row">
                          <q-btn icon="arrow_upward" color="grey-7" flat round dense :disabled="index === 0" @click="moveUp(localCV.comunicativeSkill.data, index)" />
                          <q-btn icon="arrow_downward" color="grey-7" flat round dense :disabled="index === localCV.comunicativeSkill.data.length - 1" @click="moveDown(localCV.comunicativeSkill.data, index)" />
                        </div>
                        <div class="row items-center">
                          <q-toggle
                            :model-value="!item.hidden"
                            @update:model-value="val => item.hidden = !val"
                            checked-icon="visibility"
                            unchecked-icon="visibility_off"
                            :color="!item.hidden ? 'primary' : 'negative'"
                            keep-color dense
                          >
                            <q-tooltip>{{ !item.hidden ? $root.t('visible') : $root.t('hidden') }}</q-tooltip>
                          </q-toggle>
                          <q-btn icon="delete" color="negative" flat round dense @click="removeCommunicativeSkill(index)" />
                        </div>
                      </div>
                    </q-item-section>
                  </q-item>
                </q-list>
                <q-btn :label="$root.t('addCommSkill')" icon="add" color="primary" outline class="full-width q-mt-md" @click="addCommunicativeSkill" />
              </q-tab-panel>

              <!-- Tab 8: Competenze Digitali -->
              <q-tab-panel name="digital">
                <div class="text-h6 text-bold q-mb-md">{{ $root.t('digitalSkillsTitle') }}</div>

                <q-expansion-item
                  v-for="(ds, index) in localCV.digitalSkill.data"
                  :key="index"
                  v-model="ds.expanded"
                  class="q-mb-md border rounded-borders bg-grey-1 shadow-1"
                  header-class="q-pa-xs bg-white rounded-borders"
                >
                  <template v-slot:header>
                    <div class="row items-center full-width no-wrap">
                      <div class="row items-center no-wrap q-mr-sm" @click.stop>
                        <q-btn icon="arrow_upward" color="grey-7" flat round dense :disabled="index === 0" @click="moveUp(localCV.digitalSkill.data, index)" />
                        <q-btn icon="arrow_downward" color="grey-7" flat round dense :disabled="index === localCV.digitalSkill.data.length - 1" @click="moveDown(localCV.digitalSkill.data, index)" />
                      </div>
                      <div class="col text-subtitle2 text-bold text-grey-8 q-px-sm ellipsis-2-lines" style="line-height: 1.2;">
                        {{ ds.title ? ds.title : $root.t('newDigitalArea') }}
                      </div>
                      <div class="row items-center q-gutter-xs" @click.stop>
                        <q-toggle
                          :model-value="!ds.hidden"
                          @update:model-value="val => ds.hidden = !val"
                          checked-icon="visibility"
                          unchecked-icon="visibility_off"
                          :color="!ds.hidden ? 'primary' : 'negative'"
                          keep-color
                          dense
                          class="q-ml-sm"
                        >
                          <q-tooltip>{{ !ds.hidden ? $root.t('visible') : $root.t('hidden') }}</q-tooltip>
                        </q-toggle>
                        <q-btn icon="delete" color="negative" flat round dense @click="removeDigitalArea(index)" />
                      </div>
                    </div>
                  </template>

                  <div class="q-pa-md">
                    <div class="row">
                      <div class="col-12">
                        <q-input v-model="ds.title" :label="$root.t('digitalAreaTitle')" outlined dense />
                      </div>
                    </div>

                    <div class="text-bold q-mt-md q-mb-xs">{{ $root.t('digitalDetails') }}</div>
                    <q-list bordered separator class="bg-white rounded-borders">
                      <q-item v-for="(d, dindex) in ds.description" :key="dindex" class="q-py-sm">
                        <q-item-section side class="flex-row">
                          <q-btn icon="arrow_upward" color="grey-7" flat round dense :disabled="dindex === 0" @click="moveUp(ds.description, dindex)" />
                          <q-btn icon="arrow_downward" color="grey-7" flat round dense :disabled="dindex === ds.description.length - 1" @click="moveDown(ds.description, dindex)" />
                        </q-item-section>
                        <q-item-section>
                          <div class="row q-col-gutter-xs">
                            <div class="col-12 col-sm-6">
                              <q-input v-model="d.label" :label="$root.t('projectDescription')" outlined dense />
                            </div>
                            <div class="col-12 col-sm-6">
                              <q-input v-model="d.link" :label="$root.t('projectLink')" outlined dense />
                            </div>
                          </div>
                        </q-item-section>
                        <q-item-section side>
                          <q-btn icon="delete" color="negative" flat round dense @click="removeDigitalDetail(index, dindex)" />
                        </q-item-section>
                      </q-item>
                      <q-item class="q-py-xs flex justify-end">
                        <q-btn :label="$root.t('addProject')" icon="add" color="primary" dense flat @click="addDigitalDetail(index)" />
                      </q-item>
                    </q-list>
                  </div>
                </q-expansion-item>
                <q-btn :label="$root.t('addDigitalArea')" icon="add" color="primary" outline class="full-width q-mt-md" @click="addDigitalArea" />
              </q-tab-panel>

              <!-- Tab 9: Privacy & Altro -->
              <q-tab-panel name="privacy">
                <div class="text-h6 text-bold q-mb-md">{{ $root.t('additionalInfoTitle') }}</div>
                <q-expansion-item
                  v-for="(o, index) in localCV.others.data"
                  :key="index"
                  v-model="o.expanded"
                  class="q-mb-md border rounded-borders bg-grey-1 shadow-1"
                  header-class="q-pa-xs bg-white rounded-borders"
                >
                  <template v-slot:header>
                    <div class="row items-center full-width no-wrap">
                      <div class="row items-center no-wrap q-mr-sm" @click.stop>
                        <q-btn icon="arrow_upward" color="grey-7" flat round dense :disabled="index === 0" @click="moveUp(localCV.others.data, index)" />
                        <q-btn icon="arrow_downward" color="grey-7" flat round dense :disabled="index === localCV.others.data.length - 1" @click="moveDown(localCV.others.data, index)" />
                      </div>
                      <div class="col text-subtitle2 text-bold text-grey-8 q-px-sm ellipsis-2-lines" style="line-height: 1.2;">
                        {{ o.title ? o.title : $root.t('newSectionLabel') }}
                      </div>
                      <div class="row items-center q-gutter-xs" @click.stop>
                        <q-toggle
                          :model-value="!o.hidden"
                          @update:model-value="val => o.hidden = !val"
                          checked-icon="visibility"
                          unchecked-icon="visibility_off"
                          :color="!o.hidden ? 'primary' : 'negative'"
                          keep-color
                          dense
                          class="q-ml-sm"
                        >
                          <q-tooltip>{{ !o.hidden ? $root.t('visible') : $root.t('hidden') }}</q-tooltip>
                        </q-toggle>
                        <q-btn icon="delete" color="negative" flat round dense @click="removeOther(index)" />
                      </div>
                    </div>
                  </template>

                  <div class="q-pa-md">
                    <q-input v-model="o.title" :label="$root.t('sectionTitleLabel')" outlined dense class="q-mb-sm" />
                    <q-select
                      v-model="o.skills"
                      :label="$root.t('skillsChipsLabel')"
                      use-input
                      use-chips
                      chip-class="custom-cv-chip"
                      multiple
                      hide-dropdown-icon
                      input-debounce="0"
                      new-value-mode="add-unique"
                      outlined
                      dense
                      class="bg-white q-mb-sm"
                      @new-value="createValue"
                    />

                    <q-list bordered separator class="bg-white rounded-borders">
                      <q-item v-for="(d, dindex) in o.description" :key="dindex" class="q-py-xs">
                        <q-item-section side class="flex-row">
                          <q-btn icon="arrow_upward" color="grey-7" flat round dense :disabled="dindex === 0" @click="moveUp(o.description, dindex)" />
                          <q-btn icon="arrow_downward" color="grey-7" flat round dense :disabled="dindex === o.description.length - 1" @click="moveDown(o.description, dindex)" />
                        </q-item-section>
                        <q-item-section>
                          <q-input v-model="d.label" :label="$root.t('educationDetailLabel')" outlined dense />
                        </q-item-section>
                        <q-item-section side>
                          <q-btn icon="delete" color="negative" flat round dense @click="removeOtherDetail(index, dindex)" />
                        </q-item-section>
                      </q-item>
                      <q-item class="q-py-xs flex justify-end">
                        <q-btn :label="$root.t('addCertificateDetail')" icon="add" color="primary" dense flat @click="addOtherDetail(index)" />
                      </q-item>
                    </q-list>
                  </div>
                </q-expansion-item>
                <q-btn :label="$root.t('addSection')" icon="add" color="primary" outline class="full-width q-mt-md" @click="addOther" />

                <q-separator class="q-my-lg" />

                <div class="text-subtitle1 text-bold q-mb-sm">{{ $root.t('privacyLabelTitle') }}</div>
                <q-input
                  v-model="localCV.privacy.description"
                  type="textarea"
                  rows="3"
                  :label="$root.t('privacyStatement')"
                  outlined
                  dense
                />
              </q-tab-panel>

            </q-tab-panels>
          </q-card>
        </div>
      </div>
    </div>

    <!-- Reusable Date Picker Dialog -->
    <q-dialog v-model="datePickerDialog.show" persistent>
      <q-card style="min-width: 320px; max-width: 90vw">
        <q-card-section class="row items-center q-pb-none">
          <div class="text-h6 text-bold">Scegli Data</div>
          <q-space />
          <q-btn icon="close" flat round dense v-close-popup />
        </q-card-section>

        <q-card-section class="q-pt-none">
          <q-tabs v-model="datePickerDialog.mode" class="text-primary" dense align="justify">
            <q-tab name="monthYear" label="Mese/Anno" />
            <q-tab name="full" label="Completa" />
            <q-tab name="year" label="Solo Anno" />
          </q-tabs>
          
          <q-tab-panels v-model="datePickerDialog.mode" animated class="q-mt-sm">
            <q-tab-panel name="monthYear" class="q-pa-none">
              <q-date
                v-model="datePickerDialog.tempValue"
                mask="MMMM YYYY"
                default-view="Years"
                years-in-month-view
                emit-immediately
                minimal
                class="full-width"
              />
            </q-tab-panel>

            <q-tab-panel name="full" class="q-pa-none">
              <q-date
                v-model="datePickerDialog.tempValue"
                mask="D MMMM YYYY"
                minimal
                class="full-width"
              />
            </q-tab-panel>

            <q-tab-panel name="year" class="q-pa-none">
              <q-date
                v-model="datePickerDialog.tempValue"
                mask="YYYY"
                default-view="Years"
                emit-immediately
                minimal
                class="full-width"
              />
            </q-tab-panel>
          </q-tab-panels>
        </q-card-section>

        <q-card-actions align="right">
          <q-btn label="Annulla" color="grey-8" flat v-close-popup />
          <q-btn label="Conferma" color="primary" @click="confirmDatePicker" class="text-bold" />
        </q-card-actions>
      </q-card>
    </q-dialog>

      </q-page>
    </q-page-container>

    <app-footer />
  </q-layout>
</template>

<script>
import AppFooter from 'components/AppFooter.vue'

export default {
  name: 'AdminPanel',
  components: {
    AppFooter
  },
  data () {
    return {
      isAuthorized: false,
      password: '',
      activeTab: 'info',
      birthDate: '',
      selectedLang: 'it',
      prevSelectedLang: 'it',
      pwForm: { current: '', next: '', confirm: '' },
      showLoginPwd: false,
      showPwdForm: false,
      showPwdNext: false,
      showPwdConfirm: false,
      datePickerDialog: {
        show: false,
        mode: 'monthYear',
        targetObj: null,
        targetKey: '',
        tempValue: ''
      },
      localCV: {
        info: { data: [] },
        exp: { data: [] },
        skill: { data: [] },
        lang: { data: [] },
        school: { data: [] },
        courses: { data: [] },
        managementSkill: { data: [] },
        comunicativeSkill: { data: [] },
        digitalSkill: { data: [] },
        others: { data: [] },
        privacy: {}
      },
      avatarTs: Date.now(),
      themeOptions: [
        { label: 'Default (Quasar Blue)', value: 'default', colors: { primary: '#1976D2', secondary: '#26A69A', chipBg: '#e0e0e0' } },
        { label: 'Navy & Charcoal', value: 'navy', colors: { primary: '#1F3864', secondary: '#333333', chipBg: '#E1EBF5' } },
        { label: 'Forest & Sand', value: 'forest', colors: { primary: '#1E4D2B', secondary: '#8F754F', chipBg: '#E2EDE4' } },
        { label: 'Burgundy & Gold', value: 'burgundy', colors: { primary: '#6E1F2E', secondary: '#A68A56', chipBg: '#F7EBEF' } },
        { label: 'Teal & Slate', value: 'teal', colors: { primary: '#168F8B', secondary: '#4A5568', chipBg: '#E2F1F0' } },
        { label: 'Dark Slate & Orange', value: 'slate', colors: { primary: '#2D3748', secondary: '#DD6B20', chipBg: '#EDF2F7' } },
        { label: 'Pink', value: 'pink', colors: { primary: '#E91E63', secondary: '#F48FB1', chipBg: '#FCE4EC' } },
        { label: 'Magenta', value: 'magenta', colors: { primary: '#9C27B0', secondary: '#BA68C8', chipBg: '#F3E5F5' } },
        { label: 'Darcula', value: 'darcula', colors: { primary: '#2B2B2B', secondary: '#A9B7C6', chipBg: '#FFCB42' } },
        { label: 'Vaporwave', value: 'vaporwave', colors: { primary: '#FF71CE', secondary: '#01CDFE', chipBg: '#FFF3FA' } }
      ]
    }
  },
  computed: {
    languagesOptions () {
      if (!window.mainVue || !window.mainVue.languagesList) return []
      return window.mainVue.languagesList.map(l => ({
        value: l.code,
        label: `${l.emoji} ${l.label}`
      }))
    },
    canDeleteCurrentLang () {
      if (!window.mainVue || !window.mainVue.rawCV || !window.mainVue.rawCV.languages) return false
      return window.mainVue.rawCV.languages.length > 1
    },
    activeLanguagesOptions () {
      if (!window.mainVue || !window.mainVue.languagesList || !window.mainVue.rawCV || !window.mainVue.rawCV.languages) return []
      return window.mainVue.languagesList
        .filter(l => window.mainVue.rawCV.languages.includes(l.code))
        .map(l => ({
          value: l.code,
          label: `${l.emoji} ${l.label}`
        }))
    }
  },
  mounted () {
    // Wait for CV data to load from MainApp if already fetched
    if (window.mainVue && window.mainVue.rawCV && window.mainVue.rawCV.data) {
      this.cloneCV()
    } else {
      setTimeout(() => {
        this.cloneCV()
      }, 1000)
    }
  },
  methods: {
    async uploadAvatar (file) {
      if (!file) return
      try {
        await this.$axios.post('/api/save-avatar', file, {
          headers: { 'Content-Type': file.type }
        })
        window.mainVue.rawCV.img = '/avatar.jpg'
        this.avatarTs = Date.now()
        this.$q.notify({ type: 'positive', message: 'Immagine caricata!' })
      } catch (e) {
        this.$q.notify({ type: 'negative', message: 'Errore upload immagine (dev server non disponibile)' })
      }
    },
    moveUp (array, index) {
      if (index > 0) {
        const item = array[index]
        array.splice(index, 1)
        array.splice(index - 1, 0, item)
      }
    },
    moveDown (array, index) {
      if (index < array.length - 1) {
        const item = array[index]
        array.splice(index, 1)
        array.splice(index + 1, 0, item)
      }
    },
    openDatePicker (obj, key) {
      if (obj === null) {
        this.datePickerDialog.targetObj = this
        this.datePickerDialog.targetKey = key
        this.datePickerDialog.tempValue = this[key] || ''
      } else {
        this.datePickerDialog.targetObj = obj
        this.datePickerDialog.targetKey = key
        this.datePickerDialog.tempValue = obj[key] || ''
      }
      
      const val = (obj === null ? this[key] : obj[key]) || ''
      if (/^\d{4}$/.test(val)) {
        this.datePickerDialog.mode = 'year'
      } else if (val.split(' ').length > 2) {
        this.datePickerDialog.mode = 'full'
      } else {
        this.datePickerDialog.mode = 'monthYear'
      }
      
      this.datePickerDialog.show = true
    },
    confirmDatePicker () {
      if (this.datePickerDialog.targetObj && this.datePickerDialog.targetKey) {
        this.datePickerDialog.targetObj[this.datePickerDialog.targetKey] = this.datePickerDialog.tempValue
      }
      this.datePickerDialog.show = false
    },
    createValue (val, done) {
      if (val.length > 0) {
        const parts = val.split(',').map(v => v.trim()).filter(v => v.length > 0)
        parts.forEach(part => {
          done(part, 'add-unique')
        })
      }
    },
    async sha256 (str) {
      const buf = await crypto.subtle.digest('SHA-256', new TextEncoder().encode(str))
      return Array.from(new Uint8Array(buf)).map(b => b.toString(16).padStart(2, '0')).join('')
    },
    async authorize () {
      const stored = window.mainVue && window.mainVue.rawCV && window.mainVue.rawCV.adminPassword
      let ok = false
      if (stored) {
        ok = (await this.sha256(this.password)) === stored
      } else {
        ok = this.password === 'admin'
      }
      if (ok) {
        this.isAuthorized = true
        this.selectedLang = this.$root.currentLang
        this.prevSelectedLang = this.$root.currentLang
        this.cloneCV()

        this.$q.notify({ type: 'positive', message: this.$root.t('accessAuthorized') })
        if (!stored) {
          this.$q.notify({
            type: 'warning',
            icon: 'mdi-alert',
            message: this.$root.t('noPasswordSet'),
            timeout: 6000
          })
          this.activeTab = 'pwd'
        }
      } else {
        this.$q.notify({ type: 'negative', message: this.$root.t('wrongPassword') })
      }
    },
    async changePassword () {
      const stored = window.mainVue && window.mainVue.rawCV && window.mainVue.rawCV.adminPassword
      const currentOk = stored
        ? (await this.sha256(this.pwForm.current)) === stored
        : this.pwForm.current === 'admin'
      if (!currentOk) {
        this.$q.notify({ type: 'negative', message: 'Password attuale non corretta' }); return
      }
      if (!this.pwForm.next || this.pwForm.next !== this.pwForm.confirm) {
        this.$q.notify({ type: 'negative', message: 'Le nuove password non coincidono' }); return
      }
      window.mainVue.rawCV.adminPassword = await this.sha256(this.pwForm.next)
      this.pwForm = { current: '', next: '', confirm: '' }
      await this.saveChanges()
      this.$q.notify({ type: 'positive', message: 'Password aggiornata!' })
    },
    cloneCV () {
      if (window.mainVue && window.mainVue.rawCV && window.mainVue.rawCV.data) {
        const source = window.mainVue.rawCV.data[this.selectedLang] || {}
        this.localCV = JSON.parse(JSON.stringify(source))
        const sections = ['info', 'exp', 'skill', 'lang', 'school', 'courses', 'managementSkill', 'comunicativeSkill', 'digitalSkill', 'others']
        sections.forEach(sec => {
          if (!this.localCV[sec]) {
            this.localCV[sec] = {}
          }
          if (!this.localCV[sec].data) {
            this.localCV[sec].data = []
          }
        })
        if (!this.localCV.privacy) {
          this.localCV.privacy = {}
        }
        if (this.localCV.exp && this.localCV.exp.data) {
          this.localCV.exp.data.forEach(e => {
            if (!e.skills) e.skills = []
            e.expanded = false
          })
        }
        if (this.localCV.school && this.localCV.school.data) {
          this.localCV.school.data.forEach(s => {
            if (!s.skills) s.skills = []
            s.expanded = false
          })
        }
        if (this.localCV.courses && this.localCV.courses.data) {
          this.localCV.courses.data.forEach(c => {
            if (!c.skills) c.skills = []
            c.expanded = false
          })
        }
        if (this.localCV.digitalSkill && this.localCV.digitalSkill.data) {
          this.localCV.digitalSkill.data.forEach(ds => {
            ds.expanded = false
          })
        }
        if (this.localCV.others && this.localCV.others.data) {
          this.localCV.others.data.forEach(o => {
            if (!o.skills) o.skills = []
            o.expanded = false
          })
        }
        this.birthDate = window.mainVue.rawCV.birthDate || ''
      }
    },
    async saveChanges () {
      // Sync global fields back to rawCV
      window.mainVue.rawCV.birthDate = this.birthDate

      // Update the reactive global state's language data
      window.mainVue.rawCV.data[this.selectedLang] = JSON.parse(JSON.stringify(this.localCV))
      
      // Ensure the language is marked as active/available
      if (!window.mainVue.rawCV.languages.includes(this.selectedLang)) {
        window.mainVue.rawCV.languages.push(this.selectedLang)
      }

      try {
        // Send POST request to dev server API endpoint
        const response = await this.$axios.post('/api/save-cv', window.mainVue.rawCV)
        if (response.data && response.data.success) {
          this.$q.notify({
            type: 'positive',
            message: 'Modifiche salvate con successo nel file cv.json!'
          })
          // Sync frontend cv if currentLang matches selectedLang
          if (window.mainVue.currentLang === this.selectedLang) {
            window.mainVue.cv = window.mainVue.rawCV.data[this.selectedLang]
          }
        }
      } catch (err) {
        console.warn('Dev server API not available or error saving:', err)
        // Fallback for static hosting: trigger file download of cv.json
        this.downloadJSON(window.mainVue.rawCV)
        this.$q.notify({
          type: 'warning',
          message: 'Dev server non disponibile. Il file cv.json è stato scaricato nei Download del browser.',
          caption: 'Sostituisci manualmente il file public/cv.json con quello scaricato.'
        })
      }
    },
    downloadJSON (data) {
      const dataStr = 'data:text/json;charset=utf-8,' + encodeURIComponent(JSON.stringify(data, null, 2))
      const downloadAnchor = document.createElement('a')
      downloadAnchor.setAttribute('href', dataStr)
      downloadAnchor.setAttribute('download', 'cv.json')
      document.body.appendChild(downloadAnchor)
      downloadAnchor.click()
      downloadAnchor.remove()
    },
    downloadSettings () {
      this.downloadJSON(window.mainVue.rawCV)
      this.$q.notify({ type: 'positive', message: this.$root.t('downloadSettingsSuccess') })
    },
    importSettings (event) {
      const file = event.target.files[0]
      if (!file) return
      const reader = new FileReader()
      reader.onload = (e) => {
        try {
          const imported = JSON.parse(e.target.result)
          if (!imported || typeof imported !== 'object' || !imported.data) throw new Error('invalid')
          window.mainVue.rawCV = imported
          this.cloneCV()
          if (window.mainVue.rawCV.theme) {
            window.mainVue.applyTheme(window.mainVue.rawCV.theme)
          }
          this.$q.notify({ type: 'positive', message: this.$root.t('importSettingsSuccess') })
        } catch {
          this.$q.notify({ type: 'negative', message: this.$root.t('importSettingsError') })
        }
      }
      reader.readAsText(file)
      // Reset input so the same file can be re-imported
      event.target.value = ''
    },
    onThemeChange (val) {
      if (window.mainVue) {
        window.mainVue.applyTheme(val)
      }
    },

    // Form Manipulation Methods
    addInfoData () {
      this.localCV.info.data.push({ label: '', value: '', link: null, hidden: false })
    },
    removeInfoData (index) {
      this.confirmDelete('Vuoi eliminare questo dato di contatto?', () => {
        this.localCV.info.data.splice(index, 1)
      })
    },
    addExperience () {
      this.localCV.exp.data.push({
        city: '',
        from: '',
        to: '',
        separator: '-',
        title: '',
        where: '',
        description: [],
        skills: [],
        hidden: false,
        expanded: true
      })
    },
    removeExperience (index) {
      this.confirmDelete('Vuoi eliminare questa esperienza lavorativa?', () => {
        this.localCV.exp.data.splice(index, 1)
      })
    },
    addExperienceDetail (index) {
      this.localCV.exp.data[index].description.push({ label: '', link: null })
    },
    removeExperienceDetail (index, dindex) {
      this.confirmDelete('Vuoi eliminare questa mansione?', () => {
        this.localCV.exp.data[index].description.splice(dindex, 1)
      })
    },
    addSkill () {
      this.localCV.skill.data.push({ label: '', link: null, hidden: false })
    },
    removeSkill (index) {
      this.confirmDelete('Vuoi eliminare questa competenza?', () => {
        this.localCV.skill.data.splice(index, 1)
      })
    },
    addLanguage () {
      this.localCV.lang.data.push({ countrycode: '', label: '', level: '', hidden: false })
    },
    removeLanguage (index) {
      this.confirmDelete('Vuoi eliminare questa lingua?', () => {
        this.localCV.lang.data.splice(index, 1)
      })
    },
    addSchool () {
      this.localCV.school.data.push({
        city: '',
        from: '',
        to: '',
        separator: '-',
        title: '',
        where: '',
        description: [],
        skills: [],
        hidden: false,
        expanded: true
      })
    },
    removeSchool (index) {
      this.confirmDelete('Vuoi eliminare questo titolo di studio?', () => {
        this.localCV.school.data.splice(index, 1)
      })
    },
    addSchoolDetail (index) {
      this.localCV.school.data[index].description.push({ label: '', link: null })
    },
    removeSchoolDetail (index, dindex) {
      this.confirmDelete('Vuoi eliminare questo dettaglio di studio?', () => {
        this.localCV.school.data[index].description.splice(dindex, 1)
      })
    },
    addCourse () {
      this.localCV.courses.data.push({
        city: '',
        from: '',
        to: '',
        separator: '-',
        title: '',
        where: '',
        description: [],
        skills: [],
        hidden: false,
        expanded: true
      })
    },
    removeCourse (index) {
      this.confirmDelete('Vuoi eliminare questo corso/certificato?', () => {
        this.localCV.courses.data.splice(index, 1)
      })
    },
    addCourseDetail (index) {
      this.localCV.courses.data[index].description.push({ label: '', link: null })
    },
    removeCourseDetail (index, dindex) {
      this.confirmDelete('Vuoi eliminare questo dettaglio?', () => {
        this.localCV.courses.data[index].description.splice(dindex, 1)
      })
    },
    addManagementSkill () {
      this.localCV.managementSkill.data.push({ label: '', link: null, hidden: false })
    },
    removeManagementSkill (index) {
      this.confirmDelete('Vuoi eliminare questa competenza gestionale?', () => {
        this.localCV.managementSkill.data.splice(index, 1)
      })
    },
    addCommunicativeSkill () {
      this.localCV.comunicativeSkill.data.push({ label: '', link: null, hidden: false })
    },
    removeCommunicativeSkill (index) {
      this.confirmDelete('Vuoi eliminare questa competenza comunicativa?', () => {
        this.localCV.comunicativeSkill.data.splice(index, 1)
      })
    },
    addDigitalArea () {
      this.localCV.digitalSkill.data.push({ title: '', description: [], hidden: false, expanded: true })
    },
    removeDigitalArea (index) {
      this.confirmDelete('Vuoi eliminare questa area di progetti digitali?', () => {
        this.localCV.digitalSkill.data.splice(index, 1)
      })
    },
    addDigitalDetail (index) {
      this.localCV.digitalSkill.data[index].description.push({ label: '', link: null })
    },
    removeDigitalDetail (index, dindex) {
      this.confirmDelete('Vuoi eliminare questo progetto/dettaglio?', () => {
        this.localCV.digitalSkill.data[index].description.splice(dindex, 1)
      })
    },
    addOtherDetail (index) {
      this.localCV.others.data[index].description.push({ label: '', link: null })
    },
    removeOtherDetail (index, dindex) {
      this.confirmDelete('Vuoi eliminare questo dettaglio?', () => {
        this.localCV.others.data[index].description.splice(dindex, 1)
      })
    },
    addOther () {
      this.localCV.others.data.push({
        title: '',
        description: [],
        skills: [],
        hidden: false,
        expanded: true
      })
    },
    removeOther (index) {
      this.confirmDelete('Vuoi eliminare questa sezione addizionale?', () => {
        this.localCV.others.data.splice(index, 1)
      })
    },
    confirmDelete (message, callback) {
      this.$q.dialog({
        title: this.$root.t('confirmDeleteTitle'),
        message: message || this.$root.t('confirmDeleteMsg'),
        cancel: {
          label: this.$root.t('cancel'),
          color: 'grey-7',
          flat: true
        },
        ok: {
          label: this.$root.t('deleteBtn'),
          color: 'negative',
          unelevated: true
        },
        persistent: true
      }).onOk(() => {
        callback()
      })
    },
    async onLangChange (newVal) {
      if (this.hasUnsavedChanges()) {
        this.$q.dialog({
          title: this.$root.t('unsavedChangesTitle'),
          message: this.$root.t('unsavedChangesMsg'),
          cancel: {
            label: this.$root.t('stayHere'),
            color: 'grey-7',
            flat: true
          },
          ok: {
            label: this.$root.t('changeAnyway'),
            color: 'negative',
            unelevated: true
          },
          persistent: true
        }).onOk(async () => {
          this.prevSelectedLang = newVal
          await this.loadLanguageInAdmin(newVal)
        }).onCancel(() => {
          this.selectedLang = this.prevSelectedLang
        })
      } else {
        this.prevSelectedLang = newVal
        await this.loadLanguageInAdmin(newVal)
      }
    },
    async loadLanguageInAdmin (lang) {
      if (window.mainVue.rawCV.data[lang]) {
        this.$root.currentLang = lang
        this.cloneCV()
      } else {
        this.$q.dialog({
          title: this.$root.t('newLanguageTitle'),
          message: `${this.$root.t('newLanguageMsg')} (${this.getLangLabel(lang)})`,
          options: {
            type: 'radio',
            model: 'import',
            items: [
              { label: this.$root.t('importExisting'), value: 'import' },
              { label: this.$root.t('startEmpty'), value: 'empty' }
            ]
          },
          cancel: {
            label: this.$root.t('cancel'),
            color: 'grey-7',
            flat: true
          },
          persistent: true
        }).onOk(action => {
          if (action === 'empty') {
            this.initializeEmptyCV(lang)
          } else if (action === 'import') {
            this.promptImportSource(lang)
          }
        }).onCancel(() => {
          this.selectedLang = this.prevSelectedLang
        })
      }
    },
    getLangLabel (lang) {
      const found = window.mainVue.languagesList.find(l => l.code === lang)
      return found ? `${found.emoji} ${found.label}` : lang
    },
    getSelectedLangEmoji (opt) {
      if (!opt) return ''
      if (this.$q.screen.lt.sm) {
        const code = typeof opt === 'object' ? opt.value : opt
        const found = window.mainVue.languagesList.find(l => l.code === code)
        return found ? found.emoji : ''
      }
      return typeof opt === 'object' ? opt.label : opt
    },
    tForLang (key, lang) {
      const translations = window.mainVue ? window.mainVue.translations : {}
      const dict = translations[lang] || translations['it'] || {}
      return dict[key] || translations['it'][key] || key
    },
    initializeEmptyCV (lang) {
      const emptyCV = {
        info: { icon: 'mdi-account', title: '', description: '', about: '', data: [] },
        exp: { icon: 'mdi-briefcase', title: this.tForLang('experience', lang), data: [] },
        skill: { icon: 'mdi-tools', title: this.tForLang('skills', lang), data: [] },
        lang: { icon: 'mdi-translate', title: this.tForLang('languages', lang), data: [] },
        school: { icon: 'mdi-school', title: this.tForLang('education', lang), data: [] },
        courses: { icon: 'mdi-teach', title: this.tForLang('certificates', lang), data: [] },
        managementSkill: { icon: 'mdi-account-group', title: this.tForLang('mgmtSkillsTitle', lang), data: [] },
        comunicativeSkill: { icon: 'mdi-account-voice', title: this.tForLang('commSkillsTitle', lang), data: [] },
        digitalSkill: { icon: 'mdi-laptop', title: this.tForLang('digitalSkillsTitle', lang), data: [] },
        others: { icon: 'mdi-information', title: this.tForLang('additionalInfoTitle', lang), data: [] },
        privacy: { icon: 'mdi-draw', title: this.tForLang('privacyLabelTitle', lang), description: '' }
      }
      window.mainVue.rawCV.data[lang] = emptyCV
      if (!window.mainVue.rawCV.languages.includes(lang)) {
        window.mainVue.rawCV.languages.push(lang)
      }
      this.$root.currentLang = lang
      this.cloneCV()
      this.$q.notify({
        type: 'positive',
        message: `Struttura vuota creata per ${this.getLangLabel(lang)}!`
      })
    },
    promptImportSource (lang) {
      const available = window.mainVue.rawCV.languages.map(code => {
        const found = window.mainVue.languagesList.find(l => l.code === code)
        return {
          label: found ? `${found.emoji} ${found.label}` : code,
          value: code
        }
      })

      this.$q.dialog({
        title: this.$root.t('selectSourceTitle'),
        message: this.$root.t('selectSourceMsg'),
        options: {
          type: 'radio',
          model: available[0] ? available[0].value : 'it',
          items: available
        },
        cancel: {
          label: this.$root.t('cancel'),
          color: 'grey-7',
          flat: true
        },
        persistent: true
      }).onOk(sourceLang => {
        const sourceData = JSON.parse(JSON.stringify(window.mainVue.rawCV.data[sourceLang]))
        if (sourceData.exp) sourceData.exp.title = this.tForLang('experience', lang)
        if (sourceData.skill) sourceData.skill.title = this.tForLang('skills', lang)
        if (sourceData.lang) sourceData.lang.title = this.tForLang('languages', lang)
        if (sourceData.school) sourceData.school.title = this.tForLang('education', lang)
        if (sourceData.courses) sourceData.courses.title = this.tForLang('certificates', lang)
        if (sourceData.managementSkill) sourceData.managementSkill.title = this.tForLang('mgmtSkillsTitle', lang)
        if (sourceData.comunicativeSkill) sourceData.comunicativeSkill.title = this.tForLang('commSkillsTitle', lang)
        if (sourceData.digitalSkill) sourceData.digitalSkill.title = this.tForLang('digitalSkillsTitle', lang)
        if (sourceData.others) sourceData.others.title = this.tForLang('additionalInfoTitle', lang)
        if (sourceData.privacy) sourceData.privacy.title = this.tForLang('privacyLabelTitle', lang)

        window.mainVue.rawCV.data[lang] = sourceData
        if (!window.mainVue.rawCV.languages.includes(lang)) {
          window.mainVue.rawCV.languages.push(lang)
        }
        this.$root.currentLang = lang
        this.cloneCV()
        this.$q.notify({
          type: 'positive',
          message: `Dati importati con successo da ${this.getLangLabel(sourceLang)}!`
        })
      }).onCancel(() => {
        this.selectedLang = this.prevSelectedLang
      })
    },
    hasUnsavedChanges () {
      if (!window.mainVue || !window.mainVue.rawCV || !window.mainVue.rawCV.data) return false
      const original = window.mainVue.rawCV.data[this.prevSelectedLang]
      if (!original) return false
      
      const cleanOriginal = this.stripExpanded(original)
      const cleanLocal = this.stripExpanded(this.localCV)
      return JSON.stringify(cleanOriginal) !== JSON.stringify(cleanLocal)
    },
    stripExpanded (obj) {
      if (obj === null || typeof obj !== 'object') return obj
      if (Array.isArray(obj)) {
        return obj.map(item => this.stripExpanded(item))
      }
      const clean = {}
      for (const key in obj) {
        if (key !== 'expanded') {
          clean[key] = this.stripExpanded(obj[key])
        }
      }
      return clean
    },
    goBackWithCheck () {
      if (this.hasUnsavedChanges()) {
        this.$q.dialog({
          title: this.$root.t('unsavedChangesTitle'),
          message: this.$root.t('unsavedBackMsg'),
          cancel: {
            label: this.$root.t('stayHere'),
            color: 'grey-7',
            flat: true
          },
          ok: {
            label: this.$root.t('backToCV'),
            color: 'negative',
            unelevated: true
          },
          persistent: true
        }).onOk(() => {
          this.$router.push('/')
        })
      } else {
        this.$router.push('/')
      }
    },
    confirmDeleteLang () {
      this.$q.dialog({
        title: this.$root.t('deleteLangConfirmTitle'),
        message: `${this.$root.t('deleteLangConfirmMsg')} (${this.getLangLabel(this.selectedLang)})`,
        cancel: {
          label: this.$root.t('cancel'),
          color: 'grey-7',
          flat: true
        },
        ok: {
          label: this.$root.t('deleteBtn'),
          color: 'negative',
          unelevated: true
        },
        persistent: true
      }).onOk(async () => {
        const langToDelete = this.selectedLang
        window.mainVue.rawCV.languages = window.mainVue.rawCV.languages.filter(l => l !== langToDelete)
        delete window.mainVue.rawCV.data[langToDelete]

        const remainingLang = window.mainVue.rawCV.languages[0] || 'it'
        this.selectedLang = remainingLang
        this.prevSelectedLang = remainingLang

        try {
          const response = await this.$axios.post('/api/save-cv', window.mainVue.rawCV)
          if (response.data && response.data.success) {
            this.$q.notify({
              type: 'positive',
              message: this.$root.t('deleteLangSuccess')
            })
            if (window.mainVue.currentLang === langToDelete) {
              window.mainVue.setLanguage(remainingLang)
            }
            this.cloneCV()
          }
        } catch (err) {
          console.warn('Error saving cv after language deletion:', err)
          this.downloadJSON(window.mainVue.rawCV)
          this.$q.notify({
            type: 'warning',
            message: 'Dev server non disponibile. Il file cv.json è stato scaricato.'
          })
          this.cloneCV()
        }
      })
    }
  }
}
</script>

<style scoped>
.max-width-container {
  max-width: 1200px;
  margin: 0 auto;
}
.min-height-card {
  min-height: 60vh;
}
.border {
  border: 1px solid #e0e0e0;
}
.flex-row {
  flex-direction: row;
  align-items: center;
}
:deep(.q-field__control) {
  background-color: white !important;
}
</style>
