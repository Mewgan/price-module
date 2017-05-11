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
                <li class="active">Tarif <a data-toggle="modal" data-target="#infoPriceModal"><i class="fa fa-info-circle"></i></a></li>
            </ol>
        </div>

        <div class="section-body">

            <div class="col-lg-4 col-md-12 mb10">
                <service-category-list :website_id="website_id" :categories="categories" @reloadServices="reload_services = !reload_services" @selectCategory="selectCategory"></service-category-list>
            </div>

            <div class="col-lg-8 col-md-12">
                <service-list :website_id="website_id" :reload_services="reload_services" :category="category"></service-list>
            </div><!--end .section-body -->

        </div>

        <!-- Modal Structure -->
        <div class="modal fade" id="infoPriceModal" tabindex="-1" role="dialog"
             aria-labelledby="simpleModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog modal-xlg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="infoPriceModalLabel">Information</h4>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info clearfix" role="alert">
                            <div class="col-md-4 col-sm-12">
                                <h4 class="m0 mb10">1/ Catégories</h4>
                                <p><i class="fa drag-arrows fa-arrows mr10"></i> Pour définir l'ordre d'affichage de vos catégories</p>
                                <p><i class="fa fa-pencil mr10"></i> Pour modifier une catégorie</p>
                                <p><i class="fa fa-trash mr10"></i> Pour supprimer une catégorie</p>
                                <p><i class="fa fa-arrow-right mr10"></i> Pour afficher les tarifs de la catégorie</p>
                                <p><i class="fa fa-save"></i> Enregistrer : Pour mettre à jour les catégories</p>
                            </div>
                            <div class="col-md-8 col-sm-12">
                                <h4 class="m0 mb10">2/ Tarifs</h4>
                                <p>Séléctionner une catégorie pour afficher ses tarifs</p>
                                <p><i class="fa drag-arrows fa-arrows mr10"></i> Pour définir l'ordre d'affichage de vos tarifs</p>
                                <p><i class="fa fa-angle-left mr10"></i> Pour ajouter/modifier la description d'un tarif</p>
                                <p><i class="fa fa-trash mr10"></i> Pour supprimer un tarif</p>
                                <p><i class="fa fa-save"></i> Enregistrer : Pour mettre à jour les tarifs</p>
                            </div>
                        </div>

                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div>

    </section>

</template>


<script type="text/babel">

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
            }
        },
        data () {
            return {
                website_id: this.$route.params.website_id,
                reload_services: false,
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
