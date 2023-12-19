<script setup lang="ts">
import { useAuthStore } from '@/stores/authStore'
import router from '@/router'
import {onMounted} from "vue";

const authStore = useAuthStore()

const submitHandler = e => {
  authStore.loading = true
  const form = e.target
  authStore.loginHandler({
    email: form.email.value,
    password: form.password.value,
    to: router.currentRoute.value.query.from || 'home'
  })
  form.email.value = ''
  form.password.value = ''
}

onMounted(async () => {
  authStore.errors = []
})
</script>

<template>
  <div class="loader" v-if="authStore.loading" />
  <form v-else class="form_container" @submit.prevent='submitHandler' autoComplete="off">
    <div v-if="authStore.errors?.email && authStore.errors?.email[0].includes('These credentials')" class="error-msg top-error-msg">
      {{authStore.errors?.email[0]}}
    </div>
    <div>
      <input name="email" type="text" placeholder="Email" />
      <div v-if="authStore.errors?.email && !authStore.errors?.email[0].includes('These credentials')" class="error-msg">
        {{authStore.errors?.email[0]}}
      </div>
    </div>
    <div>
      <input name="password" type="text" placeholder="Password" />
      <div v-if="authStore.errors?.password" class="error-msg">
        {{authStore.errors?.password[0]}}
      </div>
    </div>
    <input type="submit" value="Sign in" />
  </form>
</template>

<style scoped>
</style>
