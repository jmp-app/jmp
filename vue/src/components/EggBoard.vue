<template>
    <v-card flat>
        <canvas
                ref="canvas"
                style="width: 100%"
        ></canvas>
        <v-card-actions>
            <v-btn @click="startGame()" color="success" v-if="startBtn">Start</v-btn>
            <v-btn @click="resetGame()" v-if="stopBtn">Reset</v-btn>
        </v-card-actions>
    </v-card>
</template>

<script>
    export default {
        name: 'EggBoard',
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
                    this.canvas.width = this.canvas.width * 10;
                    this.canvas.height = this.canvas.height * 10;
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
                this.initGamePiece(200, 300, '#a32014', this.gameArea.canvas.width / 2, this.gameArea.canvas.height / 2);
            },
            resetGame() {
                this.stopGame();
                this.startGame();
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
                gamePiece.speed = 0;
                gamePiece.angle = 0;
                gamePiece.moveAngle = 0;
                gamePiece.x = x;
                gamePiece.y = y;
                gameArea.context.fillStyle = color;
                gameArea.context.fillRect(gamePiece.x, gamePiece.y, gamePiece.width, gamePiece.height);

                gamePiece.update = function () {
                    let context = gameArea.context;
                    context.save();
                    context.translate(gamePiece.x, gamePiece.y);
                    context.rotate(gamePiece.angle);
                    context.fillStyle = color;
                    gameArea.context.fillRect(gamePiece.width / -2, gamePiece.height / -2, gamePiece.width, gamePiece.height);
                    context.restore();
                };

                gamePiece.newPos = function () {
                    gamePiece.angle += gamePiece.moveAngle * Math.PI / 180;
                    gamePiece.x += gamePiece.speed * Math.sin(gamePiece.angle);
                    gamePiece.y -= gamePiece.speed * Math.cos(gamePiece.angle);
                };
            },
            updateGameArea() {
                let gameArea = this.gameArea;
                let gamePiece = this.gamePiece;
                let angleSteps = 2.5;
                gameArea.clear();
                gamePiece.moveAngle = 0;
                gamePiece.speed = 0;
                if (gameArea.keys && gameArea.keys[37] && gameArea.keys[38]) {
                    gamePiece.moveAngle = -angleSteps;
                }
                if (gameArea.keys && gameArea.keys[37] && gameArea.keys[40]) {
                    gamePiece.moveAngle = angleSteps;
                }
                if (gameArea.keys && gameArea.keys[39] && gameArea.keys[38]) {
                    gamePiece.moveAngle = angleSteps;
                }
                if (gameArea.keys && gameArea.keys[39] && gameArea.keys[40]) {
                    gamePiece.moveAngle = -angleSteps;
                }
                if (gameArea.keys && gameArea.keys[38]) {
                    gamePiece.speed = 20;
                }
                if (gameArea.keys && gameArea.keys[40]) {
                    gamePiece.speed = -20;
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
            this.startGame();
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
