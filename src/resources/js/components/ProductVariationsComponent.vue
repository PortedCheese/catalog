<template>
    <div class="form-group">
        <div class="custom-control custom-radio" v-for="variation in variations">
            <input type="radio"
                   :id="'customRadio' + variation.id"
                   name="customRadio"
                   v-model="chosenVariation"
                   :value="variation.id"
                   :disabled="!variation.available"
                   @change="$emit('change', chosenVariation)"
                   class="custom-control-input">
            <label class="custom-control-label"
                   :title="variation.description"
                   data-toggle="tooltip"
                   data-placement="top"
                   :for="'customRadio' + variation.id">
                <span v-if="variation.available">
                    <del v-if="variation.sale">{{ variation.sale_price }}</del> {{ variation.price }} руб.
                </span>
                <span v-else>Нет в наличии</span>
            </label>
        </div>
    </div>
</template>

<script>
    export default {
        model: {
            prop: 'chosen',
            event: 'change'
        },
        props: ['variations', 'chosen'],
        data() {
            return {
                chosenVariation: '',
            }
        },
        created() {
            this.chosenVariation = this.chosen;
        }
    }
</script>

<style scoped>

</style>