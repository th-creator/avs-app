<template>
    <div class="panel">
        <div>
            <div>{{title}}</div>
            <div class="text-[#f8538d] text-lg">{{value}}</div>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { defineProps } from 'vue';

// Define props
const props = defineProps({
  title: {
    type: String,
    required: true,
  },
  value: {
    type: String,
    required: false,
    default: 'This is a default description.',
  },
});
</script>