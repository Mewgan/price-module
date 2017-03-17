<template>
    <div>
        <a data-toggle="modal" :data-target="'#editGirdEditorContentModal' + line"
           class="btn ink-reaction btn-info">Modifier</a>
        <div class="modal fade content-editor"
             :id="'editGirdEditorContentModal' + line" tabindex="-1" role="dialog"
             aria-labelledby="simpleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-xlg content-modal">
                <div class="modal-content">
                    <div class="modal-header style-primary">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×
                        </button>
                        <h4 class="modal-title" id="simpleModalLabel">Contenu</h4>
                    </div>
                    <div class="modal-body content-body">
                    </div>
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div>
    </div>
</template>

<script type="text/babel">
    import {mapGetters} from 'vuex'

    export default{
        name: 'user-price',
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
        }
    }
</script>