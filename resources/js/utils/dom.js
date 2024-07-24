import { delay } from "./delays"

export function addGlobalEventListener(type, selector, callback, options, parent = document) {
  parent.addEventListener(type, e => { if (e.target.matches(selector)) callback(e) }, options)
}

export const qs = (selector, parent = document) => parent?.querySelector(selector)

export const qsa = (selector, parent = document) => [...parent?.querySelectorAll(selector)]

export function createElement(type, options = {}) {
  const element = document.createElement(type)
  Object.entries(options).forEach(([key, value]) => {
    if (key === "class") {
      element.classList.add(value)
      return
    }

    if (key === "dataset") {
      Object.entries(value).forEach(([dataKey, dataValue]) => {
        element.dataset[dataKey] = dataValue
      })
      return
    }

    if (key === "text") {
      element.textContent = value
      return
    }

    element.setAttribute(key, value)
  })
  return element
}

export async function toggleElement(el, show = true) {
  if (show) {
    el.classList.remove('hidden')
    await delay(() => el.classList.add('shown'), 100)
    return
  }

  el.classList.remove('shown')
  await delay(() => el.classList.add('hidden'), 300)
}

export async function slideToggle(el) {
  if (!el.matches('.shown')) {
    el.style.height = ''
    return
  }

  let elHeight = 0
  Array.prototype.forEach.call(el.children, child => elHeight += child.clientHeight)
  el.style.height = `${elHeight}px`
}

export function getCSSVariable(variable, parent = document.documentElement) {
  return getComputedStyle(parent)?.getPropertyValue(variable).replace('px', '')
}
