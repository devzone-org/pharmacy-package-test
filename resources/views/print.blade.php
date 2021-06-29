<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Receipt Printing</title>
</head>
<body>

<div style="text-align:center">
    <h1>Receipt Printing...</h1>
    <p>This dialog will close automatically.</p>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bluebird/3.3.5/bluebird.min.js"></script>
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>

<!--IMPORTANT: BE SURE YOU HONOR THIS JS LOAD ORDER-->
<script src="https://jsprintmanager.azurewebsites.net/scripts/cptable.js"></script>
<script src="https://jsprintmanager.azurewebsites.net/scripts/cputils.js"></script>
<script src="https://jsprintmanager.azurewebsites.net/scripts/JSESCPOSBuilder.js"></script>
<script src="https://jsprintmanager.azurewebsites.net/scripts/JSPrintManager.js"></script>
<script src="https://jsprintmanager.azurewebsites.net/scripts/zip.js"></script>
<script src="https://jsprintmanager.azurewebsites.net/scripts/zip-ext.js"></script>
<script src="https://jsprintmanager.azurewebsites.net/scripts/deflate.js"></script>

<script>
    var clientPrinters = null;
    var _this = this;

    //WebSocket settings
    JSPM.JSPrintManager.auto_reconnect = true;
    JSPM.JSPrintManager.start();
    JSPM.JSPrintManager.WS.onStatusChanged = function () {
        if (jspmWSStatus()) {
            doPrinting();
            //get client installed printers
            // JSPM.JSPrintManager.getPrinters().then(function (printersList) {
            //     clientPrinters = printersList;
            //     var options = '';
            //     for (var i = 0; i < clientPrinters.length; i++) {
            //         options += '<option>' + clientPrinters[i] + '</option>';
            //     }
            //     $('#printerName').html(options);
            //
            // });
        }
    };

    //Check JSPM WebSocket status
    function jspmWSStatus() {
        if (JSPM.JSPrintManager.websocket_status == JSPM.WSStatus.Open)
            return true;
        else if (JSPM.JSPrintManager.websocket_status == JSPM.WSStatus.Closed) {
            console.warn(
                'JSPrintManager (JSPM) is not installed or not running! Download JSPM Client App from https://neodynamic.com/downloads/jspm'
            );
            return false;
        } else if (JSPM.JSPrintManager.websocket_status == JSPM.WSStatus.Blocked) {
            alert('JSPM has blocked this website!');
            return false;
        }
    }

    //Do printing...
    function doPrinting() {
        if (jspmWSStatus()) {

            // Gen sample label featuring logo/image, barcode, QRCode, text, etc by using JSESCPOSBuilder.js

            var escpos = Neodynamic.JSESCPOSBuilder;
            var doc = new escpos.Document();


                    // logo image loaded, create ESC/POS commands

                    var escposCommands = doc
                        .align(escpos.TextAlignment.Center)

                        .font(escpos.FontFamily.A)
                        .feed()
                        .text("{{ $print['address_1'] }}")
                        .text("{{ $print['address_2'] }}")
                        .feed()
                        .style([escpos.FontStyle.Bold])
                        .text("{{ $print['invoice_no']  }}")
                        .style([escpos.FontStyle.Normal])
                        .feed()
                        .align()
                        .text("{{ $print['sale_by'] }}")
                        .text("{{ $print['sale_at'] }}")
                        .feed()
                        .text("================================================")
                        .text("{{ $print['heading'] }}")
                        .text("================================================")
                        .text("{{ $print['inner'] }}")
                        .text("------------------------------------------------")
                        .text("{{ $print['sub_total'] }}")
                        .text("{{ $print['discount'] }}")
                        .text("{{ $print['gross_total'] }}")
                        .text("{{ $print['refund'] }}")
                        .text("{{ $print['net_total'] }}")
                        .feed(2)
                        .text("{{ $print['developer'] }}")
                        .text("{{ $print['developer_phone'] }}")

                        .feed(2)
                        .cut()
                        .generateUInt8Array();


                    // create ClientPrintJob
                    var cpj = new JSPM.ClientPrintJob();

                    // Set Printer info
                    //var myPrinter = new JSPM.InstalledPrinter($('#printerName').val());
                    cpj.clientPrinter = new JSPM.DefaultPrinter();

                    // Set the ESC/POS commands
                    cpj.binaryPrinterCommands = escposCommands;

                    // Send print job to printer!
                    cpj.sendToClient();
close();

        }
    }
</script>

</body>
</html>
