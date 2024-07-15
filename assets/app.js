// import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');



//--------------------------------------------SWIPER SCRIPT--------------------------------------------

const swiper = new Swiper('.swiper', {
    // Optional parameters
    direction: 'horizontal',
    loop: true,
  
    // If we need pagination
    pagination: {
      el: '.swiper-pagination',
    },
  
    // Navigation arrows
    navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev',
    },
  
    // And if we need scrollbar
    scrollbar: {
      el: '.swiper-scrollbar',
    },
  });

//--------------------------------------------BURGER MENU SCRIPT--------------------------------------------

const menu = document.querySelector("#menu");
const menuItems = document.querySelectorAll(".menuItem");
const hamburger= document.querySelector("#burger");
const closeIcon= document.querySelector("#closeIcon");
const menuIcon = document.querySelector("#menuIcon");

function toggleMenu() {
    if (menu.classList.contains("hideMenu")) {
        menu.classList.remove("hideMenu");
        closeIcon.style.display = "block";
        menuIcon.style.display = "none";
    } else {
        menu.classList.add("hideMenu");
        closeIcon.style.display = "none";
        menuIcon.style.display = "block";
    }
}

hamburger.addEventListener('click', toggleMenu);

//--------------------------------------------SCORE STARS SCRIPT--------------------------------------------

const productScore = document.querySelector("#globalRating");
const productStars = document.querySelector(".productStars");
const pStars = productStars.querySelectorAll(".fa-star");

for (let i = 0; i < productScore.innerHTML; i++) {
    pStars[i].classList.add("star-active");
}

const reviews = document.querySelectorAll(".review")

reviews.forEach(review => {
    let reviewScore = review.querySelector(".reviewRating");
    let reviewStars = review.querySelector(".reviewStars");
    let rStars = reviewStars.querySelectorAll(".fa-star");
    
    for (let i = 0; i < reviewScore.innerHTML; i++) {
        rStars[i].classList.add("star-active");
    }
      
});

//----------------------BUILDER COMPATIBILITY SCRIPT-----------------------------------

document.addEventListener('DOMContentLoaded', function () {
  const cpuField = document.getElementById('build_cpu');
  const motherboardField = document.getElementById('build_motherboard');

  cpuField.addEventListener('change', function () {
      const cpuId = this.value;

      fetch(`/get-motherboards?cpuId=${cpuId}`)
          .then(response => response.json())
          .then(data => {
            motherboardField.innerHTML = '';

            data.forEach(motherboard => {
                const option = document.createElement('option');
                option.value = motherboard.id;
                option.textContent = motherboard.label;
                motherboardField.appendChild(option);
              });
          });
  });
});

//----------------------SEARCHBAR SCRIPT----------------------

// document.addEventListener('DOMContentLoaded', function() {
//     const searchField = document.getElementById("searchbar");
//     const resultsList = document.getElementById("suggestionList");
//     const searchUrl = `search_products`;

//     function handleSearch() {
//         const query = searchField.value;
//         console.log("Searching for : ", query);
//         console.log(`${searchUrl}?searchQuery=${encodeURIComponent(query)}`)

//         if (query.length > 2) {
//             fetch(`${searchUrl}?searchQuery=${encodeURIComponent(query)}`)
//                 .then(response => response.json())
//                 .then(data => {
//                     resultsList.innerHTML = '';
//                     data.forEach(product => {
//                         const result = document.createElement('li');
//                         result.textContent = product.label; // Ensure this matches your Product entity field
//                         resultsList.appendChild(result);
//                     });
//                 })
//                 .catch(error => console.error('Error:', error));
//         } else {
//             resultsList.innerHTML = '';
//         }
//     }

//     searchField.addEventListener('input', handleSearch);
// });