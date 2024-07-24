export function searchSimpleForm() {
  const btnSearch = [...document.querySelectorAll('.header-search')]
  btnSearch.forEach(btn => {
    btn.addEventListener('click', function() {
      const searchForm = document.querySelector('.search-simple')

      if ( searchForm.classList.contains('opened') ) {
        searchForm.classList.remove('opened')
      } else {
        searchForm.classList.add('opened')
      }
    })
  })
}
