<template>
    <div class="container">
        <form @submit.prevent="submitUrl">
            <label for="url">Enter URL:</label>
            <input v-model="url" type="url" required>
            <button type="submit">Shorten URL</button>
        </form>

        <p v-if="shortUrl">Your short URL: <a :href="shortUrl" target="_blank">{{ shortUrl }}</a></p>
        <p v-if="error" class="alert alert-danger">{{ error }}</p>
    </div>
</template>

<script>
export default {
    data() {
        return {
            url: '',
            shortUrl: null,
            error: null,
        };
    },
    methods: {
        submitUrl() {
            axios.post('/store', { url: this.url })
                .then(response => {
                    this.shortUrl = response.data.shortUrl;
                    this.error = response.data.error;
                })
                .catch(error => {
                    console.error(error);
                });
        },
    },
};
</script>
