<script>

export default {
  props: {
    msg: {
      type: String,
      required: true
    }
  },
  data() {
    return {
      searchTerm: ''
    };
  },
  methods: {
    updatePublicationsList(searchTerm) {
      this.searchTerm = searchTerm;
      this.emitter.emit("PublicationList-SearchTerm", this.searchTerm);
    }
  }
};
</script>

<template>
  <div class="menu">
    <h1 class="green">{{ msg }}</h1>
    <button @click="updatePublicationsList('Matrix')">Matrix</button>
    <button @click="updatePublicationsList('Matrix Reloaded')">Matrix Reloaded</button>
    <button @click="updatePublicationsList('Matrix Revolutions')">Matrix Revolutions</button>
  </div>
</template>

<style scoped>
h1 {
  font-weight: 500;
  font-size: 2.6rem;
  top: -10px;
}

.menu h1 {
  text-align: center;
}

@media (min-width: 1024px) {
  .menu h1 {
    text-align: left;
  }
}
</style>
