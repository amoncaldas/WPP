
const lang = {
  inserted: (el, binding) => {
    if (binding.value) {
      let htmlNode = document.getElementsByTagName('html')[0]
      htmlNode.lang = binding.value
    }
  },
  update: (el, binding) => {
    if (binding.value) {
      let htmlNode = document.getElementsByTagName('html')[0]
      htmlNode.lang = binding.value
    }
  }
}
export default lang
