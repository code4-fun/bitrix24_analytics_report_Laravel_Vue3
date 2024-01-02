import {ref} from 'vue'
import {defineStore} from 'pinia'
import {getReportDataApi, makeIntegrationApi} from '@/api/bitrix24'
import type {ApiDataResponse, Bitrix24Store, ReportLine, IntegrationData} from "@/types";

export const useBitrix24Store = defineStore('bitrix24Store', () => {
  const state: Bitrix24Store = {
    reportData: ref([]),
    reportDataTotal: ref({}),
    loading: ref(false),
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
      state.reportData.value = response.data || []
    } catch(e){
      console.log(e)
    } finally {
      state.loading.value = false
    }
  }

  const makeIntegrationHandler = async (data: IntegrationData) => {
    await makeIntegrationApi({
      uri: '/api/v1/integrate',
      body: {...data}
    })
  }

  return {
    ...state,
    fetchReportData,
    makeIntegrationHandler
  }
})
