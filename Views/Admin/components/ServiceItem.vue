<style>
    .service-item{
        margin: 10px 0 !important;
    }
    .service-item .list-header .card-head{
        display: inline-block;
    }
    .service-item .list-header .card-head .tools{
        padding: 5px 24px;
    }
    .service-item .list-header .delete-container{
        padding-top: 14px;
    }
    .service-item .drag-arrows{
        margin-right: 15px;
        cursor: move;
    }
    .service-item .edit-service-title, .service-item .edit-price-title{
        display: inline-block;
        width: inherit;
    }
</style>

<template>
    <li class="service-item tile card panel" :data-id="service.id">
        <div class="list-header">
            <div class="card-head col-md-10 collapsed">
                <header class="pl0 pr0">
                    <i title="Glisser et déposer pour changer l'ordre d'affichage" class="fa drag-arrows fa-arrows mr10"></i>
                    <span class="text-primary">Titre * : </span>
                    <input type="text" class="form-control edit-service-title" v-model="service.title">
                    <span class="text-primary">Prix * : </span>
                    <input type="text" class="form-control edit-price-title" v-model="service.price">
                </header>
            </div>
            <div class="delete-container col-md-2">
                <a @click="openAccordion" title="Ajouter une description au service" class="btn btn-default edit-service collapsed" data-toggle="collapse" :data-parent="accordion_parent" :data-target="'#accordion-' + id"><i class="fa fa-angle-down"></i></a>
                <a data-toggle="modal" title="Supprimer le service" :data-target="'#deleteServiceModal' + id" class="btn btn-default"><i class="fa fa-trash"></i></a>
            </div>
        </div>

        <div :id="'accordion-' + id" class="accordion collapse">
            <div v-if="accordion" class="col-md-12">
                <form class="form">
                    <table class="table table-banded no-margin">
                        <tbody>
                        <tr>
                            <td class="col-md-3">
                                <h4>Description</h4>
                            </td>
                            <td class="col-md-9 field-value">
                                <div class="from-group">
                                    <tinymce-editor @updateContent="updateContent" :height="200"
                                                    :id="'service-description-' + id"
                                                    :value="service.description"></tinymce-editor>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>

        <div class="modal fade" :id="'deleteServiceModal' + id" tabindex="-1" role="dialog"
             aria-labelledby="simpleModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" :id="'deleteServiceModalLabel' + id">Suppression</h4>
                    </div>
                    <div class="modal-body">
                        <p>Êtes-vous sûr de vouloir supprimer ce tarif ?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="modal-btn btn btn-default" data-dismiss="modal">Non</button>
                        <button type="button" class="modal-btn btn btn-primary" data-dismiss="modal" @click="deleteService">
                            Oui
                        </button>
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div>
    </li>
</template>

<script type="text/babel">

    import {service_api} from '../api'
    import {mapActions} from 'vuex'

    export default{
        name: 'service-item',
        components: {
            TinymceEditor: resolve => { require(['@front/components/Helper/TinymceEditor.vue'], resolve) },
        },
        props: {
            accordion_parent: {
                default: '#service-accordion'
            },
            id: {
                required: true
            },
            website_id: {
                required: true
            },
            service: {
                required: true,
                default: () => {
                    return {
                        id: 'default',
                        title: '',
                        price: '',
                        description: '',
                        position: 0,
                        category: {}
                    }
                }
            }
        },
        data(){
            return {
                accordion : false
            }
        },
        methods: {
            ...mapActions(['destroy']),
            openAccordion(){
                this.accordion = true;
            },
            updateContent(val) {
                this.service.description = val;
            },
            deleteService(){
                if(this.service.id !== undefined && (typeof this.service.id === 'number' || (typeof this.service.id === 'string' && this.service.id.substring(0,6) !== 'create'))){
                    this.destroy({
                        api: service_api.destroy + this.website_id,
                        ids: [this.service.id]
                    }).then((response) => {
                        if (response.data.status == 'success')
                            this.$emit('serviceDeleted', this.service.id);
                    });
                }else{
                    this.$emit('serviceDeleted', this.service.id);
                }
            }
        }
    }
</script>
