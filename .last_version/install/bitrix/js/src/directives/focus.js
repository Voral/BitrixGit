const Focus = {
    mounted: (el, binding) => {
        if (binding.value) {
            el.focus();
        }
    },
};
export default Focus;