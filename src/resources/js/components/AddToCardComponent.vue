<template>
    <div class="col-12">
        <form>
            <product-variations :variations="variations" v-model="chosenVariation"></product-variations>

            <div class="form-group">
                <div class="btn-toolbar mb-3" role="toolbar" aria-label="Toolbar with button groups">
                    <div class="input-group input-group-sm">
                        <div class="input-group-prepend">
                            <button class="btn btn-outline-secondary"
                                    type="button"
                                    :disabled="quantity <= 1"
                                    @click="changeQuantity(true)">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                        <input type="number" class="form-control" id="variationQuantity" v-model="quantity" min="1">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary"
                                    type="button"
                                    @click="changeQuantity(false)">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>

                    <div class="btn-group btn-group-sm ml-2"
                         role="group">
                        <button type="button"
                                class="btn btn-primary"
                                @click="addToCart"
                                :disabled="chosenVariation === '' || loading">
                            Добавить в корзину <i class="fas fa-spinner fa-spin" v-if="loading"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="alert" :class="{'alert-danger': error, 'alert-success': !error}" role="alert" v-if="messages.length">
                <template v-for="message in messages">
                    {{ message }}
                    <br>
                </template>
            </div>
        </form>
    </div>
</template>

<script>
    import ProductVariationsComponent from "./ProductVariationsComponent";

    export default {
        components: {
            'product-variations': ProductVariationsComponent
        },
        props: ['variations', 'formAction'],
        data() {
            return {
                chosenVariation: '',
                loading: false,
                messages: [],
                error: false,
                quantity: 1
            }
        },
        methods: {
            changeQuantity(decrease) {
                if (!decrease) {
                    this.quantity++;
                }
                else if (this.quantity > 1) {
                    this.quantity--;
                }
            },
            addToCart() {
                this.loading = true;
                this.messages = [];
                this.error = false;

                axios
                    .put(this.formAction, {
                        variation: this.chosenVariation,
                        quantity: this.quantity
                    }, {
                        responseType: 'json'
                    })
                    .then(response => {
                        let result = response.data;
                        this.messages.push(result.message);
                        this.$root.$emit('change-cart', result.cart);
                    })
                    .catch(error => {
                        this.error = true;
                        let data = error.response.data;
                        for (error in data.errors) {
                            this.messages.push(data.errors[error][0]);
                        }
                    })
                    .finally(() => {
                        this.loading = false;
                    });
            }
        }
    }
</script>

<style scoped>
    #variationQuantity {
        width: 60px;
        text-align: center;
    }
</style>