<template>
    <div class="container">
        <div class="row" v-for="(user, index) in boardList" :key="index">
            <div class="col-1">{{ index + 1 }}</div>
            <div class="col-7">{{ user.userName }}</div>
            <div class="col-4">{{ user.totalWins }}</div>
        </div>
    </div>
</template>

<script>
export default {
    props: ["boardList"],
};
</script>

<style scoped></style>
