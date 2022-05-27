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
        <span v-if="publication.poster !== 'N/A'">
            <img v-bind:src="publication.poster" height="100" alt="Poster" />
        </span>
        <span v-if="publication.poster === 'N/A'" class="no-image">no poster</span>
      </template>
      <template #heading>{{ publication.title }}</template>
      <strong class="uppercase">{{ publication.type }}</strong>
      <div v-if="publication.year">Year: {{ publication.year }}</div>
      <div v-if="!publication.year">&nbsp</div>
    </PublicationItem>
  </div>
</template>

<style scoped>
   .uppercase {
     text-transform: uppercase;
   }
</style>
