document.addEventListener("DOMContentLoaded", function () {
    const selectedSeats = [];
    const formSeat =
        document.getElementById("form-seat") || document.querySelector("form");
    const seatCheckboxes = document.querySelectorAll(".seat-checkbox");

    // Selector alternatif jika ID menggunakan format camelCase atau lowercase
    const selectedSeatsElement =
        document.getElementById("selectedSeats") ||
        document.getElementById("selected-seats");
    const quantityElement = document.getElementById("quantity");
    const priceElement = document.getElementById("price");
    const subTotalElement =
        document.getElementById("subTotal") ||
        document.getElementById("sub-total");
    const totalTaxElement =
        document.getElementById("totalTax") ||
        document.getElementById("total-tax");
    const grandTotalElement =
        document.getElementById("grandTotal") ||
        document.getElementById("grand-total");

    const taxRate = 0.11;

    // Function to update the seat display information
  function updateSeatInfo() {
      if (quantityElement)
          quantityElement.textContent = `${selectedSeats.length} People`;
      if (selectedSeatsElement)
          selectedSeatsElement.textContent =
              selectedSeats.length > 0 ? selectedSeats.join(", ") : "-";

      // Jika tidak ada kursi yang dipilih, paksa semua nominal jadi 0
      const currentPrice = selectedSeats.length > 0 ? basePrice : 0;
      const subTotal = basePrice * selectedSeats.length;
      const totalTax = subTotal * taxRate;
      const grandTotal = subTotal + totalTax;

      // Tampilkan Rp 0 jika belum ada kursi yang dipilih
      if (priceElement)
          priceElement.textContent = `Rp ${currentPrice.toLocaleString("id-ID")}`;
      if (subTotalElement)
          subTotalElement.textContent = `Rp ${subTotal.toLocaleString("id-ID")}`;
      if (totalTaxElement)
          totalTaxElement.textContent = `Rp ${totalTax.toLocaleString("id-ID")}`;
      if (grandTotalElement)
          grandTotalElement.textContent = `Rp ${grandTotal.toLocaleString("id-ID")}`;
  }

    // Add event listener to each seat checkbox
    seatCheckboxes.forEach((checkbox) => {
        checkbox.addEventListener("change", function () {
            const seatLabel = this.closest("label");
            if (!seatLabel) return;

            const seatName = seatLabel.getAttribute("data-seat");
            const seatId = seatLabel.getAttribute("data-seat-id");

            if (this.checked) {
                selectedSeats.push(seatName);

                // Buat input hidden untuk dikirim ke backend Laravel
                const input = document.createElement("input");
                input.type = "hidden";
                input.name = "selected_seats[]";
                input.value = seatId;
                input.id = `hidden-seat-${seatId}`; // Beri ID unik agar mudah dihapus
                if (formSeat) formSeat.appendChild(input);
            } else {
                const index = selectedSeats.indexOf(seatName);
                if (index > -1) {
                    selectedSeats.splice(index, 1);
                    const hiddenInput =
                        document.getElementById(`hidden-seat-${seatId}`) ||
                        document.querySelector(
                            `input[name="selected_seats[]"][value="${seatId}"]`,
                        );
                    if (hiddenInput) hiddenInput.remove();
                }
            }
            updateSeatInfo();
        });
    });
});
