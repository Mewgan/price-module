export var routes = [
    {
        path: 'service-list',
        name: 'module:price',
        component: resolve => {
            require(['./components/Service.vue'], resolve)
        }
    }
];

export var content_routes = {
    price: (resolve) => {
        require(['./components/Module/PriceModule.vue'], resolve)
    },
    userPrice: (resolve) => {
        require(['./components/Module/UserPriceModule.vue'], resolve)
    }
};

export default {
    routes,
    content_routes
}