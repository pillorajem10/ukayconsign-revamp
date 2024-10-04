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
