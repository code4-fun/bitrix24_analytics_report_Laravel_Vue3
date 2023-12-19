<script setup lang="ts">
import { useAuthStore } from '@/stores/authStore'
import {onMounted} from "vue";

const authStore = useAuthStore()

const submitHandler = e => {
  authStore.loading = true
  const form = e.target
  authStore.registerHandler({
    name: form.name.value,
    email: form.email.value,
    password: form.password.value,
    password_confirmation: form.password_confirmation.value
  })
  form.name.value = ''
  form.email.value = ''
  form.password.value = ''
  form.password_confirmation.value = ''
}

onMounted(async () => {
  authStore.errors = []
})
</script>

<template>
  <div class="loader" v-if="authStore.loading" />
  <form v-else class="form_container" @submit.prevent='submitHandler' autoComplete="off">
    <div>
      <input name="name" type="text" placeholder="Name" />
      <div v-if="authStore.errors?.name" class="error-msg">
        {{authStore.errors?.name[0]}}
      </div>
    </div>
    <div>
      <input name="email" type="text" placeholder="Email" />
      <div v-if="authStore.errors?.email" class="error-msg">
        {{authStore.errors?.email[0]}}
      </div>
    </div>
    <div>
      <input name="password" type="text" placeholder="Password" />
      <div v-if="authStore.errors?.password" class="error-msg">
        {{authStore.errors?.password[0]}}
      </div>
    </div>
    <input name="password_confirmation" type="text" placeholder="Password Confirmation" />
    <input type="submit" value="Register" />
  </form>
</template>

<style scoped>
</style>
