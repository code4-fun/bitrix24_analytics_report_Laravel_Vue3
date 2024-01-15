import {ref} from 'vue'
import {defineStore} from 'pinia'
import {
  getReportDataApi,
  makeIntegrationApi,
  removeIntegrationApi
} from '@/api/bitrix24'
import type {
  ApiDataResponse,
  Bitrix24Store,
  ReportLine,
  IntegrationData,
  IntegrationResponse,
  ReportResponse
} from "@/types";
import {
  isIntegrationError,
  isReportError,
  isValidationError
} from "@/types";

export const useBitrix24Store = defineStore('bitrix24Store', () => {
  const state: Bitrix24Store = {
    reportData: ref([]),
    reportDataTotal: ref({}),
    reportMessage: ref(''),
    reportStatus: ref(0),
    integrationMessage: ref(''),
    integrationStatus: ref(0),
    loading: ref(false),
    removeIntegrationLoading: ref(false),
    errors: ref({})
  }

  const fetchReportData = async () => {
    try{
      state.loading.value = true
      const response: ApiDataResponse<ReportLine[]> = await getReportDataApi({
        uri: '/api/v1/report'
      })
      const indexOfTotal = response.data?.findIndex(i => i.name.startsWith('Итого'))
      if(indexOfTotal !== undefined && indexOfTotal !== -1){
        state.reportDataTotal.value = response.data?.splice(indexOfTotal, 1)?.[0] || {}
      }
      state.reportData.value = response.data
    } catch(e){
      if (isReportError(e)) {
        saveReportMessage({
          message: e.response.data.message,
          status: e.response.status
        })
      } else {
        console.error('An unexpected error occurred', (e as Error).message)
      }
    } finally {
      state.loading.value = false
    }
  }

  const makeIntegrationHandler = async (data: IntegrationData) => {
    try{
      state.loading.value = true
      const response: ApiDataResponse<IntegrationResponse> = await makeIntegrationApi({
        uri: '/api/v1/integrate',
        body: {...data}
      })
      saveIntegrationMessage(response.data)
    } catch(e){
      if (isValidationError(e)) {
        state.errors.value = e.response.data.errors
      } else if (isIntegrationError(e)) {
        saveIntegrationMessage({
          message: e.response.data.message,
          status: e.response.status
        })
      } else {
        console.error('An unexpected error occurred', (e as Error).message)
      }
    } finally {
      state.loading.value = false
    }
  }

  const removeIntegrationHandler = async (data: IntegrationData) => {
    try{
      state.removeIntegrationLoading.value = true
      const response: ApiDataResponse<IntegrationResponse> = await removeIntegrationApi({
        uri: '/api/v1/remove-integration',
        body: {...data}
      })
      saveIntegrationMessage(response.data)
      clearIntegrationErrors()
    } catch(e){
      if (isIntegrationError(e)) {
        saveIntegrationMessage({
          message: e.response.data.message,
          status: e.response.status
        })
      } else {
        console.error('An unexpected error occurred', (e as Error).message)
      }
    } finally {
      state.removeIntegrationLoading.value = false
    }
  }

  const saveIntegrationMessage = (data: IntegrationResponse) => {
    state.integrationMessage.value = data?.message
    state.integrationStatus.value = data?.status
    setTimeout(() => {
      state.integrationMessage.value = ''
      state.integrationStatus.value = 0
    }, 3000)
  }

  const saveReportMessage = (data: ReportResponse) => {
    state.reportMessage.value = data?.message
    state.reportStatus.value = data?.status
  }

  const clearIntegrationErrors = () => {
    state.errors.value.webhookName = ''
    state.errors.value.webhookUrl = ''
    state.errors.value.leadDailygrow = ''
    state.errors.value.dealDailygrow = ''
    state.errors.value.dealCost = ''
  }

  return {
    ...state,
    fetchReportData,
    makeIntegrationHandler,
    removeIntegrationHandler
  }
})
