<template>
  <div id="nav">
    <router-view />
  </div>
</template>

<style>
html,
body {
  margin: 0;
  padding: 0;
  font-family: Arial, Helvetica, sans-serif;
  background-color: #eeeeee;
}

a,
.hyperlink {
  text-decoration: none;
  color: #00519b;
}

span.required {
  color: red;
}

.body {
  display: flex;
  justify-content: center;
  flex-direction: column;
  background-color: white;
  width: 80%;
  padding: 1rem;
  margin: 0 auto;
}

.d-flex {
  display: flex;
  align-items: center;
  justify-content: center;
  flex-direction: row;
  flex-wrap: wrap;
}
select,
input,
textarea {
  padding: 0.5rem;
  font-family: Arial, Helvetica, sans-serif;
}

button {
  padding: 0.5rem;
  background-color: unset;
  border: 1px solid #fd5b09;
  background-color: #fd5b09;
  color: white;
  cursor: pointer;
  transition: 0.2s all ease-in-out;
}

button:hover {
  background-color: transparent;
  color: #fd5b09;
}

.m-0 {
  margin: 0;
}
</style>
