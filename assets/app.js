// Einstiegspunkt der AssetMapper-Importmap ({{ importmap('app') }} in base.html.twig).
// Die Styles (assets/styles/main.scss, über symfonycasts/sass-bundle) und Font Awesome bindet base.html.twig ein.
import 'bootstrap';
import './bootstrap.js';

// Chart.js (symfony/ux-chartjs) an das dunkle Schema anpassen – gilt für alle Diagramme der App.
document.addEventListener('chartjs:init', (event) => {
    const Chart = event.detail.Chart;
    Chart.defaults.color = '#93a0b8';
    Chart.defaults.borderColor = 'rgba(147, 160, 184, 0.15)';
    Chart.defaults.font.family = 'Inter, system-ui, -apple-system, "Segoe UI", Roboto, sans-serif';
    Chart.defaults.plugins.legend.labels.boxWidth = 12;
});

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
        // Ergebnisse werden stündlich zur Minute 5 geprüft (CheckMatchOutcomes, docker/cron/crontab).
        const targetMinute = 5;

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
            timeLeftDisplay.textContent = `${pad(minutes)}:${pad(seconds)}`;

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


// upcoming fixtrue
document.addEventListener('DOMContentLoaded', function () {
    const tipicoLinks = document.querySelectorAll('.tipico-url a');

    tipicoLinks.forEach(function(tipicoLink) {
        let stateElement = tipicoLink.parentNode.parentNode.parentNode.querySelector('.check-state');

        tipicoLink.addEventListener('click', function (event) {
            event.preventDefault(); // Prevent the default behavior of the link


            // Add the 'is-checked' class to the check-state span
            stateElement.classList.add('is-checked');

            let newTab = window.open(event.target, '_blank');
            // newTab.focus();
        });
    });
});


