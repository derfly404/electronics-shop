// // Create snow canvas
// const canvas = document.createElement("canvas");
// canvas.id = "snow-cvs";
// document.documentElement.prepend(canvas);

// const ctx = canvas.getContext("2d");

// let W, H, DPR;

// // Resize canvas
// function resize() {
//     DPR = window.devicePixelRatio || 1;
//     W = window.innerWidth;
//     H = window.innerHeight;

//     canvas.width = W * DPR;
//     canvas.height = H * DPR;
//     canvas.style.width = W + "px";
//     canvas.style.height = H + "px";

//     ctx.setTransform(DPR, 0, 0, DPR, 0, 0);
// }
// resize();
// window.addEventListener("resize", resize);

// /* ========= TĂNG TUYẾT TẠI ĐÂY ========= */
// const FLAKE_COUNT = 80;      // trước là 40 → tăng gấp đôi
// const FLAKE_SIZE = 1.5;      // hệ số tăng kích thước bông
// /* ======================================= */

// // Create snowflakes
// let flakes = [];

// function newFlake(init = true) {
//     const r = (Math.random() * 1.8 + 0.8) * FLAKE_SIZE;   // bông to hơn
//     const sp = Math.random() * 0.6 + 0.35;                // tốc độ vẫn nhẹ
//     const drift = (Math.random() - 0.5) * 0.7;            // lắc nhẹ

//     return {
//         x: Math.random() * W,
//         y: init ? Math.random() * H : -20,
//         r,
//         sp,
//         drift
//     };
// }

// for (let i = 0; i < FLAKE_COUNT; i++) flakes.push(newFlake());

// // Detect light/dark for snow color
// function getBgLight() {
//     const bg = getComputedStyle(document.body).backgroundColor;
//     if (!bg.includes("rgb")) return 255;
//     const c = bg.match(/\d+/g).map(Number);
//     return (c[0] + c[1] + c[2]) / 3;
// }

// // Snow animation
// function animate() {
//     ctx.clearRect(0, 0, W, H);

//     const light = getBgLight() > 170;
//     const color = "rgba(170, 220, 255, 1)";  // Light-cyan snow
//     const shadow = "rgba(210,240,255,0.6)"; // glow nhẹ
//     ctx.shadowBlur = 6;

//     flakes.forEach(f => {
//         // Draw ❄ snowflake
//         ctx.font = (f.r * 9) + "px Arial";     // kích thước ❄ theo bán kính
//         ctx.fillStyle = color;
//         ctx.shadowColor = shadow;
//         ctx.shadowBlur = 8;
//         ctx.fillText("❄", f.x, f.y);


//         // movement
//         f.y += f.sp;
//         f.x += f.drift + Math.sin(f.y * 0.02) * 0.4;

//         // recycle
//         if (f.y > H + 10) Object.assign(f, newFlake(false));
//         if (f.x > W) f.x = -10;
//         if (f.x < -10) f.x = W;
//     });

//     requestAnimationFrame(animate);
// }
// animate();

// Gift pop on click
// document.addEventListener("click", (e) => {
//     const gift = document.createElement("div");
//     gift.className = "gift-pop";
//     gift.textContent = "🎁";
//     gift.style.left = e.clientX + "px";
//     gift.style.top = e.clientY + "px";

//     document.body.appendChild(gift);
//     setTimeout(() => gift.remove(), 900);
// });


//////

/* Xmas Countdown Card JS */
/* - Auto inits any element with id="xmas-countdown-card"
   - data-target attribute (ISO datetime) can override target
   - pause sleigh animation when card not visible (IntersectionObserver)
*/

document.addEventListener("DOMContentLoaded", () => {
    const card = document.getElementById("xmas-countdown-card");
    const targetDateStr = card.getAttribute("data-target");

    const targetDate = new Date(targetDateStr).getTime();

    const dayEl = card.querySelector('[data-type="days"]');
    const hourEl = card.querySelector('[data-type="hours"]');
    const minEl = card.querySelector('[data-type="minutes"]');
    const secEl = card.querySelector('[data-type="seconds"]');

    function updateCountdown() {
        const now = Date.now();
        const diff = targetDate - now;

        if (diff <= 0) {
            dayEl.textContent = "00";
            hourEl.textContent = "00";
            minEl.textContent = "00";
            secEl.textContent = "00";
            return;
        }

        const days = Math.floor(diff / (1000 * 60 * 60 * 24));
        const hours = Math.floor((diff / (1000 * 60 * 60)) % 24);
        const minutes = Math.floor((diff / 1000 / 60) % 60);
        const seconds = Math.floor((diff / 1000) % 60);

        dayEl.textContent = days.toString().padStart(2, "0");
        hourEl.textContent = hours.toString().padStart(2, "0");
        minEl.textContent = minutes.toString().padStart(2, "0");
        secEl.textContent = seconds.toString().padStart(2, "0");
    }

    // chạy ngay lập tức và mỗi 1 giây
    updateCountdown();
    setInterval(updateCountdown, 1000);
});

// JavaScript cho đếm ngược và nút bật/tắt hoạt ảnh
document.addEventListener("DOMContentLoaded", () => {
    const toggleBtn = document.querySelector(".xcard-toggle-anim");
    const sleigh = document.querySelector(".xcard-sleigh");

    let isPlaying = true; // mặc định đang chạy

    toggleBtn.addEventListener("click", () => {
        isPlaying = !isPlaying;

        if (isPlaying) {
            sleigh.style.animationPlayState = "running";
            toggleBtn.textContent = "OK!!!";
            toggleBtn.setAttribute("aria-pressed", "true");
        } else {
            sleigh.style.animationPlayState = "paused";
            toggleBtn.textContent = "OK!!!";
            toggleBtn.setAttribute("aria-pressed", "false");
        }
    });
});