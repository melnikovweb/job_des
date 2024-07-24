import { addGlobalEventListener } from "./../../resources/js/utils/dom"
import { 
    handleCommitment,
    syncReportElementsWithParams,
    handleChapter,
    firstLoadPage,
    renderPost,
    handleRemoveSelectedCommitment,
 } from "./../../resources/js/modules/report"

import {
    getURLparams,
    syncElementsWithParams,
} from "./../../resources/js/utils/URLParams"
import {
    qs,
    // qsa,
    slideToggle,
} from "./../../resources/js/utils/dom"
import { getSiblings } from "./../../resources/js/utils/helpers"
import sendRequest from "./../../resources/js/utils/sendRequest"
import { handleScrollToLinkInTab, syncTabsWithSidebar } from "./../../resources/js/modules/renderReportNavigation.js"
import scrollSpy from "./../../resources/js/modules/scrollSpy"

let controller = null

async function renderChapterBlock() {
    try {

        if (controller) {
            controller.abort()
        }
        
        controller = new AbortController()
        const signal = controller.signal

        const chapterTab = qs('.js-chapter-tab.active');
        const container = qs('.js-chapter-block', chapterTab);
        const wrapChapterBlock = qs('.js-wrap-chapter-block', chapterTab);
        const parentContainer = qs('.js-wrap-data-content');

        // send request
        const containerData = container.dataset;
        let queryParams = getURLparams();
        const commitmentID = queryParams.commitment;

        // close/clear commitment answers
        const commitmentNav = qs(`.js-sidebar-commitment-nav [data-commitment="${commitmentID}"]`)
        if ( commitmentNav ) {
            const parent = commitmentNav.closest( '.js-sidebar-commitment-nav');
            const otherCommitmentNavLinks = getSiblings(parent);
            otherCommitmentNavLinks.forEach(elem => {
                const answer = qs( '.js-sidebar-commitment-answer', elem );
                const answerContent = qs( '.js-sidebar-commitment-content', elem );
                elem.classList.remove('shown', 'loaded');
                answer.classList.remove('shown');
                answerContent.innerHTML = '';
                slideToggle(answer);
            })
        }

        if ( !commitmentID || containerData.showCommitment == commitmentID ) return
        
        queryParams = {...queryParams, ...containerData};
        
        const result = new URLSearchParams(queryParams)
        result.toString()
        
        const query = `${container.dataset.endpoint}?${result}`

        // add loading
        parentContainer?.classList.add('loading')

        const response = await sendRequest({method: 'GET', endpoint: query, signal})

        if ( response ) {
            const results = await response?.data

            // remove loading
            parentContainer?.classList.remove('loading')

            wrapChapterBlock.classList.add('loaded');

            container.innerHTML = '';

            if ( results?.postsData ) {
                container.dataset.showCommitment = commitmentID;
                container.insertAdjacentHTML( "beforeend", results.postsData )

                syncElementsWithParams()

                const signatoryID = queryParams.signatory;

                if (!signatoryID) return;

                renderPost()
            }
        }
        
    } catch(err) {
        console.error(err)
    }
}

window.addEventListener('DOMContentLoaded', () => {
    firstLoadPage();
    syncReportElementsWithParams();
    renderChapterBlock();

    addGlobalEventListener('click', '[data-url-params="chapter"]', handleChapter)
    addGlobalEventListener('click', '[data-url-params="commitment"]', handleCommitment)
    addGlobalEventListener('click', '.js-search-commitment .js-remove-selected-item', handleRemoveSelectedCommitment)

    addGlobalEventListener('click', '[data-params-key="commitment"]', (e) => {
        setTimeout(() => {
            renderChapterBlock(e)
        }, 0)
    });
    
    addGlobalEventListener('click', '[data-params-key="signatory"]', () => {
        setTimeout(() => {
            renderPost()
        }, 0)
    })

    addGlobalEventListener('click', '.js-scroll-to-active-tab', handleScrollToLinkInTab)
    addGlobalEventListener('click', '.js-second-level-title', () => {
        setTimeout(() => {
            syncTabsWithSidebar()
            scrollSpy.init();
        }, 0)
    })
});

