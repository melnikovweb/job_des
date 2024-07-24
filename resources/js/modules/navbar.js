import { delay } from "./../utils/delays"

export const navbarToggler = () => {
  const body = document.querySelector('body')
  const btnMenu = document.querySelector('.menu-open')
  const btnClose = document.querySelector('.menu-close')  
  const siteNav = document.querySelector('.header__inner_center')
  let lastScrollTop = 0
  let isOpen = false

  async function hideMenu() {
    body.classList.remove('fixed')
    btnMenu.parentElement.classList.remove('menu-opened')
    siteNav.classList.remove('opened')
    await delay(() => siteNav.classList.remove('fixed'), 100)
    lastScrollTop = body.style.top.replace('px', '').replace('-', '')
    body.style.removeProperty('margin-left')
    body.style.removeProperty('overflow')
    body.style.removeProperty('position')
    body.style.removeProperty('top')
    body.style.removeProperty('width')
    window.scrollTo(0, lastScrollTop)

    isOpen = false
  }

  async function showMenu() {
    body.classList.add('fixed')
    btnMenu.parentElement.classList.add('menu-opened')
    await delay(() => siteNav.classList.add('fixed'), 100)
    siteNav.classList.add('opened')
    lastScrollTop = window.pageYOffset
    body.style.top = `-${lastScrollTop}px`
    isOpen = true
  }

  function handleMenuButtonClick() {
    siteNav.classList.remove('opened')

    if (isOpen) {
      hideMenu()
    } else {
      showMenu()
    }
  }

  return {
    handleEvent(event) {
      if (event.type === 'click') {
        handleMenuButtonClick()
      }
    },

    init() {
      btnMenu.addEventListener('click', this)
      btnClose.addEventListener('click', this)
    },
  }
}
