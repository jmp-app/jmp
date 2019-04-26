<template>
    <v-card>
        <canvas
                ref="canvas"
                style="width: 100%"
        ></canvas>
        <v-card-actions>
            <v-btn @click="startGame()" color="success" v-if="startBtn">Start</v-btn>
            <v-btn @click="stopGame()" color="error" v-if="stopBtn">Stop</v-btn>
        </v-card-actions>
    </v-card>
</template>

<script>
    export default {
        name: 'Board',
        data() {
            return {
                gameArea: Object,
                gamePiece: Object,
                startBtn: true,
                stopBtn: false
            };
        },
        methods: {
            initGameArea() {
                this.gameArea.canvas = this.$refs.canvas;
                this.gameArea.start = function () {
                    // this.canvas.width = 1920;
                    // this.canvas.height = 1080;
                    this.context = this.canvas.getContext('2d');
                };
                this.gameArea.clear = function () {
                    this.gameArea.context.clearRect(0, 0, this.gameArea.canvas.width, this.gameArea.canvas.height);
                }.bind(this);
            },
            startGame() {
                this.startBtn = false;
                this.stopBtn = true;
                this.gameArea.interval = setInterval(function () {
                    this.updateGameArea();
                }.bind(this), 20);
                window.addEventListener('keydown', this.handleKeyDown);
                window.addEventListener('keyup', this.handleKeyUp);
                this.initGamePiece(15, 30, '#a32014', 20, this.gameArea.canvas.height / 2);
            },
            stopGame() {
                this.gameArea.clear();
                clearInterval(this.gameArea.interval);
                this.startBtn = true;
                this.stopBtn = false;
                window.removeEventListener('keydown', this.handleKeyDown);
                window.removeEventListener('keyup', this.handleKeyUp);
            },
            initGamePiece(width, height, color, x, y) {
                let gamePiece = this.gamePiece;
                let gameArea = this.gameArea;
                gamePiece.width = width;
                gamePiece.height = height;
                gamePiece.speedX = 0;
                gamePiece.speedY = 0;
                gamePiece.x = x;
                gamePiece.y = y;
                gameArea.context.fillStyle = color;
                gameArea.context.fillRect(gamePiece.x, gamePiece.y, gamePiece.width, gamePiece.height);

                gamePiece.update = function () {
                    let context = gameArea.context;
                    context.fillStyle = color;
                    gameArea.context.fillRect(gamePiece.x, gamePiece.y, gamePiece.width, gamePiece.height);
                };

                gamePiece.newPos = function () {
                    if (((gamePiece.x + gamePiece.speedX) < this.gameArea.canvas.width - gamePiece.width) && (gamePiece.x + gamePiece.speedX) > 0) {
                        gamePiece.x += gamePiece.speedX;
                    }
                    if (((gamePiece.y + gamePiece.speedY) < this.gameArea.canvas.height - gamePiece.height) && (gamePiece.y + gamePiece.speedY) > 0) {
                        gamePiece.y += gamePiece.speedY;
                    }
                }.bind(this);
            },
            initImage() {
                let imageObj = new Image();
                imageObj.onload = function () {
                    // let wRatio = this.gameArea.canvas.width / imageObj.width;
                    // let hRatio = this.gameArea.canvas.height / imageObj.height;
                    // let ratio = Math.min(wRatio, hRatio);
                    this.gameArea.context.drawImage(imageObj, 0, 0, this.gameArea.canvas.width, this.gameArea.canvas.height);
                }.bind(this);
                // imageObj.src = '//placehold.it/500';
                imageObj.src = '//localhost:8080/img/logo.271da85f.png';
            },
            updateGameArea() {
                let gameArea = this.gameArea;
                let gamePiece = this.gamePiece;
                gameArea.clear();
                gamePiece.speedX = 0;
                gamePiece.speedY = 0;
                if (gameArea.keys && gameArea.keys[37]) {
                    gamePiece.speedX = -1;
                }
                if (gameArea.keys && gameArea.keys[39]) {
                    gamePiece.speedX = 1;
                }
                if (gameArea.keys && gameArea.keys[38]) {
                    gamePiece.speedY = -1;
                }
                if (gameArea.keys && gameArea.keys[40]) {
                    gamePiece.speedY = 1;
                }
                gamePiece.newPos();
                gamePiece.update();
            },
            handleKeyDown(e) {
                this.gameArea.keys = (this.gameArea.keys || []);
                this.gameArea.keys[e.keyCode] = (e.type === 'keydown');
            },
            handleKeyUp(e) {
                this.gameArea.keys[e.keyCode] = (e.type === 'keydown');
            }
        },
        mounted() {
            this.initGameArea();
            // this.initImage();
            this.gameArea.start();
        },
        beforeDestroy() {
            this.stopGame();
        }
    };
</script>

<style scoped>
    canvas {
        border: 1px solid #d3d3d3;
        background-color: #f1f1f1;
    }
</style>
