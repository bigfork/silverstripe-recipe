import Splide from '@splidejs/splide';

export const ElementFullWidthCarousel = () => {
  const carousels = document.querySelectorAll('[data-element-full-width-carousel]');

  const initCarousel = carousel => {
    new Splide(carousel).mount();
  }

  carousels.forEach(initCarousel);
}
