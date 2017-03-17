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
            <div v-if="auth.status.level < 4">
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
            <div>
                <div :id="'grid-editor-' + line"></div>
            </div>
        </form>

        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Fermer</button>
            <button type="button" @click="updateContent" class="btn btn-primary" data-dismiss="modal">Enregistrer
            </button>
        </div>

    </div>
</template>

<script type="text/babel">

    import '@modules/GridEditor/Resources/public/css/grideditor/grideditor.css'
    import '@modules/GridEditor/Resources/public/css/grideditor/grideditor-font-awesome.css'

    import '@modules/GridEditor/Resources/public/js/jquery-ui.min'
    import '@modules/GridEditor/Resources/public/js/tinymce/jquery.tinymce.min'
    import '@modules/GridEditor/Resources/public/js/grideditor/jquery.grideditor'

    import Media from '@front/components/Helper/Media.vue'
    import Colorpicker from '@front/components/Helper/Colorpicker.vue'
    import {mapGetters, mapActions} from 'vuex'

    export default{
        name: 'price',
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
                content_data: {
                    class: '',
                    content: ''
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
        computed: {
            ...mapGetters(['auth', 'system'])
        },
        methods: {
            ...mapActions(['read']),
        },
        mounted(){
            this.$nextTick(function () {
                let o = this;
                if (this.content.data.content !== undefined)this.content_data = this.content.data;
                this.loadEditor();
                $('#mediaLibrarygrid-editor-media-' + o.line).on('show.bs.modal', () => {
                    $('.mce-panel.mce-window').hide();
                });

                $('#mediaLibrarygrid-editor-media-' + o.line).on('hide.bs.modal', () => {
                    $('.mce-panel.mce-window').show();
                });
            })
        }
    }
</script>