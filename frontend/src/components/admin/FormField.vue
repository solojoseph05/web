<template>
  <div class="space-y-2">
    <label class="flex items-center gap-1 text-sm font-medium text-gray-700 dark:text-gray-300">
      {{ label }}
      <span v-if="required" class="text-red-600 font-bold">*</span>
      <span
        v-if="hint"
        class="ml-auto text-xs text-gray-500 dark:text-gray-400"
        :title="hint"
      >
        ℹ️
      </span>
    </label>

    <component
      :is="inputComponent"
      :value="modelValue"
      :placeholder="placeholder"
      :rows="rows"
      :readonly="readonly"
      :disabled="disabled"
      :type="type"
      class="w-full rounded-xl border px-3 py-2 text-sm transition focus:outline-none"
      :class="[
        error
          ? 'border-red-300 bg-red-50 text-red-900 focus:border-red-500 focus:ring-2 focus:ring-red-100'
          : 'border-gray-300 bg-white text-gray-900 focus:border-red-500 focus:ring-2 focus:ring-red-100',
        readonly || disabled ? 'bg-gray-100 text-gray-600 cursor-not-allowed' : '',
        'dark:bg-gray-800 dark:text-white dark:border-gray-700 dark:focus:ring-red-500/30'
      ]"
      @input="$emit('update:modelValue', $event.target.value)"
      @blur="$emit('blur')"
    />

    <p v-if="error" class="text-xs font-medium text-red-600 dark:text-red-400">
      ✗ {{ error }}
    </p>

    <p v-if="hint && !error" class="text-xs text-gray-500 dark:text-gray-400">
      {{ hint }}
    </p>

    <p v-if="showCharCount && maxLength" class="text-xs text-gray-500 dark:text-gray-400">
      {{ modelValue?.length || 0 }} / {{ maxLength }}
    </p>
  </div>
</template>

<script setup>
import { computed } from 'vue'

defineProps({
  label: {
    type: String,
    required: true
  },
  modelValue: {
    type: [String, Number],
    default: ''
  },
  type: {
    type: String,
    default: 'text'
  },
  placeholder: {
    type: String,
    default: ''
  },
  error: {
    type: String,
    default: ''
  },
  hint: {
    type: String,
    default: ''
  },
  required: {
    type: Boolean,
    default: false
  },
  maxLength: {
    type: Number,
    default: null
  },
  showCharCount: {
    type: Boolean,
    default: false
  },
  rows: {
    type: Number,
    default: 3
  },
  readonly: {
    type: Boolean,
    default: false
  },
  disabled: {
    type: Boolean,
    default: false
  },
  textarea: {
    type: Boolean,
    default: false
  }
})

defineEmits(['update:modelValue', 'blur'])

const props = defineProps
const inputComponent = computed(() => {
  return props.textarea ? 'textarea' : 'input'
})
</script>
