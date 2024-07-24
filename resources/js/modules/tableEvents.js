import { addGlobalEventListener, qs, qsa } from "../utils/dom"

function handleTableSort({ target }) {
  function getSortValue(sortBy, parent) {
    let el = qs(`[data-sort-key="${sortBy}"]`, parent)
    return el.dataset.sortValue.toUpperCase()
  }

  function sortText(x, y, sortBy, order) {

    if ( x.classList.contains('not-sort') ) return 1

    let a = getSortValue(sortBy, x)
    let b = getSortValue(sortBy, y)

    const sorting = order === 'desc' ? a < b : a > b

    return a == b ? 0 : sorting ? 1 : -1
  }

  function sortNumbers(x, y, sortBy, order) {

    if ( x.classList.contains('not-sort') ) return 1

    let a = parseFloat(getSortValue(sortBy, x))
    let b = parseFloat(getSortValue(sortBy, y))

    if (isNaN(a)) {
      return 1
    }

    const sorting = order === 'desc' ? a < b : a > b

    return sorting ? 0 : -1
  }

  const container = target.closest('table')
  if (!container) return

  const table = qs('[data-sort-table]', container)
  const sortActive = qs('.sort-active', container)
  const rows = qsa('tr', table)

  if(sortActive) sortActive.classList.remove('sort-active')
  target.classList.toggle('desc')
  target.classList.add('sort-active')
  const order = target.matches('.desc') ? 'desc' : 'asc'
  const { sortBy, sortType } = target.dataset

  sortType === 'number'
    ? rows.sort((x, y) => sortNumbers(x, y, sortBy, order))
    : rows.sort((x, y) => sortText(x, y, sortBy, order))

  table.innerHTML = ''
  rows.forEach(row => table.appendChild(row))
}

export default function initTableSort() {
  const tables = qsa('[data-sort-table]')
  if (!tables) return

  addGlobalEventListener('click', '[data-sort-by]', handleTableSort)
}