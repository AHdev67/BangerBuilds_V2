// import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');

//----------------------BURGER MENU SCRIPT----------------------
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

hamburger.addEventListener("click", toggleMenu);