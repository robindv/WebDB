<template>
  <div id="app">

<div id="header"><div id="logo"><div>Cursus</div></div></div>
<div id="nav">
 <ul id="menu">
      <li><router-link to="/">Home</router-link></li>
      <li><router-link to="/about">About</router-link></li>
 </ul>
</div>
<div id="main">
    <router-view/>
</div>

  </div>
</template>

