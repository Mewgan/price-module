<style>
    .price-module .breadcrumb {
        display: inline-block;
    }
    .price-module button {
        margin-left: 10px;
    }
    .price-module .section-header .btn{
        margin-left: 10px;
    }
</style>

<template>
    <section class="price-module">

        <div class="section-header">
            <ol class="breadcrumb">
                <li class="active">Tarif</li>
            </ol>
        </div>

        <div class="section-body">

            <div class="alert alert-info" role="alert">
                <strong><i class="fa fa-info-circle"></i> Gérer vos tarifs</strong><br/>
            </div>

            <div class="col-lg-4 col-md-12 col-sm-12 mb10">
                <service-category-list :website_id="website_id" :categories="categories" @selectCategory="selectCategory"></service-category-list>
            </div>

            <div class="col-lg-8 col-md-12 col-sm-12">
                <service-list :website_id="website_id" :category="category"></service-list>
            </div><!--end .section-body -->

        </div>

    </section>

</template>


<script type="text/babel">

    import '@admin/libs/jquery-ui/jquery-ui.min'
    import {mapActions} from 'vuex'

    import {service_category_api} from '../api'

    export default
    {
        components: {
            ServiceList: resolve => {
                require(['./ServiceList.vue'], resolve)
            },
            ServiceCategoryList: resolve => {
                require(['./ServiceCategoryList.vue'], resolve)
            },
        },
        data () {
            return {
                website_id: this.$route.params.website_id,
                categories: [],
                category: {
                    name: ''
                }
            }
        },
        methods: {
            ...mapActions(['read']),
            selectCategory(category){
                this.category = category;
            }
        },
        created() {
            this.read({api: service_category_api.all + this.website_id}).then((response) => {
                if(response.data.resource !== undefined)
                    this.categories = response.data.resource;
            })
        }
    }
</script>
