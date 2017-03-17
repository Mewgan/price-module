<style>
    .team-role-list{
        overflow: auto;
    }
</style>

<template>
    <div class="team-role-list">

        <div class="card">
            <div class="card-head style-primary">
                <header><i class="fa fa-fw fa-tag"></i> Catégories</header>
            </div>
            <div class="card-body">
                <ul class="list list-accordion panel-group" id="service-accordion" data-sortable="true">
                    <li v-for="category in categories" class="tile">
                        <a @click="selectCategory(category)" class="tile-content ink-reaction">
                            <div class="tile-text">{{ category.name }}</div>
                        </a>
                        <a @click="selectRole(role)" data-toggle="modal" data-target="#editServiceCategoryModal"
                           class="btn btn-flat">
                            <i class="fa fa-pencil"></i>
                        </a>
                        <a @click="selectRole(role)" data-toggle="modal" data-target="#deleteServiceCategoryModal"
                           class="btn btn-flat ink-reaction">
                            <i class="fa fa-trash"></i>
                        </a>
                    </li>
                </ul>
            </div><!--end .card-body -->
        </div><!--end .card -->

        <button data-toggle="modal" @click="clearCategory" data-target="#editServiceCategoryModal"
                class="btn ink-reaction btn-raised btn-lg btn-info pull-right">
            <i class="fa fa-plus" aria-hidden="true"></i>
            Ajouter une catégorie
        </button>

        <div class="modal fade" id="editServiceCategoryModal" tabindex="-1" role="dialog"
             aria-labelledby="formModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="editFormModalLabel">Ajouter/Modifier une catégorie</h4>
                    </div>
                    <form class="form" role="form">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <input type="text" class="form-control" id="role-name" v-model="category.name">
                                        <label for="role-name">Nom</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
                            <button type="button" data-dismiss="modal" @click="updateOrCreate" class="btn btn-primary">
                                Enregistrer
                            </button>
                        </div>
                    </form>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->

        <div class="modal fade" id="deleteServiceCategoryModal" tabindex="-1" role="dialog" aria-labelledby="formModalLabel"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="deleteFormModalLabel">Suppression</h4>
                    </div>
                    <div class="modal-body">
                        <p>Êtes-vous sûr de vouloir supprimer la catégorie ?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Non</button>
                        <button type="button" @click="deleteCategory" data-dismiss="modal" class="btn btn-primary">Oui</button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->

    </div>

</template>


<script type="text/babel">

    import {mapActions} from 'vuex'
    import {service_category_api} from '../api'

    export default
    {
        name: 'service-category-list',
        props: {
            website_id: {
                required: true
            },
            categories: {
                type: Array,
                default: () => {
                    return [];
                }
            }
        },
        data () {
            return {
                category: {
                    name: 'Aucune catégorie choisie',
                    position: 0,
                }
            }
        },
        methods: {
            clearCategory(){
                this.category =  {
                    name: ''
                };
            },
            editCategory(category){
                this.category = category;
            },
            selectCategory(category){
                this.$emit('selectCategory', category);
            },
            updateOrCreate(){

            },
            deleteCategory(){
                if (this.category.id !== undefined) {
                    this.destroy({
                        api: price_category_api.destroy + this.website_id,
                        ids: [this.category.id]
                    }).then((response) => {
                        if (response.data.status == 'success'){
                            let index = this.categories.findIndex((i) => i.id == this.category.id);
                            this.categories.splice(index, 1);
                        }
                    })
                }
            }
        }
    }
</script>
