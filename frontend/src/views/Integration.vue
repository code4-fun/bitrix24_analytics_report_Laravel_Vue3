<script setup lang="ts">
import {useBitrix24Store} from '@/stores/bitrix24Store'
import {onMounted} from "vue";

const bitrix24Store = useBitrix24Store()

const submitHandler = (e: Event) => {
  bitrix24Store.loading = true
  const form = e.target as HTMLFormElement
  bitrix24Store.makeIntegrationHandler({
    webhookName: 'report_integration',
    webhookUrl: form.webhookUrl.value,
    leadDailygrow: form.leadDailygrow.value,
    dealDailygrow: form.dealDailygrow.value,
    dealCost: form.dealCost.value
  })
  form.webhookUrl.value = ''
  form.leadDailygrow.value = ''
  form.dealDailygrow.value = ''
  form.dealCost.value = ''
}

const removeIntegration = () => {
  bitrix24Store.removeIntegrationHandler({
    webhookName: 'report_integration'
  })
}

onMounted(() => {
  bitrix24Store.errors = {}
})
</script>

<template>
  <div class="loader" v-if="bitrix24Store.loading" />
  <form v-else class="form_container" @submit.prevent='submitHandler' autoComplete="off">
    <div class="title">
      Analytics report integration
      <div v-if="bitrix24Store.errors?.webhookName" class="error-msg">
        {{ bitrix24Store.errors?.webhookName?.[0] }}
      </div>
    </div>
    <div>
      <input name="webhookUrl" type="text" placeholder="Webhook URL" />
      <div v-if="bitrix24Store.errors?.webhookUrl" class="error-msg">
        {{ bitrix24Store.errors?.webhookUrl?.[0] }}
      </div>
    </div>
    <div>
      <input name="leadDailygrow" type="text" placeholder="Lead dailygrow field id" />
      <div v-if="bitrix24Store.errors?.leadDailygrow" class="error-msg">
        {{ bitrix24Store.errors?.leadDailygrow?.[0] }}
      </div>
    </div>
    <div>
      <input name="dealDailygrow" type="text" placeholder="Deal dailygrow field id" />
      <div v-if="bitrix24Store.errors?.dealDailygrow" class="error-msg">
        {{ bitrix24Store.errors?.dealDailygrow?.[0] }}
      </div>
    </div>
    <div>
      <input name="dealCost" type="text" placeholder="Deal Cost field id" />
      <div v-if="bitrix24Store.errors?.dealCost" class="error-msg">
        {{ bitrix24Store.errors?.dealCost?.[0] }}
      </div>
    </div>
    <input type="submit" value="Integrate" />
    <div class="form-bottom-links">
      <div v-if="!bitrix24Store.removeIntegrationLoading"
           class="remove-link"
           @click="removeIntegration">Remove integration</div>
      <div v-else class="loader-small" />
    </div>
  </form>
</template>

<style>
.title {
  font-size: 28px;
  text-align: center;
  line-height: 1.3;
}
.remove-link {
  height: 20px;
  color: hsl(235, 100%, 60%);
}
.remove-link:hover {
  text-decoration: underline;
  cursor: pointer;
}
</style>
