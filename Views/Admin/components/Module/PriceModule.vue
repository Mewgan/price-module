<style>
    .module-title{
        padding: 10px;
        background: #f2f2f2;
    }
    .content {
        overflow: visible;
        position: relative;
        width: auto;
        margin-left: 0;
        padding: inherit;
    }

</style>

<template>
    <div class="edit-price">
        <form class="form">
            <div>
                <h5 class="module-title">Information :</h5>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="text" class="form-control" v-model="content.name" :id="'content-name-' + line">
                            <label :for="'content-name-' + line">Nom *</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="text" class="form-control" v-model="content.block"
                                   :id="'content-block-' + line">
                            <label :for="'content-block-' + line">Bloc *</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="text" class="form-control" :value="content.module.category.title" readonly
                                   :id="'content-module-' + line">
                            <label :for="'content-module-' + line">Module</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <input type="text" class="form-control" :value="content.module.name" readonly
                                   :id="'content-extension-' + line">
                            <label :for="'content-extension-' + line">Extension</label>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <input type="text" class="form-control" v-model="content_data.class" :id="'content-class-' + line">
                    <label :for="'content-class-' + line">Class</label>
                </div>
            </div>
            <h5 class="module-title">Choix du template :</h5>
            <template-editor @updateTemplate="updateTemplate" :id="line" :templates="templates" :template="content.template"
                             label="Template du contenu"></template-editor>
            <h5 class="module-title">Configuration avancé :</h5>
            <div class="row">
                <div class="col-md-4 center-align">
                    <div class="form-group">
                        <p>Lister les services dans catégorie</p>
                        <div class="switch">
                            <label>
                                Non
                                <input v-model="content_data.service_in_category" type="checkbox">
                                <span class="lever"></span>
                                Oui
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 center-align">
                    <div class="form-group">
                        <p>Lister les services</p>
                        <div class="switch">
                            <label>
                                Non
                                <input v-model="content_data.service" type="checkbox">
                                <span class="lever"></span>
                                Oui
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 center-align">
                    <div class="form-group">
                        <p>Lister les catégories</p>
                        <div class="switch">
                            <label>
                                Non
                                <input v-model="content_data.category" type="checkbox">
                                <span class="lever"></span>
                                Oui
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <select2 v-if="categories.length > 0" @updateValue="updateCategories"
                     :contents="categories" :id="'categories-' + line" val_index="id" index="name"
                     label="Choisir les catégories à afficher ou laisser vide pour tout afficher"
                     :val="content_data.categories"></select2>
        </form>

        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
            <button type="button" @click="updateContent" class="btn btn-primary" data-dismiss="modal">Enregistrer
            </button>
        </div>

    </div>
</template>

<script type="text/babel">

    import {mapActions} from 'vuex'
    import {template_api} from '@front/api'
    import {service_category_api} from '../../api'

    import module_mixin from '@front/mixin/module'

    export default{
        name: 'price',
        components: {
            TemplateEditor: resolve => {
                require(['@front/components/Helper/TemplateEditor.vue'], resolve)
            },
            Select2: resolve => {
                require(['@front/components/Helper/Select2.vue'], resolve)
            }
        },
        mixins: [module_mixin],
        props: {
            line: {
                default: 'default'
            },
            content: {
                type: Object,
                required: true
            },
            page: {
                default: null
            },
            website: {
                required: true
            }
        },
        data(){
            return {
                website_id: this.$route.params.website_id,
                templates: [],
                categories: [],
                content_data: {
                    class: '',
                    service: true,
                    category: true,
                    service_in_category: false,
                    categories: []
                }
            }
        },
        watch: {
            'content_data': {
                handler(){
                    this.$set(this.content, 'data', this.content_data);
                },
                deep: true
            }
        },
        methods: {
            ...mapActions(['read', 'setResponse']),
            updateCategories(val){
                this.content.data.categories = val;
            },
            updateTemplate(template){
                if (this.content.template !== undefined) this.content.template = template;
            }
        },
        created () {
            this.read({api: template_api.get_website_content_layouts + this.website}).then((response) => {
                this.templates = response.data;
            });
            this.read({api: service_category_api.all + this.website}).then((response) => {
                if (response.data.resource !== undefined)
                    this.categories = response.data.resource;
            });
        },
        mounted(){
            if (this.content.data.categories !== undefined && this.content.data.categories instanceof Array)
                this.content_data = Object.assign({}, this.content_data, this.content.data);
        }
    }
</script>