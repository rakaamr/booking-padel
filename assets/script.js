document.addEventListener("DOMContentLoaded", function () {
  // Dashboard Card Animation
  const cards = document.querySelectorAll(".card");

  cards.forEach(function (card, index) {
    card.style.opacity = "0";
    card.style.transform = "translateY(40px)";

    setTimeout(function () {
      card.style.transition = "all .6s ease";
      card.style.opacity = "1";
      card.style.transform = "translateY(0)";
    }, index * 150);
  });

  // Hover Button
  const buttons = document.querySelectorAll(".btn-success, .booking-btn");

  buttons.forEach(function (btn) {
    btn.addEventListener("mouseenter", function () {
      btn.style.boxShadow = "0 10px 25px rgba(0,180,80,.4)";
    });

    btn.addEventListener("mouseleave", function () {
      btn.style.boxShadow = "0 8px 20px rgba(30,168,74,.3)";
    });
  });
});

document.addEventListener("DOMContentLoaded", function () {
  const authCard = document.querySelector(".auth-card");

  if (authCard) {
    authCard.style.opacity = "0";
    authCard.style.transform = "translateY(40px)";

    setTimeout(function () {
      authCard.style.transition = "all .8s ease";

      authCard.style.opacity = "1";
      authCard.style.transform = "translateY(0)";
    }, 200);
  }
});

// Register Animation

document.addEventListener("DOMContentLoaded", function () {
  const registerCard = document.querySelector(".register-card");

  if (registerCard) {
    registerCard.style.opacity = "0";
    registerCard.style.transform = "translateY(50px)";

    setTimeout(function () {
      registerCard.style.transition = "all .8s ease";

      registerCard.style.opacity = "1";

      registerCard.style.transform = "translateY(0)";
    }, 200);
  }
});

// Animasi Notifikasi
const notifCards = document.querySelectorAll(".notif-card");

notifCards.forEach(function (card, index) {
  card.style.opacity = "0";
  card.style.transform = "translateY(30px)";

  setTimeout(function () {
    card.style.transition = "all .6s ease";
    card.style.opacity = "1";
    card.style.transform = "translateY(0)";
  }, index * 150);
});