document.addEventListener("DOMContentLoaded", function () {
    // Function to handle selecting a dropdown option
    function handleSelection(dropdownId, labelId) {
        const selectedOption = document.querySelector(
            `#${dropdownId} input:checked`,
        );
        if (selectedOption) {
            const labelText = selectedOption.id;
            const labelEl = document.getElementById(labelId);
            if (labelEl) {
                labelEl.textContent = labelText;
            }
        }
    }

    // Add event listeners to each radio input inside the dropdowns
    const departureDropdown = document.getElementById("Departure-Dropdown");
    if (departureDropdown) {
        departureDropdown.querySelectorAll("input").forEach((input) => {
            input.addEventListener("change", function () {
                handleSelection("Departure-Dropdown", "Departure-Label");
                departureDropdown.classList.add("hidden");
            });
        });
    }

    const arrivalDropdown = document.getElementById("Arrival-Dropdown");
    if (arrivalDropdown) {
        arrivalDropdown.querySelectorAll("input").forEach((input) => {
            input.addEventListener("change", function () {
                handleSelection("Arrival-Dropdown", "Arrival-Label");
                arrivalDropdown.classList.add("hidden");
            });
        });
    }
});

document.addEventListener("DOMContentLoaded", function () {
    const buttons = document.querySelectorAll(".dropdown");
    const dropdownContents = document.querySelectorAll(".dropdown-content");
    let activeDropdown = null;

    // Toggle dropdowns when clicking on the button
    buttons.forEach((button) => {
        button.addEventListener("click", function (event) {
            event.stopPropagation();
            const target = document.querySelector(
                this.getAttribute("data-dropdown-target"),
            );
            if (!target) return;

            if (activeDropdown && activeDropdown !== target) {
                activeDropdown.classList.add("hidden");
            }

            target.classList.toggle("hidden");
            activeDropdown = target.classList.contains("hidden")
                ? null
                : target;
        });
    });

    // Prevent dropdown from closing when clicking inside the dropdown content
    dropdownContents.forEach((content) => {
        content.addEventListener("click", function (event) {
            event.stopPropagation();
        });
    });

    // Close dropdown when clicking outside
    document.addEventListener("click", function () {
        if (activeDropdown) {
            activeDropdown.classList.add("hidden");
            activeDropdown = null;
        }
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const dateInput = document.getElementById("date");
    const dateLabel = document.getElementById("Date-Label");

    // Hanya jalan kalau elemen tanggal ada di halaman ini (misal halaman search/beranda)
    if (dateInput && dateLabel) {
        const today = new Date();
        const options = { year: "numeric", month: "short", day: "numeric" };

        // Set the input date value to today's date
        dateInput.valueAsDate = today;

        // Update the Date-Label to today's date
        dateLabel.textContent = today.toLocaleDateString("id-ID", options);
    }

    const dateButton = document.getElementById("Date-Button");
    if (dateButton) {
        dateButton.addEventListener("click", function () {
            const dateEl = document.getElementById("date");
            if (dateEl) {
                dateEl.showPicker();
            }
        });
    }

    if (dateInput) {
        dateInput.addEventListener("change", function () {
            const currentDateInput = document.getElementById("date");
            const currentDateLabel = document.getElementById("Date-Label");
            if (currentDateInput && currentDateLabel) {
                const selectedDate = new Date(currentDateInput.value);
                const options = {
                    year: "numeric",
                    month: "short",
                    day: "numeric",
                };
                currentDateLabel.textContent = selectedDate.toLocaleDateString(
                    "id-ID",
                    options,
                );
            }
        });
    }
});

document.addEventListener("DOMContentLoaded", function () {
    const quantityInput = document.getElementById("quantity");
    if (!quantityInput) return; // Elemen quantity tidak ada di halaman ini, hentikan

    const numberDisplays = document.querySelectorAll(".number");
    const minusButton = document.getElementById("minus");
    const plusButton = document.getElementById("plus");

    function updateNumberDisplay(value) {
        numberDisplays.forEach((el) => (el.textContent = value));
    }

    // Initial update for the number displays
    updateNumberDisplay(quantityInput.value);

    if (plusButton) {
        plusButton.addEventListener("click", function () {
            let currentValue = parseInt(quantityInput.value);
            quantityInput.value = currentValue + 1;
            updateNumberDisplay(quantityInput.value);
        });
    }

    if (minusButton) {
        minusButton.addEventListener("click", function () {
            let currentValue = parseInt(quantityInput.value);
            if (currentValue > 1) {
                quantityInput.value = currentValue - 1;
                updateNumberDisplay(quantityInput.value);
            }
        });
    }

    // Update on manual input change
    quantityInput.addEventListener("input", function () {
        let value = parseInt(this.value) || 1;
        if (value < 1) value = 1;
        this.value = value;
        updateNumberDisplay(value);
    });
});

const swiperEl = document.querySelector(".swiper");
if (swiperEl) {
    const swiper = new Swiper(".swiper", {
        // Optional parameters
        spaceBetween: 24,
        slidesPerView: "auto",
        slidesOffsetAfter: 24,
        slidesOffsetBefore: 24,
    });
}
