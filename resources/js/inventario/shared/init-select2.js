const SELECTOR = 'select.select2';
const INITIALIZED_FLAG = 'data-select2-init';
const DEFAULT_WIDTH = '100%';
const SEARCH_DISABLED_FLAG = 'data-select2-search';
const ALLOW_CLEAR_FLAG = 'data-select2-allow-clear';

function initializeSelect(element) {
    if (typeof window.$ === 'undefined' || typeof window.$.fn === 'undefined' || typeof window.$.fn.select2 === 'undefined') {
        return;
    }

    if (!element || element.hasAttribute(INITIALIZED_FLAG) || element.dataset.select2 === 'false') {
        return;
    }

    const options = {
        theme: 'bootstrap4',
        width: element.dataset.select2Width || DEFAULT_WIDTH
    };

    if (element.dataset.select2Placeholder) {
        options.placeholder = element.dataset.select2Placeholder;
    }

    if (element.getAttribute(SEARCH_DISABLED_FLAG) === 'off') {
        options.minimumResultsForSearch = Infinity;
    }

    if (element.getAttribute(ALLOW_CLEAR_FLAG) === 'true') {
        options.allowClear = true;
    }

    window.$(element).select2(options);
    element.setAttribute(INITIALIZED_FLAG, 'true');
}

function initializeAllSelects() {
    if (typeof window.$ === 'undefined' || typeof window.$.fn === 'undefined' || typeof window.$.fn.select2 === 'undefined') {
        return;
    }

    const elements = document.querySelectorAll(SELECTOR);
    elements.forEach(initializeSelect);
}

document.addEventListener('DOMContentLoaded', initializeAllSelects);

