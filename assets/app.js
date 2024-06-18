import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';
import './styles/styles.css';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootswatch/dist/sandstone/bootstrap.min.css';
import 'flatpickr/dist/flatpickr.css';
import '@fortawesome/fontawesome-free/css/fontawesome.min.css';
import '@fortawesome/fontawesome-free/js/fontawesome';
import '@fortawesome/fontawesome-free/js/regular';
import '@fortawesome/fontawesome-free/js/solid';
import '@fortawesome/fontawesome-free/js/brands';

require('bootstrap');
import flatpickr from 'flatpickr';
import {
    French
} from 'flatpickr/dist/l10n/fr.js';
import './turbo/turbo-helper';

document.addEventListener('DOMContentLoaded', () => {
    flatpickr(".flatpickr", {
        locale: French
    });
});

document.addEventListener('DOMContentLoaded', () => {
    const containers = document.querySelectorAll('.nav-tab-ad-container');
    
    const handleResize = () => {
        const isMobileView = window.innerWidth < 950;
        
        containers.forEach(container => {
            const editLinkMobile = container.querySelector('.btn.btn-to-modal-edit');
            const editButton = container.querySelector('.l1ovpqvx');

            if (isMobileView) {
                if (editLinkMobile) {
                    editLinkMobile.style.display = 'inline-block';
                }
                if (editButton) {
                    editButton.style.display = 'none';
                }
            } else {
                if (editLinkMobile) {
                    editLinkMobile.style.display = 'none';
                }
                if (editButton) {
                    editButton.style.display = 'inline-block';
                }
            }

            if (editLinkMobile) {
                editLinkMobile.addEventListener('click', (event) => {
                    if (isMobileView) {
                        event.preventDefault();
                        window.location.href = editLinkMobile.href;
                    }
                });
            }
        });
    };

    const attachEventListeners = () => {
        console.log('Attaching event listeners to containers...');
        handleResize();
        window.addEventListener('resize', handleResize);
    };

    document.addEventListener('turbo:load', attachEventListeners);
    document.addEventListener('turbo:frame-load', attachEventListeners);
    document.addEventListener('turbo:render', attachEventListeners);
    document.addEventListener('turbo:submit-end', attachEventListeners);

    attachEventListeners(); // Call initially
});


document.addEventListener('DOMContentLoaded', function () {
    closeAlertMessage();
});

// Événements Turbo : exécution du script après les mises à jour du DOM par Turbo
document.addEventListener('turbo:load', function () {
    closeAlertMessage();
});

document.addEventListener('turbo:frame-load', function () {
    closeAlertMessage();
});

document.addEventListener('turbo:render', function () {
    closeAlertMessage();
});

document.addEventListener('turbo:before-render', function () {
    closeAlertMessage();
});

const closeAlertMessage = () => {
    const alert = document.querySelector('.alert');
    if (alert) {
        setTimeout(function () {
            alert.style.transition = "opacity 1s ease";
            alert.style.opacity = '0';

            setTimeout(function () {
                alert.style.display = 'none';
            }, 1000); // Après la transition d'opacité (1 seconde)
        }, 4000); // Après 5 secondes
    }
}