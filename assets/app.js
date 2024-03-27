import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/const.scss';
import './styles/app.scss';
import './styles/dashbaord.scss';
import './styles/simulator-detail.scss';

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


document.addEventListener('DOMContentLoaded', function() {
    const progressBar = document.getElementById('progress-bar');
    const timeLeftDisplay = document.getElementById('time-left');

    if (progressBar && timeLeftDisplay){
        // Adjust this value according to the specific minute you want to wait for
        const targetMinute = 10; // Wait for the 10th minute of every hour

        function updateProgressBar() {
            const currentTime = new Date();
            const currentMinute = currentTime.getMinutes();
            const currentHour = currentTime.getHours();

            let nextExecutionTime;
            if (currentMinute >= targetMinute) {
                nextExecutionTime = new Date(currentTime.getFullYear(), currentTime.getMonth(), currentTime.getDate(), currentHour + 1, targetMinute, 0, 0);
            } else {
                nextExecutionTime = new Date(currentTime.getFullYear(), currentTime.getMonth(), currentTime.getDate(), currentHour, targetMinute, 0, 0);
            }

            const timeUntilNextExecution = nextExecutionTime - currentTime;

            const progressPercentage = ((3600000 - timeUntilNextExecution) / 3600000) * 100;
            progressBar.style.width = `${progressPercentage}%`;

            const minutes = Math.floor((timeUntilNextExecution % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((timeUntilNextExecution % (1000 * 60)) / 1000);
            timeLeftDisplay.textContent = `Next bet updates: ${pad(minutes)}:${pad(seconds)}`;

            if (timeUntilNextExecution <= 0) {
                progressBar.classList.add('fill');
                setTimeout(() => {
                    progressBar.style.width = '0%';
                    progressBar.classList.remove('fill');
                }, 1000);
            }
        }

        function pad(num) {
            return (num < 10 ? '0' : '') + num;
        }

        // Initial call to update progress bar and time
        updateProgressBar();

        // Update progress bar and time every second
        setInterval(updateProgressBar, 1000);
    }
});

document.addEventListener('DOMContentLoaded', function () {
    const oddsContainers = document.querySelectorAll('.content.odds');

    oddsContainers.forEach(container => {
        const toggleBtn = container.querySelector('.toggle-btn');
        const additionalContent = container.querySelector('.additional-content'); // Selecting the additional content
        toggleBtn.addEventListener('click', function () {
            toggleAdditionalContent(additionalContent); // Passing additional content to the toggle function
        });
    });

    function toggleAdditionalContent(additionalContent) {
        additionalContent.classList.toggle('collapsed');
    }
});


document.addEventListener('DOMContentLoaded', function() {
    var externalLinks = document.querySelectorAll('.external-link');

    externalLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            let copyText = this.getAttribute('data-copy-text');

            navigator.clipboard.writeText(copyText)
                .then(() => {
                    console.log('Text copied to clipboard successfully');
                })
                .catch(err => {
                    console.error('Could not copy text: ', err);
                });

            let url = this.getAttribute('href');

            // // Open link in new tab
            // let newTab = window.open(url, '_blank');
            // newTab.focus();

        });
    });
});



