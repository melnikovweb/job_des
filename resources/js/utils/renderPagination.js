import { qs } from "./dom"

function getPaginationLinks(current, total) {
  const MAX = 5
  const DOTS = '...'
  const ONLY_RIGHT_DOTS = current <= 2
  const ONLY_LEFT_DOTS = current >= total - 1
  const LEFT_AND_RIGHT_DOTS = current > 2 && current < total - 1

  let range = []
  let rightDotsPages = []
  let leftDotsPages = []

  if(total <= MAX) {
    /* return simple list of pages without dots */
    for (let i = 1; i <= total; i++) range.push(i)

    return {
      pages: range,
      leftPages: leftDotsPages,
      rightPages: rightDotsPages,
    }
  }

  let isDots = false
  let isInRange = false
  let isInDotRange = false

  for (let i = 1; i <= total; i++) {
    if(ONLY_RIGHT_DOTS) {
      isDots = i === 4
      isInRange = i <= 3 || i === total
      isInDotRange = !isInRange && (rightDotsPages.length + 1) <= MAX

      if(isInDotRange) rightDotsPages.push(i)
    }

    if(ONLY_LEFT_DOTS) {
      isDots = i === 2
      isInRange = i >= total - 2 || i === 1
      isInDotRange = !isInDotRange && (leftDotsPages.length + 1) <= MAX

      if(isInDotRange) leftDotsPages.push(i)
    }

    if(LEFT_AND_RIGHT_DOTS) {
      isDots = i === 3 || i === total - 1
      isInRange = i === 1 || i === current || i === total

      /* determine/push the left range */
      if(i > 1 && i < current && (leftDotsPages.length + 1) <= MAX) leftDotsPages.push(i)
      /* determine/push the right range */
      if(i > current && i < total && (rightDotsPages.length + 1) <= MAX) rightDotsPages.push(i)
    }

    /* these are always pushed */
    if(isDots) range.push(DOTS)
    if(isInRange) range.push(i)
  }

  return {
    pages: range,
    leftPages: leftDotsPages,
    rightPages: rightDotsPages,
  }
}

function getPageDropdownHTML(pages = [], containerId) {

  let result = ''

  if(pages.length > 0) {
    result = `
    <div class="pagination-options-wrapper">
      <div class="pagination-options">
        ${pages.map(page => `
          <button class="btn-tab"
            data-url-params="click"
            data-params-key="currentPage"
            data-params-value="${page}"
            data-container-id="${containerId}"
          >${page}</button>
        `).join('')}
      </div>
    </div>`
  }

  return result
}

export default function (container, curr = null, total = null) {
  const currentPage = curr ?? parseInt(container.dataset.currentPage)
  const totalPages = total ?? parseInt(container.dataset.totalPages)
  const paginationContainer = qs(`.advanced-search__pagination`)
  paginationContainer.innerHTML = ''

  if(totalPages < 2) return

  const prev = (currentPage - 1) > 0 ? currentPage - 1 : false
  const next = (currentPage + 1) <= totalPages ? currentPage + 1 : false

  const { pages, leftPages, rightPages } = getPaginationLinks(currentPage, totalPages)

  const leftPagesHTML = getPageDropdownHTML(leftPages, container.id)
  const rightPagesHTML = getPageDropdownHTML(rightPages, container.id)

  paginationContainer.innerHTML = `
    <button class="prev btn-tab"
      ${prev ? 'data-url-params="click"' : '' }
      data-params-key="currentPage"
      data-params-value="${prev}"
      data-container-id="${container.id}"
      ${prev ? '' : 'disabled' }
      aria-label="Prev"
    >
      <svg width="24" height="24"><use href="#icon-arrow-right"></use></svg>
    </button>

    <div class="pagination__pages">
      ${pages.map((item, index) => {
        let tag = 'button'
        let btnAttrs = ''
        let dropdown = ''
        let btnClass = item === currentPage ? 'btn-tab active' : 'btn-tab'

        if(item !== '...') {
          tag = 'button'
          dropdown = ''

          btnAttrs = `
            data-url-params="click"
            data-params-key="currentPage"
            data-params-value="${item}"
            data-container-id="${container.id}"
          `
        } else {
          tag = 'div'
          btnClass += ' btn pagination-dropdown'
          dropdown = index === 1 ? leftPagesHTML : rightPagesHTML
        }

        return `<${tag} class="${btnClass}" ${btnAttrs}>${item}${dropdown}</${tag}>`
      }).join('')}
    </div>

    <button class="next btn-tab"
      ${next ? 'data-url-params="click"' : '' }
      data-params-key="currentPage"
      data-params-value="${next}"
      data-container-id="${container.id}"
      ${next ? '' : 'disabled' }
      aria-label="Next"
    >
      <svg width="24" height="24"><use href="#icon-arrow-right"></use></svg>
    </button>
  `
}