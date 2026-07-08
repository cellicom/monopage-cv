<template>
  <q-footer class="bg-grey-1 text-grey-8 print-hide text-center q-py-xs" bordered>
    <div>
      <a :href="pkg.repository.url" target="_blank" class="text-primary text-weight-bold">{{ pkg.name }}</a>
      v{{ pkg.version }} - Made with
      <router-link to="/admin" class="text-red" style="text-decoration:none; cursor:pointer;">♥</router-link>
      by
      <a :href="pkg.author.url" target="_blank" class="text-primary text-weight-bold">{{ pkg.author.name }}</a>
    </div>
  </q-footer>
</template>

<script>
import pkg from '../../package.json'

export default {
  name: 'AppFooter',
  data () {
    return {
      pkg
    }
  }
}
</script>
