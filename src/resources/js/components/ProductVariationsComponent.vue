<template>
    <div class="form-group">
        <div class="variation-price" v-if="variationData">
            <h4>
                <span class="text-primary">{{ variationData.price }} руб.</span>
                <span v-if="variationData.sale"><del>{{ variationData.sale_price }}</del> руб. </span>
            </h4>
        </div>

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
                   :for="'customRadio' + variation.id">
                {{ variation.description }}<span class="text-danger" v-if="!variation.available"> нет в наличии</span>
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
        computed: {
            variationData() {
                let variation = false;
                for (let item in this.variations) {
                    if (this.variations[item].id === this.chosenVariation) {
                        variation = this.variations[item];
                    }
                }
                return variation;
            }
        },
        created() {
            this.chosenVariation = this.chosen;
            if (! this.chosenVariation && this.variations.length) {
                for (let item in this.variations) {
                    if (this.variations[item].available) {
                        this.chosenVariation = this.variations[0].id;
                        this.$emit('change', this.chosenVariation);
                        break;
                    }
                }
            }
        }
    }
</script>

<style scoped>

</style>