<template>
    <div class="container mt-5">
        <div class="row" v-if="scoreData.userWon">
            <h3 class="text-center">YOU WON</h3>
        </div>
        <div class="row" v-if="!scoreData.userWon">
            <h3 class="text-center">TRY AGAIN</h3>
        </div>
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">Player</th>
                    <th scope="col">Hand</th>
                    <th scope="col">Score</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th score="row">{{ scoreData.userName }}</th>
                    <td>{{ scoreData.userHand }}</td>
                    <td>{{ scoreData.userScore }}</td>
                </tr>
                <tr>
                    <th scope="row">Computer</th>
                    <td>{{ scoreData.generatedHand }}</td>
                    <td>{{ scoreData.generatedScore }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
export default {
    props: ["scoreData"],
};
</script>

