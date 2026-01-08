import { defineStore } from 'pinia'

export const useTestimonialStore = defineStore('TestimonialStore', {
    state: () => {
        return {
            testimonial: {
                name: '',
                designation: '',
                company: '',
                title: '',
                description: '',
                image: null,
                active: true,
                featured: false,
                remove_previous_image: false
            }
        }
    },

    actions: {
        setTestimonial(testimonial) {
            this.testimonial = testimonial
        }
    },

    getters: {}
})
