<template>

    <div class="align-content-center text-center d-flex justify-content-center">
        <form @submit="checkForm">
        <h3>Add A New Drink</h3>
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
                <input id="name" style="width: 200px" v-model="drink_name" type="text" name="name"/>
            </div>
        </div>
        <div class="row">
            <div class="col-6 p-3">
                MG Caffeine per Serving:
            </div>
            <div class="col-3 p-3">
                <input id="mg" style="width: 200px" v-model="drink_mg" type="number" name="mg" min="0"/>
            </div>
        </div>
        <div class="row">
            <div class="col-6 p-3">
                Servings per Container:
            </div>
            <div class="col-3 p-3">
                <input id="servings" style="width: 200px" v-model="drink_servings" type="number" name="servings" min="1"/>
            </div>
        </div>
            <div class="row d-flex justify-content-center">
        <div class="col-4 p-3">
            <button class="btn btn-success">Add Drink</button>

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
                drink_name: '',
                drink_mg: '',
                drink_servings: '',
                submitted: false,
                errors: []
            }
        },
        methods: {
            checkForm: function (e) {
                if(this.drink_name && this.drink_mg && this.drink_servings) {
                    this.addDrink();
                }

                if(!this.drink_name) {
                    this.errors.push("Drink name required.")
                }

                if(!this.drink_mg) {
                    this.errors.push("MG per serving is required.")
                }

                if(!this.drink_servings) {
                    this.errors.push("Servings per container is required.")
                }
                e.preventDefault();
            },
            addDrink() {

                axios.post('/api/drink/commit/' + this.drink_name + '/' + this.drink_mg + '/' + this.drink_servings, {
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
                        alert('Drink added!');
                });
            }
        }
    }
</script>