<template>
    <div>
        <form @submit="checkForm">
            <div class="row text-center pb-lg-5">
                <h3>Caffeine Limit Calculator</h3>
                <p v-if="errors.length">
                    <b>Please correct the following error(s):</b>
                    <ul>
                        <li v-for="error in errors">{{ error }}</li>
                    </ul>
                </p>
                <div class="col-4 p-3">
                    Which drink did you have?
                    <select id="selected_drink" v-model="selected_drink">
                        <option v-for="drink in drinks" v-bind:value="drink.id">{{ drink.name }}</option>
                    </select>
                </div>
                <div class="col-3 p-3">
                    Number of servings:
                    <input id="quantity" style="width: 50px" v-model="quantity" type="number" name="quantity" min="1">
                </div>
                <div class="col-3 p-3">
                    <button type="submit" class="btn btn-primary">Calculate</button>
                </div>
            </div>
        </form>
        <div class="row align-content-center d-flex justify-content-center" v-if="this.calculatedDrinks.length">
            <div class="col-9 text-center pt-lg-6">
                <h3>Maximum servings remaining of each drink:</h3>
                <div style="font-size: 16px" v-for="drink in this.calculatedDrinks">{{ drink.name }}: {{ drink.servingsRemaining }}</div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                drinks: [],
                selected_drink: '',
                quantity: '',
                calculatedDrinks: [],
                errors: []
            }
        },
        mounted() {
            this.getDrinks();
        },
        methods: {
                checkForm: function (e) {
                    if(this.selected_drink && this.quantity) {
                        this.getDrinkCalculation();
                    }

                    if(!this.selected_drink) {
                        this.errors.push("You must select a drink.")
                    }

                    if(!this.quantity) {
                        this.errors.push("You must enter the quantity of the drink you've consumed.")
                    }

                    e.preventDefault();

            },
            getDrinks() {
                this.axios
                    .get('/api/drink/index')
                    .then(response => {
                        this.drinks = response.data.drinks;
                    });
            },
            getDrinkCalculation() {
                this.error = ''
                axios.post('/api/drink/getDrinkLimitList/' + this.selected_drink + '/' + this.quantity, {
                    _token: this.CSRF_TOKEN
                }, {
                    headers: {
                        "Authorization": 'Basic ' + this.CSRF_TOKEN,
                        "Content-Type": "application/json",
                        "cache-control": "no-cache"
                    },
                })
                    .then(response => {
                        this.submitted = true;
                        this.calculatedDrinks = response.data.drinks
                    });
            }
        }
    }
</script>
