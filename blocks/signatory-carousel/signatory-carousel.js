import Swiper, { Autoplay } from "swiper";
import "swiper/css/bundle";

// init Swiper:
export let swiper = new Swiper(".mySwiper", {
  modules: [Autoplay],
  slidesPerView: 1,
  spaceBetween: 10,
  loop: true,
  autoplay: {
    delay: 1500,
  },
  loopFillGroupWithBlank: true,
  centeredSlides: true,
  breakpoints: {
    320: {
      slidesPerView: 3,
      spaceBetween: 20,
    },
    640: {
      slidesPerView: 4,
      spaceBetween: 20,
    },
    768: {
      slidesPerView: 5,
      spaceBetween: 40,
    },
    1024: {
      slidesPerView: 7,
      spaceBetween: 50,
    },
  },
});
