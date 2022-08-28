//Get the button:
mybutton = document.getElementById("myBtn");

// When the user scrolls down 20px from the top of the document, show the button
window.onscroll = function() {scrollFunction()};

function scrollFunction() {
  if(mybutton!=null){
    if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
      mybutton.style.display = "block";
    } else {
      mybutton.style.display = "none";
    }
  }
}

// When the user clicks on the button, scroll to the top of the document
function topFunction() {
  document.body.scrollTop = 0; // For Safari
  document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
}

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
