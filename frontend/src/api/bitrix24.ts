import {makeRequest} from "./index"
import type {ApiDataResponse, ApiRequest, ReportLine} from "@/types";

export const getReportDataApi = (request: ApiRequest): Promise<ApiDataResponse<ReportLine[]>> => {
  return makeRequest<ApiDataResponse<ReportLine[]>>(request.uri)
}

export const makeIntegrationApi = (request: ApiRequest): Promise<any> => {
  return makeRequest(request.uri, {
    method: 'POST',
    data: request.body
  })
}
