import DrinkList from './components/DrinkList.vue';
import AddDrink from './components/AddDrink.vue';
import EditDrink from './components/EditDrink.vue';
import ManageDrinks from './components/ManageDrinks.vue';


export const routes = [
    {
        name: 'home',
        path: '/',
        component: DrinkList,
        meta: {
            allowAnonymous: false
        }
    },
    {
        name: 'add-drink',
        path: '/add-drink',
        component: AddDrink,
        meta: {
            allowAnonymous: false
        }
    },
    {
        name: 'edit',
        path: '/edit/:id',
        component: EditDrink,
        meta: {
            allowAnonymous: false
        }
    },
    {
        name: 'manage-drinks',
        path: '/manage-drinks',
        component: ManageDrinks,
        meta: {
            allowAnonymous: false
        }
    }
];