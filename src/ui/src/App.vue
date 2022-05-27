<script setup>
import Menu from './components/Menu.vue'
import PublicationsList from './components/PublicationsList.vue'
</script>

<template>
  <header>
    <div class="wrapper">
      <Menu msg="Check Publications" />
    </div>
  </header>
  <main>
    <PublicationsList />
  </main>
</template>

<style>
@import './assets/base.css';

#app {
  max-width: 1280px;
  margin: 0 auto;
  padding: 2rem;

  font-weight: normal;
}

header {
  line-height: 1.5;
}

a,
.green {
  text-decoration: none;
  color: hsla(160, 100%, 37%, 1);
  transition: 0.4s;
}

@media (hover: hover) {
  a:hover {
    background-color: hsla(160, 100%, 37%, 0.2);
  }
}

@media (min-width: 1024px) {
  body {
    display: flex;
    place-items: center;
  }

  #app {
    display: grid;
    grid-template-columns: 1fr 3fr;
    padding: 0 2rem;
  }

  header {
    display: flex;
    place-items: flex-start;
    padding-right: calc(var(--section-gap) / 2);
    padding-top: 100px;
  }

  header .wrapper {
    display: flex;
    place-items: flex-start;
    flex-wrap: wrap;
  }
}
</style>
