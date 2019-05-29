<template>
    <div class="col-12">
        <form>
            <product-variations :variations="variations" v-model="chosenVariation"></product-variations>

            <div class="btn-group"
                 role="group">
                <button type="button"
                        class="btn btn-primary mt-3"
                        data-toggle="modal"
                        :disabled="chosenVariation === '' || loading"
                        data-target="#orderProduct">
                    Заказать
                </button>
            </div>
        </form>

        <div class="modal" id="orderProduct" aria-labelledby="orderProductLabel" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="orderProductLabel">
                            Заказать товар
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="orderProductForm">
                            <div class="alert" :class="{'alert-danger': error, 'alert-success': !error}" role="alert" v-if="messages.length">
                                <template v-for="message in messages">
                                    {{ message }}
                                    <br>
                                </template>
                            </div>

                            <input type="hidden" name="variation" v-model="chosenVariation">

                            <div class="form-group">
                                <label for="name">Имя</label>
                                <input type="text"
                                       id="name"
                                       name="name"
                                       :disabled="user"
                                       :value="user ? user.login : ''"
                                       class="form-control">
                            </div>

                            <div class="form-group">
                                <label for="email">E-mail</label>
                                <input type="email"
                                       id="email"
                                       name="email"
                                       :disabled="user"
                                       :value="user ? user.email : ''"
                                       class="form-control">
                            </div>

                            <div class="form-group">
                                <label for="phone">Телефон</label>
                                <input type="text"
                                       id="phone"
                                       name="phone"
                                       class="form-control">
                            </div>

                            <div class="form-group">
                                <label for="comment">Комметарий</label>
                                <textarea class="form-control" name="comment" id="comment" rows="3"></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button"
                                class="btn btn-secondary"
                                data-dismiss="modal">
                            Закрыть
                        </button>
                        <button type="button"
                                @click="sendOrder"
                                :disabled="chosenVariation === '' || loading"
                                class="btn btn-success">
                            Заказать <i class="fas fa-spinner fa-spin" v-if="loading"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import ProductVariationsComponent from "./ProductVariationsComponent";

    export default {
        components: {
            'product-variations': ProductVariationsComponent
        },
        props: ['variations', 'formAction', 'user'],
        data() {
            return {
                chosenVariation: '',
                loading: false,
                messages: [],
                error: false
            }
        },
        methods: {
            sendOrder() {
                let form = document.getElementById('orderProductForm');
                let formData = new FormData(form);
                if (this.user) {
                    formData.append('name', this.user.login);
                    formData.append('email', this.user.email);
                }
                this.loading = true;
                this.messages = [];
                this.error = false;

                axios
                    .post(this.formAction, formData, {
                        responseType: 'json'
                    })
                    .then(response => {
                        let result = response.data;
                        this.messages.push(result.message);
                        $(form).trigger('reset');
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

</style>