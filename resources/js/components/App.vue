<template>
<div id="app" class="container">
    <div class="row flex-d justify-content-center" style="background-color:navy ;padding:10px;">
        <img src="/images/buzzkill.png" class="text-center" style="padding:20px; width:300px"/>
    </div>

    <div class="row topnav" style="color: white; padding-left:25%">
        <div class="col-3"><router-link to="/" class="nav-item nav-link">Home</router-link></div>
        <div class="col-3"><router-link to="/manage-drinks" class="nav-item nav-link">Manage Drinks</router-link></div>
        <div class="col-3"><router-link to="/add-drink" class="nav-item nav-link">Add Drink</router-link></div>
    </div>
    <hr />
    <div class="row pb-lg-5"></div>
    <router-view> </router-view>
</div>
</template>

<script>
    export default {
    }
</script>
