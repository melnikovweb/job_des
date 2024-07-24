import { getCSSVariable, addGlobalEventListener, qs } from "./../utils/dom";

export const appHeight = () => {
    const doc = document.documentElement;
    const header = qs( '.js-sk-header' );
    if (header) doc.style.setProperty('--app-header-height', `${header.clientHeight}`);
    if (header) doc.style.setProperty('--app-header-height-px', `${header.clientHeight}px`);
}

export function getOffset(el) {
    const rect = el.getBoundingClientRect();
    return {
        left: rect.left + window.scrollX,
        top: rect.top + window.scrollY,
    };
}

const scrollToCallback = (offset, callback) => {
    const fixedOffset = offset.toFixed();

    const onScroll = function () {
        if (window.pageYOffset.toFixed() === fixedOffset) {
        window.removeEventListener("scroll", onScroll);
        callback();
        }
    };

    window.addEventListener("scroll", onScroll);
    onScroll();
    window.scrollTo({
        top: offset,
        behavior: "smooth",
    });
};
  
const scrollTo = (event) => {
    event.preventDefault();

    const id = event.target.getAttribute("href");
    const element = qs(id);

    if (!element) return;

    history.replaceState("", "", id);

    const headerHeight = getCSSVariable("--app-header-height");
    const coorsElement = getOffset(element);

    scrollToCallback(coorsElement.top - headerHeight, () => console.log("scrolled"));
};

export const initCommon = () => {
    // get height header, set css variable
    window.addEventListener('resize', appHeight);
    appHeight();

    // scrollTo
    addGlobalEventListener("click", 'a[href^="#"]:not(.js-scroll-spy-link)', scrollTo);
}