<template>
    <li class="nav-item">
        <a :href="cartUrl" class="nav-link">
            <i class="fas fa-shopping-cart"></i> <span class="badge badge-dark">{{ count }}</span>
        </a>
    </li>
</template>

<script>
    export default {
        props: ['cartTotal', 'cartCount', 'cartUrl'],
        data() {
            return {
                total: 0,
                count: 0
            }
        },
        methods: {
            changeCartData(cart) {
                this.total = cart.total;
                this.count = cart.count;
            }
        },
        created() {
            this.total = this.cartTotal;
            this.count = this.cartCount;
        },
        mounted() {
            this.$root.$on('change-cart', this.changeCartData);
        }
    }
</script>

<style scoped>

</style>