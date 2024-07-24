import { updateParamsData, getURLparams, syncElementsWithParams, updateURLParams } from "./../utils/URLParams"
import { qsa, qs, slideToggle } from "./../utils/dom"
import sendRequest from "./../utils/sendRequest"
import { generateNavigation } from "./../modules/renderReportNavigation"

export const firstLoadPage = () => {
    const data = getURLparams()

    if (data['chapter']) return;

    const chapter = qs('.js-chapter-link.active');
    const { dataset } = chapter;
    const value = dataset.paramsValue;
    updateParamsData({
        value,
        key: dataset.paramsKey,
        action: dataset.paramsAction,
    })
}

export function syncReportElementsWithParams() {
    const elements = qsa('[data-url-params]')

    if (elements.length === 0) return

    const data = getURLparams()

    elements.forEach(el => {
        const {tagName, dataset} = el
        const checkChapter = el.classList.contains('js-chapter-link');
        const commitment = el.closest('.js-sidebar-commitment-nav');

        const isInput = tagName === 'INPUT'

        if ( isInput ) return;

        const currentKey = dataset.paramsKey
        const currentValue = dataset.paramsValue

        let match = data[currentKey] ?? false

        if (!match || (match && data[currentKey] != currentValue)) {
            if ( checkChapter && el.classList.contains('active') ) {
                const chapterID = dataset.tabNav;
                el.classList.remove('active');
                qs(`.js-chapter-tab[data-tab="${chapterID}"]`).classList.remove('active');
            }

            if ( commitment && commitment.classList.contains('shown') ) {
                const answer = qs('.js-sidebar-commitment-answer', commitment);
                const answerContent = qs('.js-sidebar-commitment-content', commitment);
                commitment.classList.remove('shown', 'loaded');
                answer.classList.remove('shown');
                answerContent.innerHTML = '';
                slideToggle(answer);
            }

            return
        }

        if ( checkChapter ) {
            const chapterID = dataset.tabNav;
            el.classList.add('active');
            qs(`.js-chapter-tab[data-tab="${chapterID}"]`).classList.add('active');
        }
        if ( commitment ) {
            commitment.classList.add('shown');
        }
    });
}

export const handleChapter = ({target}) => {
    const { dataset } = target
    const currentValue = dataset.paramsValue
    const currentKey = dataset.paramsKey

    const data = getURLparams()

    if ( data[currentKey] == currentValue ) return

    updateParamsData({
        value: currentValue,
        key: dataset.paramsKey,
        action: dataset.paramsAction,
    })

    setTimeout(() => {
        const activeChapter = qs('.js-chapter-link.active').closest('li');
        let commitments = qsa('[data-commitment]', activeChapter);
        commitments = [...commitments].map(el => el.dataset.commitment);
        if ( !commitments.includes(data['commitment']) ) {
            updateURLParams({ commitment: null })
        }

        const activeCommitment = qs( '.js-sidebar-commitment-nav.shown [data-commitment]', activeChapter );
        if (activeCommitment) {
            const dataset = activeCommitment.dataset
            const currentValue = dataset.paramsValue
    
            updateParamsData({
                value: currentValue,
                key: dataset.paramsKey,
                action: dataset.paramsAction,
            });
        }

        syncElementsWithParams();
    }, 0);
}

export const handleCommitment = ({target}) => {
    const { dataset } = target
    const currentValue = dataset.paramsValue
    const currentKey = dataset.paramsKey

    const data = getURLparams()

    if ( data[currentKey] == currentValue ) return

    updateParamsData({
        value: currentValue,
        key: dataset.paramsKey,
        action: dataset.paramsAction,
    });


    setTimeout(() => {
        syncElementsWithParams();
    }, 0);
}

export const handleRemoveSelectedCommitment = () => {
    const activeCommitmentContent = qs( '.js-chapter-tab.active .js-commitment-post' );
    activeCommitmentContent.dataset.showCommitment = '';
    
    const activeCommitmentNav = qs( '.js-sidebar-commitment-nav.shown' );
    const activeCommitmentAnswer = qs( '.js-sidebar-commitment-answer', activeCommitmentNav );
    const activeCommitmentAnswerContent = qs( '.js-sidebar-commitment-content', activeCommitmentNav );
    activeCommitmentNav.classList.remove('shown', 'loaded');
    activeCommitmentAnswer.classList.remove('shown');
    activeCommitmentAnswerContent.innerHTML = '';
    slideToggle(activeCommitmentAnswer);
}

let controller = null

export async function renderPost(type) {
    try {

        if (controller) {
            controller.abort()
        }
        
        controller = new AbortController()
        const signal = controller.signal

        const container = qs('.js-chapter-tab.active .js-commitment-post');
        const parentContainer = qs('.js-wrap-data-content');

        const containerData = container.dataset;
        let queryParams = getURLparams();
        const commitmentID = queryParams.commitment;

        if ( !commitmentID || ( type === 'single-signatory' && containerData.showCommitment == commitmentID) ) return
        
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

            if ( results?.postsData ) {
                container.dataset.showCommitment = commitmentID;
                container.insertAdjacentHTML( "beforeend", results.postsData )

                generateNavigation(commitmentID);
            }
        }
        
    } catch(err) {
        console.error(err)
    }
}