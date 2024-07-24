import { handleFilter, getURLparams } from "../../resources/js/utils/URLParams"
import { qs, qsa, addGlobalEventListener } from "./../../resources/js/utils/dom"
import renderPagination from "./../../resources/js/utils/renderPagination"
import { delay } from './../../resources/js/utils/delays'

let controller = null

window.addEventListener('DOMContentLoaded', () => {
    addGlobalEventListener('change', '[data-url-params="filter"]', handleFilter)
    addGlobalEventListener('submit', '.advanced-search__form', renderSearch)
    addGlobalEventListener('click', '[data-params-key="mobile-filter"]', mobileFilter)

  /* first load render */
  renderSearch()
  loadMore()
});

function mobileFilter() {
  const container = qs('.advanced-search__filter_bar')
  container.classList.toggle('opened')
}

  /* loadmore */
  function loadMore() {
    const btnMore = document.querySelectorAll('[data-action="loadmore"]')

    if (btnMore.length > 0) {
      btnMore.forEach(btn => {
        btn.addEventListener('click', function() {
          let offset = +btn.dataset.offset + +btn.dataset.count
          let parent = btn.parentElement
          let li = parent.querySelectorAll('.sk-form-select-option')

          for(let i = 0; i < offset; i++) {
            li[i] && li[i].classList.remove('hidden')
          }

          btn.dataset.offset = offset

          li.length < offset && btn.remove()
        })
      })
    }
  }

export default async function renderSearch(e) {
  try {
    e && e.preventDefault()

    const section = qs('.advanced-search')
    const container = qs('.advanced-search__results_list')
    const template = qs('.advanced-search__template')

    if(!section) return

    if (controller) {
      controller.abort()
    }
  
    controller = new AbortController()
    const signal = controller.signal
  
    section.classList.add('loading')
    container.querySelectorAll('*').forEach( n => n.remove() )
  
    const formData = new FormData()
    const args = getURLparams()

    Object.entries(args).map(arg => {
      let key = arg[0]
      let value = arg[1]

      formData.append(key, value)
    })
    formData.append('action', 'advanced_search')

    fetch(themeVars.ajax, {
      method: 'POST',
      body: formData,
      headers: {
          Accept: 'application/json', 
      },
      signal: signal,
    })
    .then(response => {
      if (response.ok) {
        return response.json()
      } else {
        throw new Error('Failed to fetch data')
      }
    })
    .then(data => {
      delay(() => {
        if( data.reports.length !== 0 ) {
          const list = Object.values(data?.reports).map(post => handleTemplate(post, template))

          list?.forEach(item => container.appendChild(item))
  
          const currentPage = data?.currentPage
          const totalPages = data?.totalPages
      
          if (data?.postsPerPage) container.dataset.postsPerPage = data.postsPerPage
          if (currentPage) container.dataset.currentPage = data.currentPage
          if (totalPages) container.dataset.totalPages = data.totalPages

          qs('.advanced-search__results_count').innerText = data.totalPosts + ' Results'
          qs('.search-noresults').classList.add('hidden')
        } else {
          qs('.advanced-search__results_count').innerText = ''
          qs('.search-noresults').classList.remove('hidden')
        }

        section.classList.remove('loading')
        /* render pagination here */
        renderPagination(container, +data.currentPage, +data.totalPages)
      }, 500)
    })
    .catch(error => {
      console.error('Error: ', error)
    })
  } catch(err) {
    console.error(err)
  }
}

function handleTemplate(data, template) {
  let container = template.content

  for (const [key, value] of Object.entries(data)) {
    const clonItems = qsa(`[data-post-field="${key}"]`, container)

    if (!clonItems) continue

    clonItems?.forEach(item => {
      if (key === 'post_title') {
        item.dataset.sortValue = value
      }

      if (key === 'post_link') {
        item.href = value
        return
      }

      item.innerHTML = value
    })
  }

  return container.cloneNode(true)
}
