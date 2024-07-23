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

    //-----------------------------------------------------------
    //-------for global product score on product info view-------
    //-----------------------------------------------------------
    const productStars = document.querySelector(".productStars");
    const productScore = document.querySelector(".globalRating");
    if (productStars && productScore) {
        const pStars = productStars.querySelectorAll(".fa-star");
        for (let i = 0; i < productScore.innerHTML; i++) {
            pStars[i].classList.add("star-active");
        };
    };

    //-----------------------------------------------------------
    //----------for review score on product info view------------
    //-----------------------------------------------------------
    const reviews = document.querySelectorAll(".review");

    if (reviews) {
        reviews.forEach(review => {
            let reviewScore = review.querySelector(".reviewRating");
            let reviewStars = review.querySelector(".reviewStars");
            let rStars = reviewStars.querySelectorAll(".fa-star");
        
            for (let i = 0; i < reviewScore.innerHTML; i++) {
                rStars[i].classList.add("star-active");
            }
            
        });
    };

    //-----------------------------------------------------------
    //-------for product global score on product list view-------
    //-----------------------------------------------------------
    const productsList = document.querySelectorAll(".productLi");

    productsList.forEach(product => {
        let itemScore = product.querySelector(".itemGlobalRating");
        let itemStars = product.querySelector(".itemStars");
        let iStars = itemStars.querySelectorAll(".fa-star");

        for (let i = 0; i < itemScore.innerHTML; i++) {
            iStars[i].classList.add("star-active");
        }
    });

});

//---------------------------------------------------BUILDER COMPATIBILITY SCRIPT---------------------------------------------------
document.addEventListener('DOMContentLoaded', function () {
    const cpuField = document.getElementById('build_cpu');
    const motherboardField = document.getElementById('build_motherboard');

    if (cpuField && motherboardField) {

        //-----------------------------------------------------------
        //----handles motherboard compatibility with selected CPU----
        //-----------------------------------------------------------
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
    


        //-----------------------------------------------------------
        //----handles CPU compatibility with selected motherboard----
        //-----------------------------------------------------------
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
    };
});

//---------------------------------------------------------SEARCHBAR SCRIPT---------------------------------------------------------
document.addEventListener('DOMContentLoaded', function() {
    const searchField = document.getElementById("searchbar");
    const searchBtn = document.getElementById("searchButton");
    const resultsList = document.getElementById("suggestionList");
    const queryUrlTemplate = searchBtn.getAttribute('query-url-template');
    const productUrlTemplate = resultsList.getAttribute('data-product-url-template');
    let debounceTimeout = null;

    function handleSearch() {
        const searchQuery = searchField.value.trim();
        console.log("Searching for: ", searchQuery);

        if (searchQuery.length > 1) {
            searchBtn.href = queryUrlTemplate.replace('PLACEHOLDER', searchQuery);
            fetch(`/search?searchQuery=${searchQuery}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    // Clear the results list before populating
                    resultsList.innerHTML = '';

                    // Populate the results list only if the search field is not empty
                    if (searchField.value.trim().length > 0) {
                        if (data.length > 0) {
                            data.forEach(product => {
                                const resultLink = document.createElement('a');
                                resultLink.href = productUrlTemplate.replace('PLACEHOLDER', product.id);
                                const result = document.createElement('li');
                                result.textContent = product.label; // Ensure this matches your Product entity field
                                resultLink.appendChild(result);
                                resultsList.appendChild(resultLink);
                            });
                        } else {
                            const noResult = document.createElement('li');
                            noResult.textContent = "Sorry, couldn't find what you're looking for :(";
                            resultsList.appendChild(noResult);
                        }
                    }
                })
                .catch(error => console.error('Error:', error));
        } else {
            searchBtn.href = '';
            resultsList.innerHTML = ''; // Clear the list if the query length is 1 or less
        }
    }

    function debounce(func, delay) {
        return function(...args) {
            if (debounceTimeout) clearTimeout(debounceTimeout);
            debounceTimeout = setTimeout(() => func.apply(this, args), delay);
        };
    }

    // Check if the search field is empty and clear the results list
    function clearResultsIfEmpty() {
        if (searchField.value.trim().length === 0) {
            resultsList.innerHTML = '';
        }
    }

    searchField.addEventListener('input', debounce(handleSearch, 300));
    
    searchField.addEventListener('input', clearResultsIfEmpty);

    searchField.addEventListener("keypress", function(event) {
        // If the user presses the "Enter" key on the keyboard
        if (event.key === "Enter") {
          // Cancel the default action, if needed
          event.preventDefault();
          // Trigger the button element with a click
          searchBtn.click();
        }
    });
});