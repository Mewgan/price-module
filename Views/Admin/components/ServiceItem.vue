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
    .service-item header span{
        margin-left: 10px;
    }
</style>

<template>
    <li class="service-item tile card panel" :data-id="service.id">
        <div class="list-header">
            <div class="card-head col-md-11 collapsed" data-toggle="collapse" :data-parent="accordion_parent" :data-target="'#accordion-' + id">
                <header>
                    <i class="fa drag-arrows fa-arrows"></i>
                    <span> {{service.title}}</span>
                </header>
                <div class="tools">
                    <a class="btn btn-info"><i class="fa fa-pencil"></i></a>
                </div>
            </div>
            <div class="delete-container col-md-1">
                <a @click="deleteService" class="btn btn-danger"><i class="fa fa-trash"></i></a>
            </div>
        </div>
        <div :id="'accordion-' + id" class="accordion collapse">
            <div class="col-md-12">
                <form class="form">
                    <table class="table table-banded no-margin">
                        <tbody>
                        <tr>
                            <td class="col-md-3">
                                <h4>Nom du service *</h4>
                            </td>
                            <td class="col-md-9 field-value">
                                <div class="form-group">
                                    <input type="text" class="form-control" id="service-title" v-model="service.title">
                                    <label for="service-title">Titre</label>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="col-md-3">
                                <h4>Prix</h4>
                            </td>
                            <td class="col-md-9 field-value">
                                <div class="form-group">
                                    <input type="text" class="form-control" id="service-price" v-model="service.price">
                                    <label for="service-price">Prix</label>
                                </div>
                            </td>
                        </tr>
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
                        category: {},
                    }
                }
            }
        },
        methods: {
            ...mapActions(['destroy']),
            updateContent(val) {
                this.service.description = val;
            },
            deleteService(){
                if(this.service.id !== undefined){
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
