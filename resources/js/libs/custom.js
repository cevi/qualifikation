document.addEventListener("DOMContentLoaded", function () {
    var homepageSlider = new Swiper(".homepage-slider", {
        slidesPerView: 1,
        spaceBetween: 0,
        speed: 1000,
        autoplay: {
            delay: 3000,
        },

        pagination: {
            el: ".swiper-pagination",
            dynamicBullets: true,
            clickable: true,
        },
    });
});

/* =============================================
       TRIGGER COUNTER UP FUNCTION USING WAYPOINTS
   ================================================ */
const counterElem = document.getElementById("counterUp");
if (counterElem) {
    const counterWaypoint = new Waypoint({
        element: counterElem,
        handler: function () {
            vanillaCounterUp(".counter", 5);
        },
        offset: "75%",
    });
}

/* =============================================
    COUNTER UP FUNCTION
================================================ */

function vanillaCounterUp(counterTarget, counterSpeed) {
    const counters = document.querySelectorAll(counterTarget);
    const speed = counterSpeed;

    counters.forEach((counter) => {
        function updateCount() {
            const target = +counter.getAttribute("data-counter");
            const count = +counter.innerText;
            const inc = target / speed;

            if (count < target) {
                counter.innerText = Math.trunc(count + inc);
                setTimeout(updateCount, 1);
            } else {
                counter.innerText = Math.trunc(target);
            }
        }

        updateCount();
    });
}

var themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon');
var themeToggleLightIcon = document.getElementById('theme-toggle-light-icon');
// Change the icons inside the button based on previous settings
if (localStorage.getItem('color-theme') === 'dark' ||
    (!('color-theme' in localStorage) &&
        window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            
    if(!Object.is(themeToggleLightIcon, null)){
        themeToggleLightIcon.classList.remove('hidden');
        document.documentElement.classList.add('dark');
        localStorage.setItem('color-theme', 'dark');
    }
} else {
    
    if(!Object.is(themeToggleDarkIcon, null)){
        themeToggleDarkIcon.classList.remove('hidden');
    }
}

var themeToggleBtn = document.getElementById('theme-toggle');
if(!Object.is(themeToggleBtn, null)){
    themeToggleBtn.addEventListener('click', function () {

        // toggle icons inside button
        themeToggleDarkIcon.classList.toggle('hidden');
        themeToggleLightIcon.classList.toggle('hidden');

        // if set via local storage previously
        if (localStorage.getItem('color-theme')) {
            if (localStorage.getItem('color-theme') === 'light') {
                document.documentElement.classList.add('dark');
                localStorage.setItem('color-theme', 'dark');
            } else {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('color-theme', 'light');
            }

            // if NOT set via local storage previously
        } else {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('color-theme', 'light');
            } else {
                document.documentElement.classList.add('dark');
                localStorage.setItem('color-theme', 'dark');
            }
        }
    });
}
