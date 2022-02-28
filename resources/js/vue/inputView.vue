<template>
    <div class="container">
        <form>
            <div v-if="errorStatus">
                <div v-if="serverError" v-text="serverError" class="alert alert-danger"></div>
                <div v-if="hasError('generatedHand')" class="alert alert-danger">Something went wrong. Unable to generate hand</div>
            </div>
            <div class="mb-3">
                <label for="inputPassword6" class="form-label">
                    Name
                </label>
                <input type="text" class="form-control" v-model="user_name" :class="hasError('userName')? 'is-invalid':''" />
                <div v-if="hasError('userName')" class="alert alert-danger"> {{validationErrors['userName'][0]}} </div>
            </div>

            <div class="mb-3">
                <label for="inputPassword6" class="form-label">
                    Your Cards
                </label>
                <input type="text" class="form-control"
                        v-model="userInput" :class="hasError('userHand')? 'is-invalid':''"/>
                 <div  class="form-text">Cards must include 2, 3, 4, 5, 6, 7, 8, 9, 10, J, Q, K, A and separated by space.</div>
                 <div  v-if="hasError('userHand')"  class="alert alert-danger"> {{validationErrors['userHand'][0]}}</div>
            </div>
             <button type="button"
                        class="btn btn-success btn-block"
                        @click="playHand()">
                        Play Hand
             </button>
        </form>
        <div style="min-height: 200px;">
            <score-view v-if="showScore" :scoreData="scoreData" />
        </div>
    </div>
</template>

<script>
import scoreView from "./scoreView";
export default {
    data: function () {
        return {
            userInput: "",
            user_name: "",
            cards: [2, 3, 4, 5, 6, 7, 8, 9, 10, "J", "Q", "K", "A"],
            showScore: false,
            scoreData: {},
            errorStatus:false,
            validationErrors:{},
            serverError:'',
        };
    },
    components: {
        scoreView,
    },
    methods: {
        playHand() {
            this.errorStatus = false;
            this.validationErrors={};
            this.serverError = '';
            this.userInput = this.userInput.toUpperCase();
            this.showScore = false;

            const userInputArray = this.userInput.split(" ").map((el) => el.toUpperCase());

            const generatedHand = this.generateSameOrderHand(userInputArray);

            const [userHandScore, generatedHandScore] = this.scoreHands(
                userInputArray,
                generatedHand
            );

            //show who won
            let userWon;
            userHandScore > generatedHandScore ? (userWon = true):(userWon = false);

            //save to api
            const roundToSave = {
                userName: this.user_name,
                userHand: this.userInput,
                generatedHand: generatedHand.join(""),
                userScore: userHandScore,
                generatedScore: generatedHandScore,
                userWon,
            };
            this.scoreData = {
                userName: this.user_name,
                userHand: userInputArray.join(""),
                generatedHand: generatedHand.join(""),
                userScore: userHandScore,
                generatedScore: generatedHandScore,
                userWon,
            };

            this.addRoundToDB(roundToSave)
                .then((response) => {

                    if(response.data.code == 422){
                        this.setErrors(response.data.errors);
                        return
                    }
                    this.userInput = "";
                    this.showScore = true;
                    this.$emit("reloadlist");
                })
                .catch((e) => {
                    console.log(e);
                    console.log(e.response);
                    this.errorStatus = true;
                    if(e.response.status === 422){
                        this.validationErrors = e.response.data.errors;
                    }
                    if(e.response.status === 500){
                        this.serverError = 'Could not save your hand. Please try again';
                    }
                });
        },
        generateSameOrderHand(userInputArray){// Generates half the hand as digits and half as letters. STarts here //
           const validNumbers = ["2", "3", "4", "5", "6", "7", "8", "9", "10"];
            const validLetters = ["J", "K", "Q", "A"];

            const count = userInputArray.length / 2; //create function to determin more complicated instances

            const generatedDigits = [...Array(validNumbers.length).keys()]
                .sort(() => 0.5 - Math.random())
                .slice(0, count)
                .map((index) => validNumbers[index]); //creates random numbers without duplicates
            const generatedLetters = [...Array(validLetters.length).keys()]
                .sort(() => 0.5 - Math.random())
                .slice(0, count)
                .map((index) => validLetters[index]); //creates random letters without duplicates
            return generatedDigits.concat(generatedLetters);
        },
        genrateRandomOrderHand(userInputArray){ // Generates random with same number as player. STarts here //
            const allValidInputs = ["2", "3", "4", "5", "6", "7", "8", "9", "10","J", "K", "Q", "A"]

            const count = userInputArray.length; //create function to determin more complicated instances

            const generatedHand = [...Array(allValidInputs.length).keys()]
                .sort(() => 0.5 - Math.random())
                .slice(0, count)
                .map((index) => validNumbers[index]);
                return generatedHand;

        },
        verifyInput(evt) {//function allows only allowed cards and whitespace to be entered
            let keysAllowed = ["2","3","4","5","6","7","8","9","10","J","K","Q","A"," "];
            const keyPressed = evt.key;

            if (
                !keysAllowed.includes(keyPressed.toUpperCase()) ||
                keyPressed == this.userInput.split("").pop() ||
                (this.userInput.split("").includes(keyPressed) &&
                    keyPressed != " ")
            ) {
                evt.preventDefault();
            }
        },
        scoreHands(userHand, generatedHand){
            let userScore = 0;
            let genScore = 0;
            const letters = ["J", "K", "Q", "A"];
            userHand.map((el, index) => {
                if (typeof el === "number") {
                    if (el == generatedHand[index]) {
                        //if digits are same everyone gets a point
                        userScore++;
                        genScore++;
                    } else {
                        el > generatedHand[index] ? userScore++ : genScore++;
                    }
                } else {
                    if (
                        letters.indexOf(el) ==
                        letters.indexOf(generatedHand[index])
                    ) {
                        userScore++;
                        genScore++;
                    } else {
                        letters.indexOf(el) >
                        letters.indexOf(generatedHand[index])
                            ? userScore++
                            : genScore++;
                    }
                }
            });
            return [userScore, genScore];
        },
        addRoundToDB(roundToSave) {
            return axios.post("api/round/store", roundToSave);
        },
        setErrors(errors){
            this.errorStatus = true;
            this.validationErrors = errors;
        },
        hasError(fieldName){
            return (fieldName in this.validationErrors);
        },
    },
};
</script>


