import { defineStore } from 'pinia'
import slug from '@resources/js/Utils/slug.js'

export const usePageStore = defineStore('PageStore', {
    state: () => {
        return {
            page: {
                title: '',
                content: '',
                image: null,

                meta_tag_title: '',
                meta_tag_description: '',

                published_at: '',

                remove_previous_image: false,

                tagsHasChanged: false
            }
        }
    },

    actions: {
        setPage(page) {
            this.page = page
        },
        initSeoTags() {
            this.page.meta_tag_title = this.page.title.substring(0, 60)
            this.page.meta_tag_description = this.page.content.replace(
                /<\/?[^>]+(>|$)/g,
                ''
            )
        }
    },

    getters: {
        getRemainingChars: (state) => {
            return (key, max) => {
                if (!state.page[key]) return max

                return max - state.page[key].length
            }
        },

        showSeoAlert: (state) => {
            return () => {
                if (
                    state.page.meta_tag_title &&
                    state.page.meta_tag_title.length
                ) {
                    return false
                }

                if (
                    state.page.title.length > 1 &&
                    state.page.content.length > 2
                ) {
                    return false
                }

                return true
            }
        },

        getSlug: (state) => {
            return () => {
                if (!state.page.title) return ''

                return slug(state.page.title)
            }
        }
    }
})
