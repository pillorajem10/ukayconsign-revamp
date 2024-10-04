document.getElementById('scanBarcodeButton').addEventListener('click', function() {
    document.getElementById('cameraContainer').style.display = 'block';
    Quagga.init({
        inputStream: {
            name: "Live",
            type: "LiveStream",
            target: document.getElementById('videoContainer'),
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
        document.getElementById('cameraContainer').style.display = 'none';
        document.getElementById('barcodeForm').submit();
    });
});

document.getElementById('stopScan').addEventListener('click', function() {
    Quagga.stop();
    document.getElementById('cameraContainer').style.display = 'none';
});


function updateSelectedAction() {
    const actionSelect = document.getElementById('actionSelect');
    console.log('ACTION SELECT', actionSelect.value);
    document.getElementById('actionInput').value = actionSelect.value;

    if (actionSelect.value === 'pos') {
        storeInventoryDetails.style.display = 'none';
        posCartDetails.style.display = 'block';
    } else {
        storeInventoryDetails.style.display = 'block';
        posCartDetails.style.display = 'none';
    }
}

// Call this function when the form is submitted
document.getElementById('barcodeForm').onsubmit = function() {
    updateSelectedAction();
};


function hideMessage(elementId, duration) {
    const messageElement = document.getElementById(elementId);
    if (messageElement) {
        setTimeout(function() {
            messageElement.style.display = 'none';
        }, duration);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Call the hideMessage function for the success message
    hideMessage('success-message', 2000);
    hideMessage('error-message', 2000);
});


function calculateChange() {
    const amountPaid = parseFloat(document.querySelector('input[name="amount_paid"]').value) || 0;
    const total = totalAmount; // Use the value passed from the Blade template
    const change = (amountPaid - total).toFixed(2);
    document.getElementById('cx_change').value = change >= 0 ? change : 0;
}

function validateSaleForm() {
    const amountPaid = parseFloat(document.querySelector('input[name="amount_paid"]').value);
    const total = totalAmount; // Use the value passed from the Blade template
    if (amountPaid < total) {
        alert('Amount paid cannot be less than the total amount.');
        return false;
    }
    return true;
}

// Attach event listener for the amount paid input
document.addEventListener('DOMContentLoaded', function() {
    document.querySelector('input[name="amount_paid"]').addEventListener('input', calculateChange);
});


