<template>
    <form class="variationQuantityForm">
        <div class="input-group input-group mb-2 mr-2">
            <div class="input-group-prepend">
                <button class="btn btn-outline-secondary px-1"
                        type="button"
                        :disabled="quantity <= 1 || loading"
                        @click="changeQuantity(true)">
                    <i class="fas fa-minus"></i>
                </button>
            </div>
            <input type="number"
                   class="form-control"
                   :disabled="loading"
                   @change.self="sendQuantity"
                   v-model.lazy="quantity" min="1">
            <div class="input-group-append">
                <button class="btn btn-outline-secondary px-1"
                        type="button"
                        :disabled="loading"
                        @click="changeQuantity(false)">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </div>
        <div class="alert" :class="{'alert-danger': error, 'alert-success': !error}" role="alert" v-if="messages.length && error">
            <template v-for="message in messages">
                {{ message }}
                <br>
            </template>
        </div>
    </form>
</template>

<script>
    export default {
        props: ['itemQuantity', 'putUrl', 'variationId'],
        data() {
            return {
                quantity: 0,
                loading: false,
                messages: [],
                error: false
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
                this.sendQuantity();
            },
            sendQuantity() {
                this.loading = true;
                this.messages = [];
                this.error = false;

                axios
                    .put(this.putUrl, {
                        quantity: this.quantity
                    }, {
                        responseType: 'json'
                    })
                    .then(response => {
                        let result = response.data;
                        this.messages.push(result.message);
                        this.$root.$emit('change-cart', result.cart);
                        let $tr = $("#variation-" + this.variationId);
                        $tr.find('.variation-price').html(result.itemPrice);
                        $tr.find('.variation-total').html(result.itemTotal);
                        $("#cart-total-side").html(result.cart.total);
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
        },
        created() {
            this.quantity = this.itemQuantity;
        }
    }
</script>

<style scoped>
    .variationQuantityForm {
        /*width: 120px;*/
    }
    .variationQuantityForm input.form-control {
        text-align: center;
    }
</style>