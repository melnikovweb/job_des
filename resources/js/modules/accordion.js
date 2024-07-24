import { qs, slideToggle } from "../utils/dom";

export default function initAccordion() {
    function _toggleItem(item) {
        const answer = qs('[data-acc-answer]', item)

        item.classList.toggle('shown')
        answer.classList.toggle('shown')
        
        slideToggle(answer);

        const parent = item.closest('.parent');
        if ( parent ) {
            const parentAnswer = qs('[data-acc-answer]', parent);
            setTimeout(() => slideToggle(parentAnswer), 200);
        }
    }

    // function _closeAccordions(current = false) {
    //     const items = qsa('[data-acc-item]')
    //     items.forEach(i => (i !== current && i.matches('.shown')) && _toggleItem(i))
    // }

    async function _handleAccItems(target) {
        
        if(!target.matches('[data-acc-question]')) return
        
        const item = target.closest('[data-acc-item]')
        
        /* toggle current item */
        _toggleItem(item)
        /* toggle other items */
        // _closeAccordions(item)
        
    }

    document.addEventListener('click', ({target}) => {
        _handleAccItems(target)
    })
}