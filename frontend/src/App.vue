<template>
  <div id="app">
  <router-view></router-view>
  </div>
</template>

<script setup>
// Não use export default aqui
// O <script setup> já define o componente automaticamente
</script>

<style>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

body {
  min-height: 100vh;
  background-color: #f7f8fc;
}

#app {
  height: 100vh;
}
</style>
