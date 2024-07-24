import { qs } from "./dom"

export default async (args) => {
  const { method, data, endpoint, signal } = args

  let root = qs('#custom-admin-root')?.value || themeVars?.root
  let new_nonce = qs('#new_wp_nonce')?.value || qs('#custom-admin-nonce')?.value

  try {
    const reqBody = {
      method,
      credentials: 'same-origin',
      headers: {
        "X-WP-Nonce": new_nonce || themeVars?.nonce,
      },
      body: data,
      signal: signal,
    }

    const res = await fetch(`${root}api/${endpoint}`, reqBody)

    return res.json()
  } catch(err) {
    console.log('Request failed', err.message)
  }
}