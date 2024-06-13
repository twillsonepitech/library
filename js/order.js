const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.onmouseenter = Swal.stopTimer;
        toast.onmouseleave = Swal.resumeTimer;
    }
});

function openModal() {
    const rentalDate = document.getElementById('rental-date').value;
    const returnDate = document.getElementById('return-date').value;

    if (!rentalDate || !returnDate) {
        Toast.fire({
            icon: "warning",
            html: "Please make sure to enter the rental and return dates !"
        });
    } else {
        document.getElementById('hidden-rental-date').value = rentalDate;
        document.getElementById('hidden-return-date').value = returnDate;
        document.getElementById('rentalModal').style.display = 'block';
    }
}

function closeModal() {
    document.getElementById('rentalModal').style.display = 'none';
}

window.onclick = function(event) {
    if (event.target == document.getElementById('rentalModal')) {
        closeModal();
    }
}

document.addEventListener('DOMContentLoaded', (event) => {
    const _rentalDate = document.getElementById('rental-date');
    const _returnDate = document.getElementById('return-date');
    const rentDays = document.getElementById('rent-days');
    const _totalRentalPrice = document.getElementById('total-rental-price');

    function calculateDayDifference() {
        const rentalDate = new Date(_rentalDate.value);
        const returnDate = new Date(_returnDate.value);

        if (rentalDate && returnDate && returnDate >= rentalDate) {
            const timeDifference = returnDate - rentalDate;
            const dayDifference = timeDifference / (1000 * 3600 * 24);
            rentDays.textContent = `Rent days: ${dayDifference}`;
            calculateTotalRentalPrice(dayDifference);
        } else {
            rentDays.textContent = 'Rent days: 0';
            _totalRentalPrice.textContent = 'Total Rental Price: RM0.00';
        }
    }

    function calculateTotalRentalPrice(dayDifference) {
        const rows = document.querySelectorAll('tbody tr');
        let totalRentalPrice = 0;

        rows.forEach(row => {
            const rentalPriceElement = row.querySelector('.rental_price');
            const rentalPrice = parseFloat(rentalPriceElement.textContent.replace('RM', ''));
            totalRentalPrice += rentalPrice * dayDifference;
        });
        _totalRentalPrice.textContent = `Total Rental Price: RM${totalRentalPrice.toFixed(2)}`;
    }

    _rentalDate.addEventListener('change', () => {
        const rentalDate = new Date(_rentalDate.value);
        rentalDate.setDate(rentalDate.getDate() + 1);
        _returnDate.min = rentalDate.toISOString().split('T')[0];
    });
    _returnDate.addEventListener('change', calculateDayDifference);
});

document.addEventListener('DOMContentLoaded', function() {
    const searchBar = document.querySelector('.search-bar');
    searchBar.addEventListener('input', function() {
        const filter = searchBar.value.toLowerCase();
        const rows = document.querySelectorAll('tbody tr');

        rows.forEach(row => {
            const name = row.querySelector('.name').innerText.toLowerCase();
            if (name.includes(filter)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
});

const phoneInputField = document.querySelector("#phone");
const phoneInput = window.intlTelInput(phoneInputField, {
    utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js",
});

const info = document.querySelector(".alert-info");
const error = document.querySelector(".alert-error");

function process(event) {
    event.preventDefault();

    const phoneNumber = phoneInput.getNumber();

    info.style.display = "none";
    error.style.display = "none";

    if (phoneInput.isValidNumber()) {
        info.style.display = "";
        info.innerHTML = `Phone number is valid: <strong>${phoneNumber}</strong>`;
    } else {
        error.style.display = "";
        error.innerHTML = `Invalid phone number.`;
    }
}
