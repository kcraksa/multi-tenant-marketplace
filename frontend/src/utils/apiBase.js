export const getApiBaseUrl = () => {
  if (import.meta.env.VITE_API_URL) {
    return import.meta.env.VITE_API_URL
  }

  const protocol = window.location.protocol
  const hostname = window.location.hostname
  const port = import.meta.env.VITE_API_PORT || '8000'

  return `${protocol}//${hostname}:${port}/api`
}

export const getStorageBaseUrl = () => {
  const apiUrl = getApiBaseUrl()
  return apiUrl.replace(/\/api\/?$/, '')
}

export const buildStorageUrl = (path) => {
  if (!path) {
    return ''
  }

  if (path.startsWith('http')) {
    return path
  }

  const base = getStorageBaseUrl()
  return `${base}/storage/${path}`
}

export const getTenantHeaderValue = () => {
  if (import.meta.env.VITE_TENANT_IDENTIFIER) {
    return import.meta.env.VITE_TENANT_IDENTIFIER
  }

  if (typeof window === 'undefined') {
    return null
  }

  const hostname = window.location.hostname

  if (!hostname) {
    return null
  }

  const segments = hostname.split('.').filter(Boolean)

  if (segments.length > 1) {
    const subdomain = segments[0]

    if (subdomain && subdomain !== 'www' && subdomain !== 'localhost') {
      return subdomain
    }
  }

  return null
}
