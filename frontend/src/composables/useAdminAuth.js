const ADMIN_TOKEN_KEY = 'pollofeliz_admin_token'

export const getAdminToken = () => localStorage.getItem(ADMIN_TOKEN_KEY)

export const setAdminToken = (token) => {
  localStorage.setItem(ADMIN_TOKEN_KEY, token)
}

export const clearAdminToken = () => {
  localStorage.removeItem(ADMIN_TOKEN_KEY)
}

export const hasAdminToken = () => Boolean(getAdminToken())
