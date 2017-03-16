(function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = "http://localhost.xiidea.net:9432/assets/easy-print-server.js";
    fjs.parentNode.insertBefore(js, fjs);
}(document, "script", "xiidea-epp-sdk"));

function helloPrint() {

    if(typeof EasyPOSPrinter == 'undefined') {
        alert("Printer library not found");
        return;
    }

    EasyPOSPrinter.text("Hello Printer!");
    EasyPOSPrinter.feed(2);
    EasyPOSPrinter.cut();
    EasyPOSPrinter.print(function(r, x){
        console.log(r)
    });
}

function jsPostPrint(data) {

    console.log(document.getElementById("print-textarea").value);
    if(typeof EasyPOSPrinter == 'undefined') {
        alert("Printer library not found");
        return;
    }
    var date = date('l jS \of F Y h:i:s A');

    EasyPOSPrinter.selectPrintMode(32);
    EasyPOSPrinter.setJustification(1);
    EasyPOSPrinter.text(document.getElementById("Shop Online BD").value);
    EasyPOSPrinter.selectPrintMode();
    EasyPOSPrinter.text(document.getElementById("Emporium Plaza Dhanmondi").value);
    EasyPOSPrinter.feed(2);

    /* Title of receipt */
    EasyPOSPrinter.setEmphasis(true);
    EasyPOSPrinter.text(document.getElementById("SALES INVOICE").value);
    EasyPOSPrinter.setEmphasis(false);

    /* Barcode Print */
    EasyPOSPrinter.selectPrintMode (48);
    EasyPOSPrinter.text ( "\n" );
    EasyPOSPrinter.selectPrintMode ();
    EasyPOSPrinter.setBarcodeHeight (80);
    var hri = [2];
    EasyPOSPrinter.feed();

    for (i = 0; i < hri.length; i++) {

        EasyPOSPrinter.selectPrintMode ();
        EasyPOSPrinter.setJustification(1);
        EasyPOSPrinter.text (hri[i]);
        EasyPOSPrinter.setBarcodeTextPosition (i);
        EasyPOSPrinter.barcode ( "100120170226", 67);
        EasyPOSPrinter.feed ();

    }

    /* Footer */
    EasyPOSPrinter.feed();
    EasyPOSPrinter.setJustification(1);
    EasyPOSPrinter.text("Thank you for shopping at ExampleMart");
    EasyPOSPrinter.text("For trading hours, please visit example.com");
    EasyPOSPrinter.feed();
    EasyPOSPrinter.text(date);

    EasyPOSPrinter.cut();
    EasyPOSPrinter.print(function(r, x){
        console.log(r)
    });

}

