import { defineStore } from 'pinia'

export const useSliderStore = defineStore('SliderStore', {
    state: () => {
        return {
            slider: {
                title: '',
                description: '',
                image: null,
                url: null,
                order: null,
                active: false,
                remove_previous_image: false
            }
        }
    },

    actions: {
        setSlider(slider) {
            this.slider = slider
        }
    },

    getters: {}
})
