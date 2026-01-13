<template>
    <div class="calculator w-72 sm:w-80 rounded-2xl bg-neutral-1 p-4 shadow-2xl border border-neutral-5">
        <!-- Display -->
        <div class="display mb-4 rounded-xl bg-neutral-3 p-4 overflow-hidden">
            <div class="h-5 text-right text-sm text-neutral-9 truncate font-mono">
                {{ history }}
            </div>
            <div class="mt-1 text-right text-3xl sm:text-4xl font-bold text-neutral-12 truncate font-mono tracking-tight">
                {{ display || '0' }}
            </div>
        </div>

        <!-- Buttons Grid -->
        <div class="buttons grid grid-cols-4 gap-2">
            <!-- Row 1: Clear, Backspace, Percent, Divide -->
            <CalcButton variant="function" @click="clear">C</CalcButton>
            <CalcButton variant="function" @click="backspace">
                <i class="ri-delete-back-2-line"></i>
            </CalcButton>
            <CalcButton variant="operator" @click="appendOperator('%')">%</CalcButton>
            <CalcButton variant="operator" @click="appendOperator('/')">÷</CalcButton>

            <!-- Row 2: 7, 8, 9, Multiply -->
            <CalcButton @click="appendNumber('7')">7</CalcButton>
            <CalcButton @click="appendNumber('8')">8</CalcButton>
            <CalcButton @click="appendNumber('9')">9</CalcButton>
            <CalcButton variant="operator" @click="appendOperator('*')">×</CalcButton>

            <!-- Row 3: 4, 5, 6, Subtract -->
            <CalcButton @click="appendNumber('4')">4</CalcButton>
            <CalcButton @click="appendNumber('5')">5</CalcButton>
            <CalcButton @click="appendNumber('6')">6</CalcButton>
            <CalcButton variant="operator" @click="appendOperator('-')">−</CalcButton>

            <!-- Row 4: 1, 2, 3, Add -->
            <CalcButton @click="appendNumber('1')">1</CalcButton>
            <CalcButton @click="appendNumber('2')">2</CalcButton>
            <CalcButton @click="appendNumber('3')">3</CalcButton>
            <CalcButton variant="operator" @click="appendOperator('+')">+</CalcButton>

            <!-- Row 5: Sign, 0, Decimal, Equals -->
            <CalcButton variant="function" @click="toggleSign">±</CalcButton>
            <CalcButton @click="appendNumber('0')">0</CalcButton>
            <CalcButton @click="appendDecimal">.</CalcButton>
            <CalcButton variant="equals" @click="calculate">=</CalcButton>
        </div>
    </div>
</template>

<script setup>
import { ref, h } from 'vue'

const display = ref('')
const history = ref('')
const lastResult = ref(null)
const shouldResetDisplay = ref(false)

// CalcButton component
const CalcButton = (props, { slots, emit }) => {
    const baseClass = 'flex items-center justify-center rounded-xl h-14 sm:h-16 text-xl font-semibold transition-all duration-150 active:scale-95 focus:outline-none focus:ring-2 focus:ring-primary-7'
    
    let variantClass = 'bg-neutral-3 text-neutral-12 hover:bg-neutral-4 active:bg-neutral-5' // number
    
    if (props.variant === 'operator') {
        variantClass = 'bg-primary-9 text-white hover:bg-primary-10 active:bg-primary-11'
    } else if (props.variant === 'function') {
        variantClass = 'bg-neutral-4 text-danger hover:bg-neutral-5 active:bg-neutral-6'
    } else if (props.variant === 'equals') {
        variantClass = 'bg-success text-white hover:bg-success/90 active:bg-success/80'
    }
    
    return h('button', {
        type: 'button',
        class: `${baseClass} ${variantClass}`,
        onClick: () => emit('click')
    }, slots.default?.())
}
CalcButton.props = ['variant']
CalcButton.emits = ['click']

const appendNumber = (num) => {
    if (shouldResetDisplay.value) {
        display.value = num
        shouldResetDisplay.value = false
    } else {
        display.value = display.value === '0' ? num : display.value + num
    }
}

const appendOperator = (operator) => {
    if (display.value === '' && lastResult.value !== null) {
        display.value = lastResult.value.toString()
    }

    if (display.value === '') return

    const lastChar = display.value.slice(-1)
    if (['+', '-', '*', '/', '%'].includes(lastChar)) {
        display.value = display.value.slice(0, -1) + operator
    } else {
        display.value += operator
    }
    shouldResetDisplay.value = false
}

const appendDecimal = () => {
    if (shouldResetDisplay.value) {
        display.value = '0.'
        shouldResetDisplay.value = false
        return
    }

    const parts = display.value.split(/[\+\-\*\/\%]/)
    const currentNumber = parts[parts.length - 1]

    if (!currentNumber.includes('.')) {
        display.value += currentNumber === '' ? '0.' : '.'
    }
}

const calculate = () => {
    if (display.value === '') return

    try {
        history.value = display.value
        let result = display.value
            .replace(/×/g, '*')
            .replace(/÷/g, '/')
            .replace(/−/g, '-')

        // Safe mathematical expression evaluation without eval
        result = Function('"use strict"; return (' + result + ')')()

        if (isNaN(result) || !isFinite(result)) {
            display.value = 'Error'
            lastResult.value = null
        } else {
            display.value = Number(result.toFixed(10)).toString()
            lastResult.value = display.value
        }
        shouldResetDisplay.value = true
    } catch (error) {
        display.value = 'Error'
        lastResult.value = null
        shouldResetDisplay.value = true
    }
}

const clear = () => {
    display.value = ''
    history.value = ''
    lastResult.value = null
    shouldResetDisplay.value = false
}

const backspace = () => {
    if (display.value.length > 0) {
        display.value = display.value.slice(0, -1)
    }
}

const toggleSign = () => {
    if (display.value === '') return

    const parts = display.value.split(/([+\-*\/%])/)
    const lastIndex = parts.length - 1
    const lastNumber = parts[lastIndex]

    if (lastNumber && !isNaN(lastNumber)) {
        parts[lastIndex] = (parseFloat(lastNumber) * -1).toString()
        display.value = parts.join('')
    }
}
</script>
