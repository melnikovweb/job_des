import AOS from "aos";
import { navbarToggler } from "./modules/navbar.js";
import { searchSimpleForm } from "./modules/search-simple.js";
import initTooltips from "./modules/tooltip";
import initAccordion from "./modules/accordion";
import initTableSort from "./modules/tableEvents";
import { getCSSVariable } from "./utils/dom";
import CustomSelect from "./modules/customSelect";
import Dropdown from "./modules/dropdown";
import { initTabs } from "./modules/tabs";
import initURLParams from "./utils/URLParams";
import initFileGeneration from './modules/FileGeneration'
import { initCommon } from './modules/common.js'

AOS.init({
  once: true,
  disable: window.innerWidth < getCSSVariable("--breakpoint-md-s"),
});

window.addEventListener("load", () => {
  navbarToggler().init();
  searchSimpleForm();
  initTooltips();
  initAccordion();
  initTableSort();
  CustomSelect.init();
  initTabs();
  Dropdown.init();
  initURLParams();
  initFileGeneration();
  initCommon();
});

window.addEventListener("resize", () => {});
