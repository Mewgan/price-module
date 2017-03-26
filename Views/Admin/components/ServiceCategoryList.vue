<style>
    .price-category-list{
        overflow: auto;
    }
    .price-category-list .tile .drag-arrows{
        cursor: move;
    }
</style>

<template>
    <div class="price-category-list">

        <div class="price-category-container">
            <div class="card-head style-primary">
                <header><i class="fa fa-fw fa-tag"></i> Catégories</header>
                <div v-show="categories.length > 0" class="tools">
                    <a @click="updatePosition" class="btn btn-default"><i class="fa fa-save"></i> Enregistrer</a>
                </div>
            </div>
            <div class="list-results list-results-underlined">
                <ul class="list panel-group" id="price-list-sortable" data-sortable="true">
                    <li v-for="cat in categories" class="tile card panel mt10" :data-id="cat.id">
                        <div class="tile-content ink-reaction">
                            <div class="tile-text"><i class="fa drag-arrows fa-arrows mr10"></i> {{ cat.name }}</div>
                        </div>
                        <a @click="editCategory(cat)" data-toggle="modal" data-target="#editServiceCategoryModal"
                           class="btn btn-flat">
                            <i class="fa fa-pencil"></i>
                        </a>
                        <a @click="editCategory(cat)" data-toggle="modal" data-target="#deleteServiceCategoryModal"
                           class="btn btn-flat ink-reaction">
                            <i class="fa fa-trash"></i>
                        </a>
                        <a @click="selectCategory(cat)" class="btn btn-flat ink-reaction">
                            <i class="fa fa-arrow-right"></i>
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
                    position: 0
                }
            }
        },
        methods: {
            ...mapActions([
                'create', 'update', 'destroy'
            ]),
            clearCategory(){
                this.category =  {
                    name: '',
                    position: this.categories.length
                };
            },
            editCategory(category){
                this.category = category;
            },
            selectCategory(category){
                this.$emit('selectCategory', category);
            },
            updateOrCreate(){
                if (this.category.id !== undefined) {
                    this.update({
                        api: service_category_api.update + this.category.id + '/' + this.website_id,
                        value: this.category
                    }).then((response) => {
                        if(response.data.resource !== undefined){
                            let index = this.categories.findIndex((i) => i.id == this.category.id);
                            this.categories[index] = response.data.resource;
                            if(response.data.resource.id != this.category.id) {
                                this.category = response.data.resource;
                                this.$emit('reloadServices');
                            }
                        }
                    })
                } else {
                    this.create({
                        api: service_category_api.create + this.website_id,
                        value: this.category
                    }).then((response) => {
                        if (response.data.resource !== undefined){
                            this.categories.push(response.data.resource);
                        }
                    })
                }
            },
            updatePosition(){
                this.update({
                    api: service_category_api.update_position + this.website_id,
                    value: {
                        categories: this.categories
                    }
                })
            },
            deleteCategory(){
                if (this.category.id !== undefined) {
                    this.destroy({
                        api: service_category_api.destroy + this.website_id,
                        ids: [this.category.id]
                    }).then((response) => {
                        if (response.data.status == 'success'){
                            let index = this.categories.findIndex((i) => i.id == this.category.id);
                            this.categories.splice(index, 1);
                        }
                    })
                }
            }
        },
        mounted(){
            let o = this;
            $('#price-list-sortable').sortable({
                placeholder: "ui-state-highlight",
                delay: 100,
                start: function (e, ui) {
                    ui.placeholder.height(ui.item.outerHeight() - 1);
                },
                stop: function (event, ui) {
                    let new_postions = [];
                    $('#' + ui.item[0].parentNode.id + ' > li').each((index, li) => {
                        let id = $(li).attr('data-id');
                        let i = o.categories.findIndex((i) => i.id == id);
                        new_postions[i] = index;
                    });
                    new_postions.forEach((element, index) => {
                        o.categories[index].position = element;
                    })
                }
            });
        }
    }
</script>
