// import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');



//---------------------------------------------------------SWIPER SCRIPT-----------------------------------------------------------
document.addEventListener('DOMContentLoaded', function() {
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
});

//------------------------------------------------------BURGER MENU SCRIPT--------------------------------------------------------
document.addEventListener('DOMContentLoaded', function() {
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
});

//-------------------------------------------------------SCORE STARS SCRIPT-------------------------------------------------------
document.addEventListener('DOMContentLoaded', function () {
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
});

//---------------------------------------------------BUILDER COMPATIBILITY SCRIPT---------------------------------------------------
document.addEventListener('DOMContentLoaded', function () {
    const cpuField = document.getElementById('build_cpu');
    const motherboardField = document.getElementById('build_motherboard');

  // Handles motherboard compatibility with selected CPU
    cpuField.addEventListener('change', function() {
        console.log("bonjour");
        const cpuId = this.value;

        fetch(`/get-motherboards?cpuId=${cpuId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if(data.length > 0){
                    console.log("data recieved !")
                    motherboardField.innerHTML = '';
                    data.forEach(motherboard => {
                        const option = document.createElement('option');
                        option.value = motherboard.id;
                        option.textContent = motherboard.label;
                        motherboardField.appendChild(option);
                    });
                }
                else {
                    console.log("no data recieved, field unchanged")
                }
            })
            .catch(error => console.error('Fetch error:', error));
  });

  // Handles CPU compatibility with selected motherboard
  motherboardField.addEventListener('change', function() {
        console.log("bonsoir");
        const moboId = this.value;

        fetch(`/get-cpus?moboId=${moboId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.length > 0) {
                    console.log("data received!", data);
                    cpuField.innerHTML = '';
                    data.forEach(cpu => {
                        const option = document.createElement('option');
                        option.value = cpu.id;
                        option.textContent = cpu.label;
                        cpuField.appendChild(option);
                    });
                } else {
                    console.log("No data received, field unchanged");
                }
            })
            .catch(error => console.error('Fetch error:', error));
    });
});

//---------------------------------------------------------SEARCHBAR SCRIPT---------------------------------------------------------
document.addEventListener('DOMContentLoaded', function() {
    const searchField = document.getElementById("searchbar");
    const resultsList = document.getElementById("suggestionList");
    let debounceTimeout = null;

    function handleSearch() {
        const searchQuery = searchField.value;
        console.log("Searching for : ", searchQuery);

        if (searchQuery.length > 1) {
            fetch(`/search?searchQuery=${searchQuery}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    resultsList.innerHTML = ''; // Ensure the list is cleared before populating
                    if (data.length > 0) {
                        data.forEach(product => {
                            const result = document.createElement('li');
                            result.textContent = product.label; // Ensure this matches your Product entity field
                            resultsList.appendChild(result);
                        });
                    } else {
                        const noResult = document.createElement('li');
                        noResult.textContent = "Sorry, couldn't find what you're looking for :(";
                        resultsList.appendChild(noResult);
                    }
                })
                .catch(error => console.error('Error:', error));
        } else {
            resultsList.innerHTML = ''; // Clear the list if the query length is 0 or 1
        }
    }

    function debounce(func, delay) {
        return function(...args) {
            if (debounceTimeout) clearTimeout(debounceTimeout);
            debounceTimeout = setTimeout(() => func.apply(this, args), delay);
        };
    }

    searchField.addEventListener('input', debounce(handleSearch, 300));
});