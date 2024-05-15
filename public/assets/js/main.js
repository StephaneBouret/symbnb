document.addEventListener('DOMContentLoaded', function () {
    // Initialisation de Flatpickr
    var fp = flatpickr(".flatpickr", {
        altInput: true,
        minDate: "today",
        altFormat: "j F, Y",
        dateFormat: "d.m.Y",
        locale: "fr",
        disable: notAvailableDays,
        onChange: function (selectedDates, dateStr, instance) {
            // Appel de la fonction pour calculer la durée
            calculateDuration();
        }
    });

    // Fonction pour calculer la durée
    function calculateDuration() {
        // Récupération des champs de date de départ et d'arrivée
        var startDateInput = document.getElementById('booking_form_startDateAt');
        var endDateInput = document.getElementById('booking_form_endDateAt');

        // Vérification des champs de date
        if (!startDateInput || !endDateInput) {
            console.error("Les champs de date ne peuvent pas être trouvés.");
            return;
        }

        // Vérification des valeurs des champs de date
        var startDateValue = startDateInput.value;
        var endDateValue = endDateInput.value;

        if (!startDateValue || !endDateValue) {
            console.error("Les valeurs des champs de date sont vides.");
            return;
        }

        // Conversion des valeurs en objets de date JavaScript
        var startDate = parseDate(startDateValue);
        var endDate = parseDate(endDateValue);

        // Vérification des dates valides
        if (!startDate || !endDate) {
            console.error("Les dates ne sont pas valides.");
            return;
        }

        // Vérification si startDate est supérieure à endDate
        if (startDate > endDate) {
            console.log("La date de départ est postérieure à la date d'arrivée.");
            daysDifference = 0; // Définir le résultat à 0 nuit
        } else {
            // Calcul de la différence en millisecondes
            var timeDifference = endDate.getTime() - startDate.getTime();

            // Conversion en jours
            var daysDifference = timeDifference / (1000 * 3600 * 24);
        }

        // Mise à jour de l'élément affichant la durée
        var durationElement = document.getElementById('days');
        durationElement.textContent = daysDifference;

        // Récupération de la valeur du prix en euros
        var priceElement = document.getElementById('priceValue');
        var priceText = priceElement.innerText;

        // Suppression de "par nuit" et des espaces pour obtenir uniquement le prix
        var price = parseFloat(priceText.replace('par nuit', '').trim());

        // Vérification si le prix est un nombre valide
        if (!isNaN(price)) {
            console.log("Prix récupéré en euros : ", price);

            // Le reste de votre script JavaScript ...
        } else {
            console.error("Le prix n'est pas un nombre valide.");
        }

        // Calcul du montant total
        var amount = daysDifference * price;

        // Mise à jour de l'élément affichant le montant
        var amountElement = document.getElementById('amount');
        amountElement.textContent = amount.toFixed(2);
    }

    // Fonction pour analyser une date avec le format "jour.mois.année"
    function parseDate(dateStr) {
        var parts = dateStr.split('.');
        if (parts.length === 3) {
            var day = parseInt(parts[0], 10);
            var month = parseInt(parts[1], 10) - 1; // Les mois sont 0-indexés dans JavaScript
            var year = parseInt(parts[2], 10);
            return new Date(year, month, day);
        }
        return null;
    }
});