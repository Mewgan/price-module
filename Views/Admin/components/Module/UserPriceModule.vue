<template>
    <router-link
            :to="{name: 'module:' + content.module.category.slug, params: {website_id: website}}"
            class="btn ink-reaction btn-info">Modifier
    </router-link>
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