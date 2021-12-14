<template>
    <div class="text-center  grid" style="width:100%">
        <div class="row" style="width:100%">
            <div class="col-lg-12">
                <h3>Manage Drinks</h3>
                <h5>Click on a drink name to edit it.</h5>
            </div>
        </div>
        <div class="row align-content-center d-flex justify-content-center">
            <div class="col-12 d-flex justify-content-center pt-lg-4" >
                <table class="table" style="width:70%">
                    <thead>
                    <tr>
                        <th>Drink Name</th>
                        <th>Caffeine (mg) Per Serving</th>
                        <th>Servings Per Container</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="drink in drinks" :key="drink.id">
                        <td><router-link :to="{name: 'edit', params: { id: drink.id }}">{{ drink.name }}</router-link></td>
                        <td>{{ drink.mg_caffeine_per_serving }} mg</td>
                        <td>{{ drink.servings_per_container }}</td>
                        <td>
                            <div class="btn-group" role="group">
                                <button class="btn btn-danger" @click="deleteDrink(drink.id, drink.name)">Delete</button>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
</div>
</template>

<script>
    export default {
        data() {
            return {
                drinks: []
            }
        },
        mounted() {
            this.getDrinks();
        },
        methods: {
            getDrinks() {
                this.axios
                    .get('/api/drink/index')
                    .then(response => {
                        this.drinks = response.data.drinks;
                    });
            },
            deleteDrink(id, name) {
                if(confirm('Are you sure you want to delete "' + name + '"? This operation cannot be undone!')) {
                    this.axios
                        .post(`/api/drink/delete/` + id, {
                            _token: this.CSRF_TOKEN
                        },{
                            headers: {
                                "Authorization": 'Basic ' + this.CSRF_TOKEN,
                                "Content-Type": "application/json",
                                "cache-control": "no-cache"
                            },
                        })
                        .then(response => {
                            alert('Drink deleted.');
                            let index = this.drinks.map(data => data.id).indexOf(id);
                            this.drinks.splice(index, 1)
                        });
                }
            }
        }
    }
</script>