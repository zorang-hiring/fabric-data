<script setup>
import PublicationItem from './PublicationItem.vue'
</script>

<script>
export default {
  data() {
    return {
      publications: []
    };
  },
  mounted() {
    this.emitter.on("PublicationList-SearchTerm", searchTerm => {

      let loader = this.$loading.show();
      let self = this;

      // GET Publications from API
      this.exios.get(
          'http://127.0.0.1:8081/api/publications',
          {params: {q: searchTerm}}
          )
          .then(function (response) {
            if (
                response.status === 200
                && response.data.success === 'OK'
            ) {
              self.publications = response.data.result;
            }
          })
          .then(function () { loader.hide() });
    });
  }
};
</script>

<template>
  <div v-for="publication in publications">
    <PublicationItem>
      <template #icon>
        a
      </template>
      <template #heading>{{ publication.title }}</template>

      Vue’s
      <a target="_blank" href="https://vuejs.org/">official documentation</a>
      provides you with all information you need to get started.
    </PublicationItem>
  </div>
</template>
