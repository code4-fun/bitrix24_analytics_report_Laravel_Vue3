import type {Ref} from "vue";
import type {AxiosError, AxiosResponse} from "axios";
import axios from "axios";

export interface RequestBody {
  [key: string]: string
}

export interface ApiRequest {
  uri: string
  body?: RequestBody
}

export interface IUser {
  id: number
  name: string
  role: number
}

export interface IntegrationResponse {
  status: number
  message: string
}

export interface ReportResponse {
  status: number
  message: string
}

export interface ApiDataResponse<T> {
  data: T
}

export interface ApiStatusResponse {
  status?: string
}

export interface AuthErrorData {
  email?: string[]
  password?: string[]
  name?: string[]
  token?: string[]
}

export interface IntegrationErrorData {
  webhookName?: string
  webhookUrl?: string
  leadDailygrow?: string
  dealDailygrow?: string
  dealCost?: string
}

export interface AuthStore {
  user: Ref<IUser | null>
  errors: Ref<AuthErrorData>
  status: Ref<string | null>
  loading: Ref<boolean>
}

export interface AuthData {
  email: string
  password: string
  name?: string
  password_confirmation?: string
  to?: string
}

export interface ResetPasswordData {
  password: string
  password_confirmation: string
  email: string
  token: string
}

export interface ReportLine {
  name: string
  orders: string
  conversion: string
  sales: string
  revenue: string
  average: string
  income: string
  roi: string
}

export interface Bitrix24Store {
  reportData: Ref<ReportLine[]>
  reportDataTotal: Ref<ReportLine | {}>
  reportMessage: Ref<string>
  reportStatus: Ref<number>
  integrationMessage: Ref<string>
  integrationStatus: Ref<number>
  loading: Ref<boolean>
  removeIntegrationLoading: Ref<boolean>
  errors: Ref<IntegrationErrorData>
}

interface ValidationResponse extends AxiosResponse {
  status: 422
  data: {
    message: string;
    errors: Record<string, string[]>
  };
}

export interface ValidationError extends AxiosError {
  response: ValidationResponse
}

export function isValidationError(error: unknown): error is ValidationError {
  return Boolean(
    axios.isAxiosError(error)
    && error.response
    && error.response.status === 422
    && typeof error.response.data?.message === 'string'
    && typeof error.response.data?.errors === 'object'
  )
}

export interface ApplicationAxiosResponse extends AxiosResponse {
  status: number
  data: {
    message: string;
    error: string;
  };
}

export interface ApplicationError extends AxiosError {
  response: ApplicationAxiosResponse
}

export function isReportError(error: unknown): error is ApplicationError {
  return Boolean(
    axios.isAxiosError(error)
    && error.response
    && error.response.status === 500
    && typeof error.response.data?.message === 'string'
    && error.response.data?.error === 'report_error'
  )
}

export function isIntegrationError(error: unknown): error is ApplicationError {
  return Boolean(
    axios.isAxiosError(error)
    && error.response
    && error.response.status === 500
    && typeof error.response.data?.message === 'string'
    && error.response.data?.error === 'integration_error'
  )
}

export interface IntegrationData {
  webhookName: string
  webhookUrl?: string
  leadDailygrow?: string
  dealDailygrow?: string
  dealCost?: string
}
