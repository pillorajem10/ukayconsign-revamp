document.addEventListener('DOMContentLoaded', function() {
    // Initialize Quagga for barcode scanning
    const scanBarcodeButton = document.getElementById('scanBarcodeButton');
    const stopScanButton = document.getElementById('stopScan');
    const cameraContainer = document.getElementById('cameraContainer');
    const videoContainer = document.getElementById('videoContainer');
    const barcodeForm = document.getElementById('barcodeForm');

    // Function to hide messages after a specified duration
    function hideMessage(elementId, duration) {
        const messageElement = document.getElementById(elementId);
        if (messageElement) {
            setTimeout(() => {
                messageElement.style.display = 'none';
            }, duration);
        }
    }

    // Function to toggle payment fields based on the selected payment method
    function togglePaymentFields() {
        const modeOfPayment = document.getElementById('mode_of_payment');
        const refNumberGroup = document.getElementById('ref_number_group');
        const amountPaidGroup = document.getElementById('amount_paid_group');
        const changeGroup = document.getElementById('change_group');

        if (modeOfPayment.value === 'Cash') {
            refNumberGroup.style.display = 'none'; // Hide Ref #
            amountPaidGroup.style.display = 'block'; // Show Amount Paid
            changeGroup.style.display = 'block'; // Show Change
        } else if (modeOfPayment.value === 'eWallet') {
            refNumberGroup.style.display = 'block'; // Show Ref #
            amountPaidGroup.style.display = 'none'; // Hide Amount Paid
            changeGroup.style.display = 'none'; // Hide Change
        }
    }

    // Function to calculate change based on the amount paid and total
    function calculateChange() {
        const amountPaid = parseFloat(document.querySelector('input[name="amount_paid"]').value) || 0;
        const total = totalAmount; // Assume totalAmount is defined elsewhere in your script
        const change = (amountPaid - total).toFixed(2);
        document.getElementById('cx_change').value = change >= 0 ? change : 0;
    }

    // Function to update selected action when the form is submitted
    function updateSelectedAction() {
        const actionSelect = document.getElementById('actionSelect');
        const actionInput = document.getElementById('actionInput'); // Ensure this element is defined
        const storeInventoryDetails = document.getElementById('storeInventoryDetails'); // Define this
        const posCartDetails = document.getElementById('posCartDetails'); // Define this

        if (actionSelect && actionInput) {
            actionInput.value = actionSelect.value;

            // Ensure storeInventoryDetails and posCartDetails are defined
            if (storeInventoryDetails && posCartDetails) {
                if (actionSelect.value === 'pos') {
                    storeInventoryDetails.style.display = 'none'; // Hide inventory details
                    posCartDetails.style.display = 'block'; // Show POS cart details
                } else {
                    storeInventoryDetails.style.display = 'block'; // Show inventory details
                    posCartDetails.style.display = 'none'; // Hide POS cart details
                }
            }
        }
    }

    // Event listener for barcode scan button
    if (scanBarcodeButton) {
        scanBarcodeButton.addEventListener('click', function() {
            cameraContainer.style.display = 'block';
            Quagga.init({
                inputStream: {
                    name: "Live",
                    type: "LiveStream",
                    target: videoContainer,
                    constraints: {
                        facingMode: "environment"
                    },
                },
                decoder: {
                    readers: ["code_128_reader"]
                },
            }, function(err) {
                if (err) {
                    console.log(err);
                    alert("Error initializing camera: " + err.message);
                    return;
                }
                Quagga.start();
            });

            Quagga.onDetected(function(data) {
                var code = data.codeResult.code;
                document.querySelector('input[name="barcode_number"]').value = code;
                Quagga.stop();
                cameraContainer.style.display = 'none';
                barcodeForm.submit();
            });
        });
    }

    // Event listener for stop scan button
    if (stopScanButton) {
        stopScanButton.addEventListener('click', function() {
            Quagga.stop();
            cameraContainer.style.display = 'none';
        });
    }

    // Call the hideMessage function for the success and error messages
    hideMessage('success-message', 2000);
    hideMessage('error-message', 2000);

    // Validate sale form before submission
    barcodeForm.onsubmit = function() {
        updateSelectedAction();
        return validateSaleForm(); // Ensure you have defined validateSaleForm function
    };

    // Attach event listener for amount paid input
    const amountPaidInput = document.querySelector('input[name="amount_paid"]');
    if (amountPaidInput) {
        amountPaidInput.addEventListener('input', calculateChange);
    }

    // Set up payment fields on page load
    const modeOfPayment = document.getElementById('mode_of_payment');
    if (modeOfPayment) {
        togglePaymentFields(); // Initial toggle
        modeOfPayment.addEventListener('change', togglePaymentFields);
    }

    // Attach event listener for action select change
    const actionSelect = document.getElementById('actionSelect');
    if (actionSelect) {
        actionSelect.addEventListener('change', updateSelectedAction); // Add event listener for action select
    }
});
