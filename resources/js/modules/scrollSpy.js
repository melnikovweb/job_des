import { qs, qsa, addGlobalEventListener, getCSSVariable } from "./../utils/dom";
import { getOffset } from "./../modules/common.js";

export default (function () {
  const getHeadings = () => {
    const commitment = qs('.js-chapter-tab.active .js-commitment-post')
    if (!commitment) return []

    return qsa('[id]', commitment).filter(i => {
        return !i.closest('[data-tab]:not(.active)')
    })
  }

  const scrollToCallback = (offset, callback) => {
    const fixedOffset = offset.toFixed()

    const onScroll = function () {
      if (window.pageYOffset.toFixed() === fixedOffset) {
        window.removeEventListener('scroll', onScroll)
        callback()
      }
    }

    window.addEventListener('scroll', onScroll)
    onScroll()
    window.scrollTo({
      top: offset,
      behavior: 'smooth',
    })
  }

  const scrollTo = (event) => {
    event.preventDefault()

    const id = event.target.getAttribute('href');
    const element = qs(id)
    const toc = qs('.js-sidebar-commitment-nav.shown');
    let menu_links = qsa('.js-sidebar-commitment-nav.shown .js-scroll-spy-link');

    if (!element) return;

    menu_links.forEach(link => link.classList.remove('active'))
    event.target.classList.add('active')
    toc.classList.add('disableScrollSpy')

    history.replaceState('', '', id);

    const headerHeight = getCSSVariable("--app-header-height");
    const coorsElement = getOffset(element);

    scrollToCallback(coorsElement.top - headerHeight + 5, () => toc.classList.remove('disableScrollSpy'))
  }

  const initScrollSpy = () => {
    const parent = qs('.js-scroll-spy-block')
    if (!parent) return

    const makeActive = (link) => qsa(`.js-scroll-spy-link[href="#${link}"]`).forEach((link) => link.classList.add('active'))
    const removeActive = (link) => qsa(`.js-scroll-spy-link[href="#${[link]}"]`).forEach(link => link.classList.remove('active'))
    const removeAllActive = (headings) => [...headings].forEach((heading) => removeActive(heading?.getAttribute('id')))

    const headingMargin = getCSSVariable("--app-header-height");

    let currentActive = '';

    window.addEventListener('scroll', () => {
        const headings = getHeadings()
      const toc = qs('.js-sidebar-commitment-nav.shown')
      if (!toc || toc.classList.contains('disableScrollSpy')) return

      const findIndex = [...headings].reverse().filter((heading) => {
        const coorsElement = getOffset(heading);
        return window.scrollY >= coorsElement.top - headingMargin
    })
    
      const current = findIndex[0]?.getAttribute('id');
      if (current !== currentActive) {
        removeAllActive(headings)
        currentActive = current
        makeActive(current)
      }
    })
  }


  function init() {
    // // init scroll spy
    initScrollSpy()
    // // scrollTo
    addGlobalEventListener('click', '.js-scroll-spy-link', scrollTo)

    window.addEventListener('resize', initScrollSpy)
  }

  return { init }
})();
