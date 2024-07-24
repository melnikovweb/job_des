import { qs, addGlobalEventListener } from "./../../resources/js/utils/dom"
import renderPagination from "./../../resources/js/utils/renderPagination"

window.addEventListener('DOMContentLoaded', () => {
    const container = qs('.search__results')

    renderPagination(container)
    setTimeout(() => {
        addGlobalEventListener('click', '[data-url-params="click"]', function() {
            location.reload()
        })
    }, 500)
});
