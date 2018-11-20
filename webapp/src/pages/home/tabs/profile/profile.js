import UserForm from '@/fragments/forms/user/User'

export default {
  methods: {
    /**
     * The api provides some user data as a  meta property, but accept them as root object property
     * so, we set these data here, before updating the user
     * This callback is executed by the @core/crud.js before running the update method
     */
    beforeUpdate () {
      this.resource.email = this.resource.metas ? this.resource.metas.email : null
      this.resource.first_name = this.resource.metas ? this.resource.metas.first_name : null
      this.resource.last_name = this.resource.metas ? this.resource.metas.last_name : null
    },

    /**
     * Update the current logged in user's displayed name on dashboard after update the user data
     * This callback is executed by the @core/crud.js after running the update method
     * @param {*} data
     */
    afterUpdate (data) {
      this.$store.commit('userDisplayName', data.first_name)
    }
  },
  components: {
    UserForm
  }
}
