<style>
    .team-list .list-accordion .tile-text{
        display: inline-block;
        width: 95%;
    }
    .team-list .list-accordion .tile-content{
        display: inline-block;
        padding: 16px;
        vertical-align: top;
        width: 5%;
    }
</style>

<template>
    <div class="team-list">

        <div class="team-list-container">
            <div class="card-head style-primary">
                <header>Liste des tarifs pour la catégorie : {{ category.name }}</header>
                <div v-show="category.id != null" class="tools">
                    <a @click="save" class="btn btn-default"><i class="fa fa-save"></i> Enregistrer</a>
                </div>
            </div>

            <div v-show="category.id != null" class="card-body no-padding">
                <!-- BEGIN RESULT LIST -->
                <div class="list-results list-results-underlined">
                    <ul class="list list-accordion panel-group" id="service-accordion" data-sortable="true">
                        <service-item v-for="(service, key) in services" :key="service.id"
                                      v-show="service.category.id == category.id" :id="service.id"
                                      :service="service" @serviceDeleted="deleteService" :website_id="website_id"></service-item>
                    </ul>
                    <em class="text-caption pull-left mt10">(*) Champs obligatoires</em>
                    <button data-toggle="modal" @click="addService"
                            class="btn ink-reaction btn-raised btn-lg btn-info pull-right">
                        <i class="fa fa-plus" aria-hidden="true"></i>
                        Ajouter un tarif
                    </button>
                </div><!--end .list-results -->
                <!-- END RESULTS LIST -->
            </div>

            <div v-show="category.id == null" class="mt10">
                <div class="alert alert-info" role="alert">
                    <strong><i class="fa fa-info-circle"></i> Choisir une catégorie pour lister ces tarifs</strong><br/>
                </div>
            </div>
        </div><!--end .section-body -->

    </div>

</template>


<script type="text/babel">

    import {service_api} from '../api'
    import {mapActions} from 'vuex'

    export default
    {
        name: 'service-list',
        components: {
            ServiceItem: resolve => {
                require(['./ServiceItem.vue'], resolve)
            }
        },
        props: {
            website_id: {
                required: true
            },
            category: {
                default: () => {
                    return {
                        id: null
                    }
                }
            }
        },
        data(){
            return {
                services: []
            }
        },
        methods: {
            ...mapActions(['read', 'update']),
            addService(){
                this.services.push({
                    id: 'create-' + this.services.length,
                    title: 'Service ' + this.services.length,
                    price: '',
                    description: '',
                    position: this.services.length,
                    category: this.category
                });
            },
            deleteService(id){
                let index = this.services.findIndex((i) => i.id == id);
                this.services.splice(index, 1);
            },
            save(){
                this.update({
                    api: service_api.update_or_create + this.website_id,
                    value: {
                        services: this.services
                    }
                }).then((response) => {
                    if (response.data.resource !== undefined) {
                        this.services = response.data.resource;
                    }
                });
            }
        },
        created () {
            this.read({api: service_api.all + this.website_id}).then((response) => {
                if(response.data.resource != undefined)
                    this.services = response.data.resource;
            })
        },
        mounted () {
            let o = this;
            $('#service-accordion').sortable({
                placeholder: "ui-state-highlight",
                delay: 100,
                start: function (e, ui) {
                    ui.placeholder.height(ui.item.outerHeight() - 1);
                },
                stop: function (event, ui) {
                    let new_postions = [];
                    $('#' + ui.item[0].parentNode.id + ' > li').each((index, li) => {
                        let id = $(li).attr('data-id');
                        let i = o.services.findIndex((i) => i.id == id);
                        new_postions[i] = index;
                    });
                    new_postions.forEach((element, index) => {
                        o.services[index].position = element;
                    })
                }
            });
        }
    }
</script>
