<template>
    <AppDropdown :placement="placement">
        <template #trigger>
            <button
                type="button"
                :class="buttonClass"
            >
                <i class="ri-global-line"></i>
            </button>
        </template>

        <div class="min-w-[140px] py-1">
            <div class="px-3 py-2 text-xs font-semibold uppercase tracking-wide text-neutral-9">
                {{ __('Language') }}
            </div>
            <div class="space-y-0.5 px-1">
                <button
                    v-for="lang in languages"
                    :key="lang.code"
                    type="button"
                    @click="changeLocale(lang.code)"
                    class="flex w-full items-center justify-between rounded-lg px-3 py-2 text-sm transition-colors"
                    :class="lang.code === currentLocale 
                        ? 'bg-primary-4 text-primary-11 font-medium' 
                        : 'text-neutral-11 hover:bg-neutral-3 hover:text-neutral-12'"
                >
                    <span class="flex items-center gap-2">
                        <span class="flex h-5 w-5 items-center justify-center rounded text-xs" :class="getFlagClass(lang.code)">
                            {{ getFlagEmoji(lang.code) }}
                        </span>
                        {{ lang.label }}
                    </span>
                    <i v-if="lang.code === currentLocale" class="ri-check-line text-primary-9"></i>
                </button>
            </div>
        </div>
    </AppDropdown>
</template>

<script setup>
import { computed } from 'vue'
import { router, usePage } from '@inertiajs/vue3'

const __ = window.__ || ((key) => key)

const props = defineProps({
    placement: {
        type: String,
        default: 'bottom-end'
    },
    buttonClass: {
        type: String,
        default: 'btn btn-icon btn-secondary'
    },
    showLabel: {
        type: Boolean,
        default: false
    }
})

const page = usePage()

const supportedLocales = computed(() => {
    return (
        page.props.supportedLocales ??
        page.props.supported_locales ?? {
            en: 'English',
            bn: 'বাংলা'
        }
    )
})

const currentLocale = computed(() => page.props.locale ?? 'en')

const currentLabel = computed(() => {
    const label = supportedLocales.value?.[currentLocale.value]
    return label ?? currentLocale.value
})

const languages = computed(() => {
    const entries = Object.entries(supportedLocales.value ?? {})
    return entries.map(([code, label]) => ({ code, label }))
})

const getFlagEmoji = (code) => {
    const flags = {
        en: '🇺🇸',
        bn: '🇧🇩',
        es: '🇪🇸',
        fr: '🇫🇷',
        de: '🇩🇪',
        ar: '🇸🇦',
        hi: '🇮🇳',
        zh: '🇨🇳',
        ja: '🇯🇵',
        ko: '🇰🇷'
    }
    return flags[code] || '🌐'
}

const getFlagClass = (code) => {
    return code === currentLocale.value ? 'bg-primary-5' : 'bg-neutral-3'
}

const changeLocale = (locale) => {
    const url = route('lang.switch', locale)

    if (router && typeof router.visit === 'function') {
        router.visit(url, { preserveScroll: true })
        return
    }

    if (window.$inertia && typeof window.$inertia.visit === 'function') {
        window.$inertia.visit(url)
    } else {
        window.location.href = url
    }
}
</script>
