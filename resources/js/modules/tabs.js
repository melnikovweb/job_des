import { addGlobalEventListener, qsa, qs } from "../utils/dom";
import Dropdown from './dropdown';

const handlerTab = (e) => {
    e.preventDefault();

    const target = e.target;
    const id = target.dataset.tabNav;
    const parentId = target.dataset.tabParent;

    const parentTabs = target.closest( '[data-tabs]' );
    const currentTabNavs = qsa(`[data-tab-nav="${id}"][data-tab-parent="${parentId}"]`);
    const currentTab = qs(`[data-tab="${id}"][data-tab-parent="${parentId}"]`);
    const allTabsNavs = qsa(`[data-tab-parent="${parentId}"]`, parentTabs);

    allTabsNavs.forEach(tab => tab.classList.remove( 'active' ));
    
    currentTabNavs.forEach(link => link.classList.add( 'active' ))

    // currentTabNav.classList.add( 'active' );
    currentTab.classList.add( 'active' );

    // change selected label dropdown
    const dropdownNav = qs( '[data-dropdown]', parentTabs );
    if ( dropdownNav ) {
        const btn = qs(`[data-tab-nav="${id}"]`, dropdownNav);
        if (btn) Dropdown.changeSelectedLabel(btn);
    }
    // END change selected label dropdown
}

export const initTabs = () => {
    const tabs = qsa( '[data-tabs]' );
    
    if ( !tabs.length ) return;

    addGlobalEventListener( 'click', '[data-tab-nav]', handlerTab);
};