import { qsa, qs, slideToggle } from "../utils/dom"
import { getSiblings } from "../utils/helpers"
import scrollSpy from "../modules/scrollSpy"

// render services sub list
function renderServicesSubList(sublist) {
    let content = '<ul class="page-sidebar__list-sublist">';

    sublist?.forEach(item => {
        content += `<li><a href="#${item.id}" class="js-scroll-to-active-tab js-scroll-spy-link">${item.title}</a></li>`;
    });

    content += '</ul>';

    return content;
}

// render services list (accordion)
function renderServicesList(list) {
    let content = '<ul class="page-sidebar__list-sublist">';
    list?.forEach(item => {
        if (item.sublist.length) {
            const sublist = renderServicesSubList(item.sublist);
            content += `<li data-acc-item data-scroll-tab-id="${item.tabID}" data-scroll-parent-id="${item.parentID}">
                            <div data-acc-question>${item.title}<svg><use href="#icon-chevron-down"></use></svg></div>
                            <div data-acc-answer>
                                <div>${sublist}</div>
                            </div>
                        </li>`;
        }
    });

    content += '</ul>';

    return content;
}

// collect and render services navigation
function renderServicesNav(arrServices) {
    const secondLevelData = [...arrServices].map(title => ({
        'title': title?.dataset?.tabTitle,
        'tabID': title?.dataset?.tabNav,
        'parentID': title?.dataset?.tabParent,
    }));
    const subList = secondLevelData.map(block => {
        const curentTabContent = qs(`[data-tab="${block.tabID}"][data-tab-parent="${block.parentID}"]`);
        let thirdLevelTitles = qsa( '.js-third-level-title', curentTabContent );

        thirdLevelTitles = [...thirdLevelTitles].map(title => ({
            'id': title?.getAttribute('id'),
            'title': title?.dataset?.title,
        }));

        return {
            ...block,
            ['sublist']: thirdLevelTitles,
        };
    });

    return renderServicesList(subList);
}

export function generateNavigation(commitmentID) {
    const chapter = qs( `.js-chapter-link.active` );
    const parent = chapter.closest('li');
    const commitmentNav = qs( `.js-sidebar-commitment-nav [data-commitment='${commitmentID}']`, parent );
    const parentCommitmentNav = commitmentNav.closest( '.js-sidebar-commitment-nav' );
    const commitmentAnswer = qs('.js-sidebar-commitment-answer', parentCommitmentNav);
    const commitmentContent = qs( '.js-sidebar-commitment-content', parentCommitmentNav );

    const reportBlocks = qsa( '.js-chapter-tab.active .js-sub-report-block' );

    if ( !reportBlocks.length ) return;

    let content = '';

    content = '<ul class="page-sidebar__list-sublist page-sidebar__list-sublist-services">';

    reportBlocks.forEach( block => {
        const firstLevelTitle = qs( '.js-first-level-title', block );
        const title = firstLevelTitle?.dataset?.title;
        const id = firstLevelTitle?.getAttribute('id');
        const secondLevelTitles = qsa( '.js-second-level-title', block );
        let subNavigation = '';

        if (secondLevelTitles.length) {
            subNavigation = renderServicesNav(secondLevelTitles);
        } else {
            let thirdLevelTitles = qsa( '.js-third-level-title', block );
            thirdLevelTitles = thirdLevelTitles.map(title => ({
                'id': title.getAttribute('id'),
                'title': title.dataset.title,
            }))

            subNavigation = renderServicesSubList(thirdLevelTitles);
        }

        content += `<li>
                        <a href="#${id}" class="js-scroll-spy-link">${title}</a>
                        ${subNavigation}
                    </li>`;
    } )
    content += '</ul>';

    // open nav after add sub navigation
    const otherAccordions = getSiblings(parentCommitmentNav);
    otherAccordions.forEach(acc => {
        const answer = qs('[data-acc-answer]', acc);
        const answerContent = qs('.js-sidebar-commitment-content', answer);
        acc.classList.remove('shown');
        acc.classList.remove('loaded');
        answer.classList.remove('shown');
        slideToggle(qs('[data-acc-answer]', acc))
        answerContent.innerHTML = '';
    });

    parentCommitmentNav.classList.add('loaded', 'shown');
    commitmentContent.innerHTML = '';
    commitmentContent.insertAdjacentHTML( "beforeend", content );

    commitmentNav.classList.add('shown');
    commitmentAnswer.classList.add('shown');
    slideToggle(commitmentAnswer);
    syncTabsWithSidebar();
    scrollSpy.init();
}

export function handleScrollToLinkInTab({target}) {
    const parent = target.closest('[data-scroll-tab-id]');

    if(!parent) return;

    const { dataset } = parent;

    const currentTab = qs(`.js-second-level-title[data-tab-nav="${dataset.scrollTabId}"][data-tab-parent="${dataset.scrollParentId}"]`);
    if (currentTab) currentTab.click();
}

export function syncTabsWithSidebar() {
    const tabs = qsa('.js-second-level-title.active');

    tabs.forEach(tab => {
        const {dataset} = tab;
        const nav = qs(`[data-scroll-tab-id="${dataset.tabNav}"][data-scroll-parent-id="${dataset.tabParent}"]`);

        if (nav) {
            const commitmentAnswer = nav.closest('.js-sidebar-commitment-answer');
            const answer = qs('[data-acc-answer]', nav);
            nav.classList.add('shown');
            answer.classList.add('shown');
            slideToggle(answer);
            setTimeout(() => slideToggle(commitmentAnswer), 200)
        }
    })
}