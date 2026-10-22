const ADMIN_TOKEN_KEY = 'pollofeliz_admin_token'

export const getAdminToken = () => (
  localStorage.getItem(ADMIN_TOKEN_KEY) || sessionStorage.getItem(ADMIN_TOKEN_KEY)
)

export const setAdminToken = (token, persistent = true) => {
  if (persistent) {
    localStorage.setItem(ADMIN_TOKEN_KEY, token)
    sessionStorage.removeItem(ADMIN_TOKEN_KEY)
    return
  }

  sessionStorage.setItem(ADMIN_TOKEN_KEY, token)
  localStorage.removeItem(ADMIN_TOKEN_KEY)
}

export const clearAdminToken = () => {
  localStorage.removeItem(ADMIN_TOKEN_KEY)
  sessionStorage.removeItem(ADMIN_TOKEN_KEY)
}

export const hasAdminToken = () => Boolean(getAdminToken())
