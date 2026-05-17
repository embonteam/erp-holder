<script setup>
defineProps({
    label: {
        type: String,
        required: true,
    },
    value: {
        type: String,
        required: true,
    },
    tone: {
        type: String,
        default: 'neutral',
    },
});
</script>

<template>
    <article class="erp-card">
        <p class="erp-kicker">{{ label }}</p>
        <strong class="erp-metric" :data-tone="tone">{{ value }}</strong>
    </article>
</template>
