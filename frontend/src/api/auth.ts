import {makeRequest} from "./index"

export const getTokenApi = uri => {
  return makeRequest(uri)
}

export const getUserApi = uri => {
  return makeRequest(uri)
}

export const loginApi = (uri, body)=> {
  return makeRequest(uri, {
    method: 'POST',
    data: body
  })
}

export const registerApi = (uri, body)=> {
  return makeRequest(uri, {
    method: 'POST',
    data: body
  })
}

export const logoutApi = uri => {
  return makeRequest(uri, {
    method: 'POST'
  })
}
