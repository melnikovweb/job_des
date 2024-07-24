import { addGlobalEventListener, qsa, qs } from "../utils/dom";

export default (function () {
  function _closeOtherBoxes(current) {
    qsa('[data-dropdown]').forEach(dropdown => {
        if ( dropdown === current ) return;
        
        dropdown.classList.remove('openned');
    })
  }

  function _changeSelectedLabel(selector) {
    const parent = selector.closest('[data-dropdown]');
    const label = selector.matches( '[data-dropdown-label]' ) ? selector : selector.closest( '[data-dropdown-label]' );

    if ( label ) {
        let selectedLabel = qs( '[data-dropdown-selected-label]', parent );
        selectedLabel.innerHTML = label.dataset.dropdownLabel;
    }
  }

  function _toggleDropdown({target}) {
    const parent = target.closest('[data-dropdown]');

    if ( !parent ) {
        qs( '[data-dropdown].openned' )?.classList.remove( 'openned' );

        return
    }

    _changeSelectedLabel(target);

    _closeOtherBoxes(parent);

    parent.classList.toggle('openned');
  }

  function _init() {
    document.addEventListener('click', _toggleDropdown);

    addGlobalEventListener('click', '[data-dropdown-selected]', _toggleDropdown);
  }

  return {
    init: () => _init(),
    changeSelectedLabel: (selector) => _changeSelectedLabel(selector),
  }
})();