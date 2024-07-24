import { qs, qsa, getCSSVariable } from "../utils/dom"

export default function initTooltips() {
  function closeOtherTooltips(current = null) {
    const tooltips = qsa('.js-tooltip-mouseover.openned')

    tooltips?.forEach(i => i !== current && i.classList.remove('openned'))
  }

  function updateTooltipPos(current = null) {
    const target = current
    if (!target.matches('.js-tooltip-mouseover')) return

    const infobox = qs('.js-tooltip-info', target)

    const { top, left } = target.getBoundingClientRect()
    const windowWidth = window.innerWidth
    let topPos = top - (infobox.clientHeight + 10)
    let shiftLeft = 0

    if (window.matchMedia(`(min-width: ${getCSSVariable('--breakpoint-sm')}px)`).matches) {
        shiftLeft = 12
    } else {
        shiftLeft = 8
    }

    let leftPos = left - shiftLeft

    if ((leftPos + infobox.clientWidth) > windowWidth) {
        target.classList.add('right-arrow');
        leftPos = left - infobox.clientWidth + target.clientWidth + shiftLeft
    }

    infobox.style.cssText = `
      top: ${topPos}px;
      left: ${leftPos}px;
    `
  }

  function handleTooltips({ target }) {
    const isInfo = target.matches('.js-tooltip-info')

    if (isInfo) return

    if (!target.matches('.js-tooltip-mouseover')) {
      closeOtherTooltips(null)
      return
    }

    updateTooltipPos(target)
    target.classList.toggle('openned')
    closeOtherTooltips(target)
  }

  document.addEventListener('mouseover', e => handleTooltips(e))
  window.addEventListener('scroll', () => closeOtherTooltips())
}
