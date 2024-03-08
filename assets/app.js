import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss';

// JavaScript to toggle visibility of content elements
let containerElements = document.querySelectorAll('.switchable-content');

containerElements.forEach(function(element) {

    element.addEventListener("click", function() {
        toggleContent(element);
    });
});

function toggleContent(clickedElement) {
    let contentElements = clickedElement.children;

    Array.prototype.forEach.call(contentElements, function(el) {
        el.classList.toggle('active');
        el.classList.toggle('inactive');
    });

    // contentElements.forEach(function(element) {
    //     if (element.classList.contains('active')) {
    //         element.classList.toggle('active');
    //         element.classList.toggle('inactive');
    //     }else{
    //         element.classList.toggle('active');
    //         element.classList.toggle('inactive');
    //     }
    // });
}