// frontend/src/directives/clickOutside.js
export default {
  mounted(el, binding) {
    el._clickOutsideHandler = (event) => {
      if (!el.contains(event.target)) {
        binding.value();
      }
    };
    document.addEventListener('click', el._clickOutsideHandler);
  },
  unmounted(el) {
    document.removeEventListener('click', el._clickOutsideHandler);
  },
};
