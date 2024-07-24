import { addGlobalEventListener, qsa, qs, slideToggle } from "../utils/dom";
import { updateParamsData } from "./../utils/URLParams"

export default (function () {
  function _handleSelectedHTML(box) {
    const select = box.closest( '.js-sk-form-select' );
    const inputs = qsa('.js-sk-form-select-option input', box);
    const selected = qs('.js-sk-form-select-selected', box);

    let selectedValues = [];
    let selectedItems = inputs.map(input => {
    let checkedInput = input.checked;

    if ( !checkedInput ) {
        input.closest( '.js-sk-form-select-option' ).classList.remove( 'selected' );
        return '';
    } else {
        input.closest( '.js-sk-form-select-option' ).classList.add( 'selected' );
    }

    selectedValues.push( input.value );

    return `
        <div class="sk-form-select-selected-item js-selected-item" data-value="${input.value}">
            ${input.value}
            <div class="sk-form-select-selected-close js-remove-selected-item">
                <svg><use href="#icon-close"></use></svg>
            </div>
        </div>`;

    }).join('');

    if ( selectedItems.length > 0 ) {
      select.classList.add( 'not-empty' );
      selected.innerHTML = selectedItems;
    } else {
      select.classList.remove( 'not-empty' );
      selected.innerHTML = '';
    }
    
    // filter table
    const table = qs('table', box.closest( '.js-sk-table' ));
    
    if ( table ) {
        const tbody = qs('tbody', table);
        const tableRows = qsa('tr', tbody);

        if ( selectedItems.length > 0 ) {
            tableRows.forEach(row => row.style.display = 'none');
            selectedValues.forEach(value => qs(`[data-sort-value="${value}"]`, table).closest('tr').style.display = '');
        } else {
            tableRows.forEach(row => row.style.display = '');
        }
    }
    // END filter table
    
    // recalc height accordion
    const answer = qs('[data-acc-answer]', box.closest( '[data-acc-item]' ));

    if ( answer ) {
      slideToggle(answer)
    }
    // END recalc height accordion
  }

  function _showSelected(target = false) {
    if(!target) {
      qsa('.js-sk-form-select').forEach(selectBox => _handleSelectedHTML(selectBox))
      return
    }

    const singleSelect = target.closest('.js-sk-form-select-single');

    if ( singleSelect ) {
      const inputs = qsa('.js-sk-form-select-option input', singleSelect);
      inputs.forEach(input => input !== target && (input.checked = false))
    }

    _handleSelectedHTML(target.closest('.js-sk-form-select'))
  }

  function _closeOtherBoxes() {
    qsa('.js-sk-form-select').forEach(select => select.classList.remove('openned'))
  }

  function _handleSelectedLabels({target}) {
    const currentBox = target.closest('.js-sk-form-select')
    const currentItem = target.closest('.js-selected-item')
    const currentValue = currentItem.dataset.value
    const chapterTab = target.closest( '.js-chapter-tab' )
    const searchCommitment = target.closest( '.js-search-commitment' )

    qsa('input[type="checkbox"]', currentBox).forEach(checkbox => {
      if( checkbox.value === currentValue ) {
        checkbox.checked = false;
        currentItem.remove();

        /* Remove from url params */
        updateParamsData({
          value: checkbox.dataset.paramsValue,
          key: checkbox.dataset.paramsKey,
          action: checkbox.dataset.paramsAction,
        })
        
        _showSelected(checkbox);
      }
    })
    
    // remove post data (Signatory single)
    if ( chapterTab ) {
      const selectedItems = qsa('.js-selected-item', searchCommitment)
      if ( !selectedItems.length ) {
        const postContent = qs( '.js-commitment-post', chapterTab );
        postContent.innerHTML = '';
      }
    }
  }

  function _toggleSelect({target}) {
    const parent = target.closest('.js-sk-form-select');

    _closeOtherBoxes();

    if ( parent ) {
      parent.classList.add('openned');
      return;
    }
  }

  function _findOptions({target}) {
    const { value } = target

    const currentBox = target.closest('.js-sk-form-select');

    if( !currentBox ) return;

    const resultsBox = qs('.js-sk-form-select-result', currentBox);
    const noResultsBox = qs('.js-sk-form-select-noresult', currentBox);
    const noResultsText = qs('.js-sk-form-noresults-text', noResultsBox);
    const options = qsa('.js-sk-form-select-option', currentBox);

    let hasResults = false;

    options.forEach(option => {
      const optionLabel = qs('.js-checkbox-wrapper-label', option);
      const text = optionLabel.innerHTML.toLowerCase();
      

      if(text.includes(value.toLowerCase())) {
        option.style.display = '';
        hasResults = true;
    } else {
        option.style.display = 'none';
      }
    })

    if(hasResults) {
      resultsBox.style.display = '';
      noResultsText.innerHTML = '';
      noResultsBox.style.display = '';
      return;
    }

    resultsBox.style.display = 'none';
    noResultsText.innerHTML = `“${value}”`;
    noResultsBox.style.display = 'block';
  }

  function _init() {
    document.addEventListener('click', _toggleSelect);

    addGlobalEventListener('keyup', '.js-sk-form-select-control', _findOptions);
    addGlobalEventListener('click', '.js-remove-selected-item', _handleSelectedLabels);
    addGlobalEventListener('change', '.js-sk-form-select-option input', (e) => _showSelected(e.target))
  }

  return {
    init: () => _init(),
    syncSelected: () => _showSelected(),
  }
})();