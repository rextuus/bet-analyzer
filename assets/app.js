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
let containerElements = document.querySelectorAll('.switchable-content-header');

containerElements.forEach(function(element) {

    element.addEventListener("click", function() {
        toggleContent(element);
    });
});

function toggleContent(clickedElement) {
    let contentElements = clickedElement.parentElement.children;

    let filteredElements = Array.prototype.filter.call(contentElements, function(el) {
        return !el.classList.contains('switchable-content-header');
    });

    let currentActive = 0;
    let nextActive = 1;

    filteredElements.forEach(function(el, index) {
        if (el.classList.contains('active')) {
            currentActive = index;
            nextActive = currentActive + 1;
            if (nextActive === filteredElements.length) {
                currentActive = filteredElements.length - 1;
                nextActive = 0;
            }
        }
    });
    filteredElements.forEach(function(el, index) {
        if (index === currentActive){
            el.classList.remove('active');
            el.classList.add('inactive');
        }
        else if (index === nextActive){
            el.classList.remove('inactive');
            el.classList.add('active');
        }
        else {
            el.classList.remove('active');
            el.classList.add('inactive');
        }
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