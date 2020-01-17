<template>
    <draggable class="dragArea list-unstyled"
               tag="ul"
               :list="items"
               @change="checkMove"
               :group="{ name: 'g1' }" handle=".category-handle">
        <li v-for="element in items" :key="element.id" class="dragArea__item">
            <i class="fa fa-align-justify handle cursor-move category-handle"></i>
            <a :href="element.url">{{ element.title }}</a>
            <span class="badge badge-primary" v-if="element.children.length">{{ element.children.length }}</span>
            <nested-draggable :items="element.children" v-on:changed="checkMove" />
        </li>
    </draggable>
</template>
<script>
    import draggable from 'vuedraggable'
    export default {
        props: {
            items: {
                required: true,
                type: Array
            }
        },
        components: {
            draggable
        },
        name: "nested-draggable",
        methods: {
            checkMove() {
                this.$emit('changed');
            }
        }
    };
</script>
<style scoped>
    .dragArea {
        min-height: 1.25rem;
        padding-top: .75rem;
    }
    .dragArea__item {
        border: 1px solid rgba(0,0,0,.125);
        background-color: #fff;
        padding: .75rem 1.25rem 0;
        margin-bottom: -1px;
    }
    .dragArea__item:first-child {
        border-top-left-radius: .25rem;
        border-top-right-radius: .25rem;
    }
    .dragArea__item:last-child {
        border-bottom-left-radius: .25rem;
        border-bottom-right-radius: .25rem;
        margin-bottom: .75rem;
    }
</style>