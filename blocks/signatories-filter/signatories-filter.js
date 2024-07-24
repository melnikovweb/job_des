import { getURLparams } from "../../resources/js/utils/URLParams"
import { qs, addGlobalEventListener } from "../../resources/js/utils/dom"
import sendRequest from "../../resources/js/utils/sendRequest";

let controller = null

export default async function renderSignatories() {
    try {

        if (controller) {
            controller.abort()
        }
        
        controller = new AbortController()
        const signal = controller.signal

        const container = qs('.js-signatories-filter-results');
        const parentContainer = container?.closest('.js-signatories-filter-wrap-results');

        const containerData = container.dataset
        let queryParams = getURLparams();
        queryParams = {...queryParams, ...containerData};
        
        const result = new URLSearchParams(queryParams)
        result.toString()
        
        const query = `${container.dataset.endpoint}?${result}`

        parentContainer?.classList.add('loading')

        const response = await sendRequest({method: 'GET', endpoint: query, signal})
        
        if ( response ) {
            const results = await response?.data

            parentContainer?.classList.remove('loading')
    
            container.innerHTML = '';
    
            if ( results?.postsData ) container.insertAdjacentHTML( "beforeend", results.postsData )
        }
        
    } catch(err) {
        console.error(err)
    }
}

window.addEventListener('DOMContentLoaded', () => {
    addGlobalEventListener('keyup', '#search-signatories', () => {
        setTimeout(() => {renderSignatories()}, 0)
    })
});



