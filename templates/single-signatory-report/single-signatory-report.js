import { addGlobalEventListener } from "./../../resources/js/utils/dom"
import {
    handleCommitment,
    syncReportElementsWithParams,
    handleChapter,
    renderPost,
    firstLoadPage,
    handleRemoveSelectedCommitment,
} from "./../../resources/js/modules/report"
import { handleScrollToLinkInTab, syncTabsWithSidebar } from "./../../resources/js/modules/renderReportNavigation.js"
import scrollSpy from "./../../resources/js/modules/scrollSpy"



window.addEventListener('DOMContentLoaded', () => {
    firstLoadPage();
    syncReportElementsWithParams();
    renderPost();

    addGlobalEventListener('click', '[data-url-params="chapter"]', handleChapter)
    addGlobalEventListener('click', '[data-url-params="commitment"]', handleCommitment)
    addGlobalEventListener('click', '.js-search-commitment .js-remove-selected-item', handleRemoveSelectedCommitment)
    
    addGlobalEventListener('click', '[data-params-key="commitment"]', () => {
        setTimeout(() => {
            renderPost('single-signatory')
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
