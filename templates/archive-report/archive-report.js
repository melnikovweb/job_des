import { handleFilter, getURLparams, updateURLParams } from '../../resources/js/utils/URLParams'
import { getSiblings } from '../../resources/js/utils/helpers'
import { qs } from './../../resources/js/utils/dom'
import { delay } from './../../resources/js/utils/delays'
import template from './template'

document.addEventListener('DOMContentLoaded', function() {

  const filters = document.querySelectorAll('[data-url-params="filter"]')
  const sorts = document.querySelectorAll('[data-action="sort"]')
  const container = document.querySelector('.report-table__body_list')
  const input = document.querySelector('#report_archive_search')

  let controller = null

  /* first load render */
  container && handleReportsList()

  /* filters render */
  if (filters.length > 0) {
    filters.forEach(item => {
      item.addEventListener('click', function(e) {
        item.classList.add('active')
        getSiblings(e.target).forEach(sibling => {
            sibling.classList.remove('active')
        })

        item.classList.add('active')
        handleFilter(e)
        searchCallback(e)
      })
    })
  }

  /* sort */
  if (sorts.length > 0) {
    sorts.forEach(item => {
      item.addEventListener('click', function() {
        item.classList.toggle('active')
        getSiblings(item).forEach(sibling => {
          sibling.classList.remove('active')
        })
        sortTable()
      })
    })
  }

  /* search */
  input && input.addEventListener('keyup', inputSearch)

  /* loadmore */
  function loadMore() {
    const btnMore = document.querySelectorAll('[data-action="loadmore"]')

    if (btnMore.length > 0) {
      btnMore.forEach(btn => {
        btn.addEventListener('click', function() {
          let offset = +btn.dataset.offset + 6
          let parent = btn.parentElement.parentElement
          let li = parent.querySelectorAll('.report-table__body_items-signatory')

          for(let i = 0; i < offset; i++) {
            li[i] && li[i].classList.remove('hidden')
          }

          btn.dataset.offset = offset

          li.length < offset && btn.parentElement.remove()
        })
      })
    }
  }

  function inputSearch() {
    let filter = input.value.toUpperCase()
    let row = document.querySelectorAll('.report-table__body_row')

    for (let i = 0; i < row.length; i++) {
      let li = row[i].querySelectorAll('.report-table__body_items-signatory')

      // Hide signatory item
      for (let y = 0; y < li.length; y++) {
        let span = li[y].getElementsByTagName('span')[0]
        let txtValue

        if (span) {
          txtValue = span.textContent || span.innerText
          if (txtValue.toUpperCase().indexOf(filter) > -1) {
            li[y].style.display = ''
            li[y].classList.remove('hidden')
          } else {
            li[y].style.display = 'none'
            li[y].classList.add('hidden')
          }
        }
      }

      // Hide row item in case all signatory is hidden
      let liHidden = row[i].querySelectorAll('.report-table__body_items-signatory.hidden')
      if( li.length === liHidden.length ) {
        row[i].style.display = 'none'
        row[i].classList.add('hidden')
      } else {
        row[i].style.display = ''
        row[i].classList.remove('hidden')
      }

      // Hide/show no results
      let liAll = row[i].querySelectorAll('.report-table__body_items-signatory')
      if( liAll.length === liHidden.length ) {
        qs('.search-noresults').classList.remove('hidden')
      } else {
        qs('.search-noresults').classList.add('hidden')
      }
    }

    // show no results
    row.length === 0 && qs('.search-noresults').classList.remove('hidden')
  }

  function sortTable() {
    let rows = document.querySelectorAll('.report-table__body_row')
    
    for (let i = 0; i < (rows.length - 1); i++) {
      /* change order */
      rows[i].parentNode.insertBefore(rows[i + 1], rows[i])
    }
  }

  function handleReportsList() {
    const current = document.querySelector('.report-filter__item.active')

    updateURLParams({
      years: current.dataset.paramsValue,
    })

    searchCallback()
  }

  function searchCallback() {
    if (controller) {
      controller.abort()
    }

    controller = new AbortController()
    const signal = controller.signal

    container.parentElement.classList.add('loading')
    container.querySelectorAll('*').forEach( n => n.remove() )

    const formData = new FormData()
    const { years } = getURLparams()

    formData.append('action', 'archive_reports')
    years && formData.append('years', years)
    
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
        if( data.length === 0 ) {
          qs('.search-noresults').classList.add('hidden')
  
        } else {
          const list = Object.values(data).map((key) => key.reports && template(key)).join('')
  
          container.insertAdjacentHTML('beforeend', list)
          container.parentElement.classList.remove('loading')
          qs('.search-noresults').classList.add('hidden')
          loadMore()
        }

        container.parentElement.classList.remove('loading')
        inputSearch()
      }, 500)
      
    })
    .catch(error => {
      console.error('Error: ', error)
    })
  }
})
