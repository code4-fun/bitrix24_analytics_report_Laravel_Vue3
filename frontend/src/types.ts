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

export interface ApiDataResponse<T> {
  data?: T
}

export interface ApiStatusResponse {
  status?: string
}

export interface ErrorData {
  email?: string[]
  password?: string[]
  name?: string[]
  token?: string[]
}

export interface AuthStore {
  user: Ref<IUser | null>
  errors: Ref<ErrorData>
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
  loading: Ref<boolean>
  errors: Ref<ErrorData>
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

export interface IntegrationData {
  webhookUrl: string
  leadDailygrow: string
  dealDailygrow: string
  dealCost: string
}
