<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deployed on Liara</title>
    <style>
        @font-face {
            font-family: 'Beautiful People';
            src: url('/fonts/BeautifulPeople.ttf') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #000;
            color: #fff;
            font-family: Arial, sans-serif;
            overflow: hidden;
        }

        h1 {
            position: absolute;
            z-index: 10;
            font-size: 5rem;
            font-family: 'Beautiful People', sans-serif;
            color: #FFD700;
            text-shadow: 0 0 10px rgba(255, 215, 0, 0.5), 0 0 20px rgba(255, 215, 0, 0.3);
        }

        canvas {
            display: block;
            position: absolute;
            top: 0;
            left: 0;
        }
    </style>
</head>
<body>
    <h1>Hooray!</h1>
    <canvas id="fireworksCanvas"></canvas>

    <script>
        const canvas = document.getElementById('fireworksCanvas');
        const ctx = canvas.getContext('2d');
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;

        function random(min, max) {
            return Math.random() * (max - min) + min;
        }

        class Firework {
            constructor(x, y, targetX, targetY) {
                this.x = x;
                this.y = y;
                this.targetX = targetX;
                this.targetY = targetY;
                this.distanceToTarget = Math.sqrt((targetX - x) ** 2 + (targetY - y) ** 2);
                this.distanceTraveled = 0;
                this.coordinates = [];
                this.coordinateCount = 5;
                while (this.coordinateCount--) {
                    this.coordinates.push([this.x, this.y]);
                }
                this.angle = Math.atan2(targetY - y, targetX - x);
                this.speed = 2;
                this.acceleration = 1.05;
                this.brightness = random(50, 70);
                this.targetRadius = 1;
            }

            update(index) {
                this.coordinates.pop();
                this.coordinates.unshift([this.x, this.y]);

                if (this.targetRadius < 8) {
                    this.targetRadius += 0.3;
                } else {
                    this.targetRadius = 1;
                }

                this.speed *= this.acceleration;

                const vx = Math.cos(this.angle) * this.speed;
                const vy = Math.sin(this.angle) * this.speed;
                this.distanceTraveled = Math.sqrt((this.x + vx - this.x) ** 2 + (this.y + vy - this.y) ** 2);

                if (this.distanceTraveled >= this.distanceToTarget) {
                    createParticles(this.targetX, this.targetY);
                    fireworks.splice(index, 1);
                } else {
                    this.x += vx;
                    this.y += vy;
                }
            }

            draw() {
                ctx.beginPath();
                ctx.moveTo(this.coordinates[this.coordinates.length - 1][0], this.coordinates[this.coordinates.length - 1][1]);
                ctx.lineTo(this.x, this.y);
                ctx.strokeStyle = `hsl(${random(0, 360)}, 100%, ${this.brightness}%)`;
                ctx.stroke();

                ctx.beginPath();
                ctx.arc(this.targetX, this.targetY, this.targetRadius, 0, Math.PI * 2);
                ctx.stroke();
            }
        }

        class Particle {
            constructor(x, y) {
                this.x = x;
                this.y = y;
                this.coordinates = [];
                this.coordinateCount = 5;
                while (this.coordinateCount--) {
                    this.coordinates.push([this.x, this.y]);
                }
                this.angle = random(0, Math.PI * 2);
                this.speed = random(1, 10);
                this.friction = 0.95;
                this.gravity = 1;
                this.hue = random(0, 360);
                this.brightness = random(50, 80);
                this.alpha = 1;
                this.decay = random(0.015, 0.03);
            }

            update(index) {
                this.coordinates.pop();
                this.coordinates.unshift([this.x, this.y]);
                this.speed *= this.friction;
                this.x += Math.cos(this.angle) * this.speed;
                this.y += Math.sin(this.angle) * this.speed + this.gravity;
                this.alpha -= this.decay;

                if (this.alpha <= this.decay) {
                    particles.splice(index, 1);
                }
            }

            draw() {
                ctx.beginPath();
                ctx.moveTo(this.coordinates[this.coordinates.length - 1][0], this.coordinates[this.coordinates.length - 1][1]);
                ctx.lineTo(this.x, this.y);
                ctx.strokeStyle = `hsla(${this.hue}, 100%, ${this.brightness}%, ${this.alpha})`;
                ctx.stroke();
            }
        }

        const fireworks = [];
        const particles = [];
        const maxFireworks = 5;
        const maxParticles = 100;

        function createParticles(x, y) {
            let particleCount = maxParticles;
            while (particleCount--) {
                particles.push(new Particle(x, y));
            }
        }

        function loop() {
            requestAnimationFrame(loop);
            ctx.globalCompositeOperation = 'destination-out';
            ctx.fillStyle = 'rgba(0, 0, 0, 0.5)';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            ctx.globalCompositeOperation = 'lighter';

            let i = fireworks.length;
            while (i--) {
                fireworks[i].draw();
                fireworks[i].update(i);
            }

            let j = particles.length;
            while (j--) {
                particles[j].draw();
                particles[j].update(j);
            }

            if (fireworks.length < maxFireworks) {
                fireworks.push(new Firework(
                    canvas.width / 2,
                    canvas.height,
                    random(0, canvas.width),
                    random(0, canvas.height / 2)
                ));
            }
        }

        window.onload = loop;
    </script>
</body>
</html>
