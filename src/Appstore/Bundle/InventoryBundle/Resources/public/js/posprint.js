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

function jsPostPrintx(data) {

    if(typeof EasyPOSPrinter == 'undefined') {
        alert("Printer library not found");
        return;
    }
    EasyPOSPrinter.raw(data);

    EasyPOSPrinter.cut();
    EasyPOSPrinter.print(function(r, x){
        console.log(r)
    });

}

