document.addEventListener('DOMContentLoaded', () => {
  // Initialize AOS if you're using it
  if (typeof AOS !== 'undefined') {
    AOS.init({ duration: 800, once: true });
  }

  // --- Utility Functions ---
  function safeQuery(selector) {
    return document.querySelector(selector) || null;
  }

  function safeQueryAll(selector) {
    return document.querySelectorAll(selector) || [];
  }

  function safeSetInnerHTML(element, html) {
    if (element) element.innerHTML = html;
  }

  // --- Elements ---
  const stepHeaders = safeQueryAll('.step-item');
  const stepContents = safeQueryAll('.step-content');

  const carTypeButtons = safeQueryAll('.car-select-btn');
  const serviceSelectButtons = safeQueryAll('.service-select-btn');

  const bookNowBtn = safeQuery('#bookNowBtn');
  const confirmLocationBtn = safeQuery('#confirmLocationBtn');
  const nextToVehicleInfoBtn = safeQuery('#nextToVehicleInfoBtn');
  const nextToAddonsBtn = safeQuery('#nextToAddonsBtn');
  const nextToRequestsBtn = safeQuery('#nextToRequestsBtn');
  const confirmBookingBtn = safeQuery('#confirmBookingBtn');

  const backButtons = safeQueryAll('.back-btn');

  const bookingDateInput = safeQuery('#bookingDate');
  const bookingTimeSelect = safeQuery('#bookingTime');
  const customerPhoneInput = safeQuery('#customerPhone');
  const dateWarning = safeQuery('#dateWarning');

  const vehicleMakeInput = safeQuery('#vehicleMake');
  const vehicleModelInput = safeQuery('#vehicleModel');
  const licensePlateInput = safeQuery('#licensePlate');
  const specialRequestsInput = safeQuery('#specialRequests');

  const addOnsContainer = safeQuery('#addOnsContainer');
  const addOnCheckboxes = safeQueryAll('.add-on-checkbox');

  // Summary elements
  const summaryCarType = safeQuery('#summary-car-type');
  const summaryService = safeQuery('#summary-service');
  const summaryTotalCost = safeQuery('#summary-total-price');
  const summaryDateTimeDate = safeQuery('#summary-datetime-date');
  const summaryDateTimeTime = safeQuery('#summary-datetime-time');
  const summaryAddOnsList = safeQuery('#summary-add-ons-list');
  const summaryAddOnsSection = safeQuery('.summary-add-ons-section');
  const summaryRequestsSection = safeQuery('#summary-requests-section');
  const summaryRequestsText = safeQuery('#summary-requests-text');

  // Hidden inputs
  const hiddenCarType = safeQuery('#hiddenCarType');
  const hiddenServiceId = safeQuery('#hiddenServiceId');
  const hiddenServicePrice = safeQuery('#hiddenServicePrice');
  const hiddenBookingDate = safeQuery('#hiddenBookingDate');
  const hiddenBookingTime = safeQuery('#hiddenBookingTime');
  const hiddenMake = safeQuery('#hiddenMake');
  const hiddenModel = safeQuery('#hiddenModel');
  const hiddenLicensePlate = safeQuery('#hiddenLicensePlate');
  const hiddenSpecialRequests = safeQuery('#hiddenSpecialRequests');
  const hiddenTotalPrice = safeQuery('#hiddenTotalPrice');
  const addonsFormGroup = safeQuery('#addonsFormGroup');

  // --- State ---
  let selectedCarType = null;
  let selectedServiceId = null;
  let selectedServiceName = null;
  let selectedServicePrice = 0;
  let selectedServiceDuration = null;
  let selectedAddOns = [];
  let locationConfirmed = false;
  let currentStep = 1;

  // --- Step Handling ---
  function updateStep(stepNumber) {
    currentStep = stepNumber;

    stepHeaders.forEach((header, idx) => {
      const stepNumText = header.querySelector('.step-num-text');
      const checkIcon = header.querySelector('.bi-check-circle-fill');
      if (idx + 1 === stepNumber) {
        header.classList.add('active');
        header.classList.remove('completed');
        if (stepNumText) stepNumText.style.display = 'inline';
        if (checkIcon) checkIcon.style.display = 'none';
      } else if (idx + 1 < stepNumber) {
        header.classList.remove('active');
        header.classList.add('completed');
        if (stepNumText) stepNumText.style.display = 'none';
        if (checkIcon) checkIcon.style.display = 'inline';
      } else {
        header.classList.remove('active', 'completed');
        if (stepNumText) stepNumText.style.display = 'inline';
        if (checkIcon) checkIcon.style.display = 'none';
      }
    });

    stepContents.forEach(content => (content.style.display = 'none'));
    const curContent = safeQuery(`#step${stepNumber}`);
    if (curContent) {
      curContent.style.display = 'block';
      curContent.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    updateBookingSummary();
    updateButtonStates();
  }

  function checkStepRequirements(step) {
    switch (step) {
      case 1: return selectedCarType !== null;
      case 2: return selectedServiceId !== null;
      case 3: return bookingDateInput?.value !== '' && bookingTimeSelect?.value !== '';
      case 4: return locationConfirmed;
      case 5: return customerPhoneInput?.value.trim() !== '';
      case 6:
        return vehicleMakeInput?.value.trim() !== '' &&
               vehicleModelInput?.value.trim() !== '' &&
               licensePlateInput?.value.trim() !== '';
      case 7: return true; // add-ons optional
      case 8: return true; // requests optional
      default: return false;
    }
  }

  function updateButtonStates() {
    if (bookNowBtn) bookNowBtn.disabled = !checkStepRequirements(3);
    if (nextToVehicleInfoBtn) nextToVehicleInfoBtn.disabled = !checkStepRequirements(5);
    if (nextToAddonsBtn) nextToAddonsBtn.disabled = !checkStepRequirements(6);
  }

  // --- Booking Summary ---
  function updateBookingSummary() {
    let total = parseFloat(selectedServicePrice) || 0;

    if (summaryAddOnsList) safeSetInnerHTML(summaryAddOnsList, '');
    selectedAddOns.forEach(addOn => {
      total += parseFloat(addOn.price);
      if (summaryAddOnsList) {
        const li = document.createElement('li');
        li.className = 'd-flex justify-content-between align-items-center mb-1';
        li.innerHTML = `
          <span class="text-muted"><i class="bi bi-plus me-2"></i>${addOn.name}</span>
          <span class="fw-bold">${parseFloat(addOn.price).toFixed(2)} JD</span>
        `;
        summaryAddOnsList.appendChild(li);
      }
    });

    if (summaryAddOnsSection) {
      summaryAddOnsSection.style.display = selectedAddOns.length > 0 ? 'block' : 'none';
    }

    const req = specialRequestsInput?.value.trim() || '';
    if (req) {
      if (summaryRequestsSection) summaryRequestsSection.style.display = 'block';
      if (summaryRequestsText) summaryRequestsText.textContent = req;
    } else {
      if (summaryRequestsSection) summaryRequestsSection.style.display = 'none';
    }

    if (summaryCarType) summaryCarType.textContent = selectedCarType || '...';
    if (summaryService) summaryService.textContent = selectedServiceName || '...';
    if (summaryDateTimeDate) summaryDateTimeDate.textContent = bookingDateInput?.value || '...';
    if (summaryDateTimeTime && bookingTimeSelect) {
      const timeOption = bookingTimeSelect.options[bookingTimeSelect.selectedIndex];
      summaryDateTimeTime.textContent = timeOption ? timeOption.textContent : '...';
    }
    if (summaryTotalCost) summaryTotalCost.textContent = `${total.toFixed(2)} JD`;

    // Hidden inputs
    if (hiddenCarType) hiddenCarType.value = selectedCarType || '';
    if (hiddenServiceId) hiddenServiceId.value = selectedServiceId || '';
    if (hiddenServicePrice) hiddenServicePrice.value = parseFloat(selectedServicePrice).toFixed(2) || '0';
    if (hiddenBookingDate) hiddenBookingDate.value = bookingDateInput?.value || '';
    if (hiddenBookingTime) hiddenBookingTime.value = bookingTimeSelect?.value || '';
    if (hiddenMake) hiddenMake.value = vehicleMakeInput?.value.trim() || '';
    if (hiddenModel) hiddenModel.value = vehicleModelInput?.value.trim() || '';
    if (hiddenLicensePlate) hiddenLicensePlate.value = licensePlateInput?.value.trim() || '';
    if (hiddenSpecialRequests) hiddenSpecialRequests.value = req;
    if (hiddenTotalPrice) hiddenTotalPrice.value = total.toFixed(2);

    // Add-ons hidden inputs
    if (addonsFormGroup) {
      safeSetInnerHTML(addonsFormGroup, '');
      selectedAddOns.forEach(addOn => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'add_ons[]';
        input.value = addOn.id;
        addonsFormGroup.appendChild(input);
      });
    }
  }

  // --- Time Slots Fetch ---
  async function fetchAndPopulateTimeSlots() {
    if (!bookingDateInput) return;
    const selectedDate = bookingDateInput.value;
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const selectedDateObj = new Date(selectedDate);

    if (!selectedDate || selectedDateObj < today) {
      if (dateWarning) dateWarning.style.display = 'block';
      if (bookingTimeSelect) bookingTimeSelect.innerHTML = '<option selected disabled>Please select a future date.</option>';
      return;
    }
    if (dateWarning) dateWarning.style.display = 'none';

    if (!selectedServiceDuration) {
      if (bookingTimeSelect) bookingTimeSelect.innerHTML = '<option selected disabled>Please select a service first...</option>';
      return;
    }

    try {
      const resp = await fetch(`get_available_slots.php?date=${encodeURIComponent(selectedDate)}&duration=${encodeURIComponent(selectedServiceDuration)}`);
      const data = await resp.json();
      if (bookingTimeSelect) {
        if (data.success) {
          bookingTimeSelect.innerHTML = '<option selected disabled>Select a time...</option>';
          data.available_slots.forEach(slot => {
            const opt = document.createElement('option');
            opt.value = slot.start_time;
            opt.textContent = `${slot.start_time} - ${slot.end_time}`;
            bookingTimeSelect.appendChild(opt);
          });
        } else {
          bookingTimeSelect.innerHTML = `<option selected disabled>${data.message}</option>`;
        }
      }
    } catch (err) {
      console.error('Error fetching time slots', err);
      if (bookingTimeSelect) bookingTimeSelect.innerHTML = '<option selected disabled>Error loading times.</option>';
    } finally {
      updateButtonStates();
    }
  }

  // --- Event Listeners ---
  carTypeButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      carTypeButtons.forEach(b => b.classList.remove('selected'));
      btn.classList.add('selected');
      selectedCarType = btn.dataset.car;
      updateStep(2);
    });
  });

  serviceSelectButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      const card = btn.closest('[data-service-id]');
      if (!card) return;
      serviceSelectButtons.forEach(b => {
        const parent = b.closest('[data-service-id]');
        if (parent) parent.classList.remove('selected');
      });
      card.classList.add('selected');
      selectedServiceId = card.dataset.serviceId;
      selectedServiceName = card.dataset.name;
      selectedServicePrice = card.dataset.price;
      selectedServiceDuration = card.dataset.duration;
      updateStep(3);
      fetchAndPopulateTimeSlots();
    });
  });

  if (bookingDateInput) bookingDateInput.addEventListener('change', fetchAndPopulateTimeSlots);
  if (bookingTimeSelect) bookingTimeSelect.addEventListener('change', () => { updateStep(4); updateBookingSummary(); });

  if (confirmLocationBtn) confirmLocationBtn.addEventListener('click', () => { locationConfirmed = true; updateStep(5); });
  if (customerPhoneInput) customerPhoneInput.addEventListener('input', updateButtonStates);

  [vehicleMakeInput, vehicleModelInput, licensePlateInput].forEach(inp => {
    if (inp) inp.addEventListener('input', updateButtonStates);
  });

  if (nextToVehicleInfoBtn) nextToVehicleInfoBtn.addEventListener('click', () => {
    if (checkStepRequirements(5)) { updateStep(6); updateBookingSummary(); } 
    else { alert('Please fill your phone number.'); }
  });

  if (nextToAddonsBtn) nextToAddonsBtn.addEventListener('click', () => {
    if (checkStepRequirements(6)) updateStep(7);
    else alert('Please fill vehicle info.');
  });

  if (nextToRequestsBtn) nextToRequestsBtn.addEventListener('click', () => updateStep(8));
  backButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      const target = parseInt(btn.dataset.step, 10);
      if (!isNaN(target)) updateStep(target);
    });
  });

  if (confirmBookingBtn) confirmBookingBtn.addEventListener('click', () => {
    if (!checkStepRequirements(8)) { alert('Please complete all required fields.'); return false; }
  });

  // --- Initialize ---
  updateStep(1);
  updateBookingSummary();
});
