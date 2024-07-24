import { qs, qsa, addGlobalEventListener } from "./dom"
import { handleArrayReplace } from "./helpers"
import CustomSelect from "./../modules/customSelect"
import renderSearch from "./../../../blocks/advanced-search/advanced-search"

const parseParamsValue = (value) => {
  const isArray = value.includes(',')

  if (!isArray) return value

  return value.split(',').filter(i => i.length > 0 && i)
}

export function getURLparams() {
  const url = new URL(document.URL)
  const params = url.searchParams
  const entries = params.keys()

  let data = {}

  for (const entry of entries) {
    let value = params.get(entry)
    if (value) data[entry] = parseParamsValue(value)
  }

  return data
}

export function updateURLParams(data, reset = false) {
  if (reset) window.history.pushState('', '', window.location.pathname)

  let url = new URL(document.URL)
  let params = url.searchParams

  for (const [key, value] of Object.entries(data)) {
    if (value) params.set(key, value)
    if (!value) params.delete(key)
  }

  url.search = params.toString()
  window.history.pushState('', '', url)
}

export function updateParamsData(args) {
  const oldData = getURLparams()

  let { key, value, action = 'preserve' } = args
  let merged = {}

  const match = oldData[key] ?? false

  /* reset everything if needed */
  if (key === 'reset') {
    updateURLParams({}, true)
    return
  }

  if (!match) {
    merged[key] = value
    updateURLParams(merged)
    return
  }

  if (action === 'add') {
    merged[key] = handleArrayReplace(oldData[key], value)
  }

  if (action === 'replace') {
    merged[key] = oldData[key] !== value ? value : null
  }

  if (action === 'preserve') {
    merged[key] = value
  }

  updateURLParams(merged)
}

export function syncElementsWithParams() {
  const elements = qsa('[data-url-params]')

  if (elements.length === 0) return
  const data = getURLparams()

  elements.forEach(el => {
    const { tagName, type, dataset } = el

    const isInput = tagName === 'INPUT' || tagName === 'SELECT'
    const isOnOffBox = type === 'radio' || type === 'checkbox'

    const currentKey = dataset.paramsKey
    const currentValue = dataset.paramsValue || el.value

    let match = data[currentKey] ?? false
    const matchedValue = Array.isArray(match) ? match.some(i => i === currentValue) : match === currentValue
    

    if (!match || ( currentKey !== "search" && (match && data[currentKey] != dataset.paramsValue) )) {
      if (isInput && !isOnOffBox) el.value = ''
      if (isOnOffBox) el.checked = false
      return
    }

    if (isInput && !isOnOffBox) el.value = type === 'text' ? match : currentValue
    if (matchedValue && isOnOffBox) el.checked = true
  })

  CustomSelect.syncSelected()
}

export function getQueryString(containerId) {
  const containerData = qs(`#${containerId}`).dataset
  const urlData = getURLparams()

  let data = containerData
  data = { ...data, ...urlData }

  const result = new URLSearchParams(data)

  return result.toString()
}

export const handleFilter = ({target}) => {
  const { dataset } = target
  const value = dataset.paramsValue || target.value

  updateParamsData({
    value,
    key: dataset.paramsKey,
    action: dataset.paramsAction,
  })
  updateURLParams({ currentPage: null })
}

function addParamsHandlers(e) {
  e.preventDefault()

  const { target } = e
  const { dataset } = target

  const value = target.value || dataset.paramsValue
  const key = dataset.paramsKey || target.name

  /* remove currentPage key */
  if (key !== 'currentPage' && value) {
    const container = qs(`.advanced-search__results_list`)
    if (container) container.dataset.currentPage = 1
    updateURLParams({ currentPage: null })
  }

  updateParamsData({
    value,
    key: dataset.paramsKey,
    action: dataset.paramsAction,
  })

  syncElementsWithParams()

  if (!target.value) target.classList.add('active')

  if (e.key === 'Enter' || e.keyCode === 13)
  renderSearch(e)

  if(e.type === 'keyup' ) return;
  renderSearch(e)
}

export default function initURLParams() {
  syncElementsWithParams()
  addGlobalEventListener('click', '[data-url-params="click"]', addParamsHandlers)
  addGlobalEventListener('keyup', '[data-url-params="keyup"]', addParamsHandlers)
}
