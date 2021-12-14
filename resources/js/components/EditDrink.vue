<template>
    <div class="align-content-center text-center d-flex justify-content-center">
        <form @submit="checkForm">
            <h3>Edit Drink</h3>
            <p v-if="errors.length">
                <b>Please correct the following error(s):</b>
            <ul>
                <li v-for="error in errors">{{ error }}</li>
            </ul>
            </p>
            <div class="row">
                <div class="col-6 p-3">
                    Name:
                </div>
                <div class="col-3 p-3">
                    <input id="name" style="width: 200px" v-model="drink.name" type="text" name="name" />
                </div>
            </div>
            <div class="row">
                <div class="col-6 p-3">
                    MG Caffeine per Serving:
                </div>
                <div class="col-3 p-3">
                    <input id="mg" style="width: 200px" v-model="drink.mg_caffeine_per_serving" type="number" name="mg" min="0"/>
                </div>
            </div>
            <div class="row">
                <div class="col-6 p-3">
                    Servings per Container:
                </div>
                <div class="col-3 p-3">
                    <input id="servings" style="width: 200px" v-model="drink.servings_per_container" type="number" name="servings" min="1"/>
                </div>
            </div>

            <div class="row d-flex justify-content-center">
                <div class="col-5 p-3">
                    <button class="btn btn-success">Update Drink</button>

                </div>
            </div>
        </form>
    </div>
</template>

<script>
    import axios from 'axios'
    window.$ = require('jquery');
    export default {
        data() {
            return {
                CSRF_TOKEN: $('meta[name="csrf-token"]').attr('content'),
                drink: {},
                submitted: false,
                errors: []
            }
        },
        mounted() {
            this.getDrink();
        },
        methods: {
            checkForm: function (e) {
                if(this.drink.name && this.drink.mg_caffeine_per_serving && this.drink.servings_per_container) {
                    this.updateDrink();
                }

                if(!this.drink.name) {
                    this.errors.push("Drink name is required.")
                }

                if(!this.drink.mg_caffeine_per_serving) {
                    this.errors.push("MG per serving is required.")
                }

                if(!this.drink.servings_per_container) {
                    this.errors.push("Servings per container is required.")
                }

                e.preventDefault();
            },
            getDrink() {
                axios
                    .get(`/api/drink/edit/${this.$route.params.id}`)
                    .then(response => {
                        this.drink = response.data.drink[0];
                    });
            },
            updateDrink() {
                axios.post('/api/drink/update/' + this.drink.id + '/' + this.drink.name + '/' + this.drink.mg_caffeine_per_serving + '/' + this.drink.servings_per_container, {
                    _token: this.CSRF_TOKEN
                }, {
                    headers: {
                        "Authorization": 'Basic ' + this.CSRF_TOKEN,
                        "Content-Type": "application/json",
                        "cache-control": "no-cache"
                    },
                })
                    .then(response => {
                        this.submitted = true
                        alert('Drink updated!');
                    });
            }
        }
    }
</script>