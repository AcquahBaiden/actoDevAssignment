<template>
    <div class="main">
        <div class="game">
            <h1 class="text-center">Assignment</h1>
            <p>
                Enter your name and a hand of cards then click Play <br />
                Hands must be digits first then letters and must be the same number <br /> <!--Confirm with ACTO-->
            </p>
            <input-view v-on:reloadlist="getRounds()" />
            <h2 class="text-center">Leaderboard</h2>
            <board-view :boardList="boardList" />
            <div v-if="apiError" class="alert alert-danger" v-text="apiError"></div>
        </div>
    </div>
</template>

<script>
import boardView from "./boardView";
import inputView from "./inputView";

export default {
    data: function () {
        return {
            boardList: [],
            apiError: false,
        };
    },
    components: {
        boardView,
        inputView,
    },
    methods: {
        getRounds() {
            axios
                .get("api/rounds")
                .then((response) => {
                    this.boardList = this.createLeaderboard(response.data);
                })
                .catch((e) => {
                    if(e.response.data.status = 404){
                        this.apiError = 'Page not found. Unable to retrieve leaderboard';
                    }else{
                        this.apiError = 'Something went wrong. Unable to retrieve leaderboard'; //showing error as is
                    }
                });
        },
        createLeaderboard(rounds) {
            let board = [];
            let userNames = new Set(rounds.map((round) => round['userName'].toUpperCase())); //array containing all userNames without duplicates
            userNames.forEach((userName) =>
                board.push({ userName, totalWins: 0, totalPlayed: 0 })
            ); //creating objects for each userName
            rounds.forEach((round) => {
                for (let i = 0; i < board.length; i++) {
                    const roundName = round.userName;
                    if (board[i].userName == roundName.toUpperCase()) {
                        if (round.userWon == true)
                            board[i].totalWins = board[i].totalWins + 1;
                        board[i].totalPlayed = board[i].totalPlayed + 1;
                    }
                }
            });
            board.sort((a, b) => b.totalWins - a.totalWins);//sorting by totalWins descending
            return board;
        },
    },
    created() {//life-cycle event to initate data retrieval
        this.getRounds();
    },
};
</script>

<style scoped>
.main {
    display: flex;
    justify-content: center;
    flex-direction: column;
    align-items: center;
    margin-top: 50px;
}
.game {
    background-color: #f1f1f180;
    padding: 30px 20px;
}
</style>
