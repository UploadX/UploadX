function loadDoc() {

    hljs.configure({
        tabReplace: '    ',
        useBR: true

    })

    hljs.initHighlightingOnLoad();

    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (xhttp.readyState == 4 && xhttp.status == 200) {
            document.getElementById("code").innerHTML = HtmlEncode(xhttp.responseText);
            hljs.initHighlighting.called = false;
            hljs.initHighlighting();
        }
    };

    var location = window.location.href.toString();
    console.log("loading " + location + "/view");
    xhttp.open("GET", location + "/view", true);
    xhttp.send();
}

function HtmlEncode(s) {
    var el = document.createElement("div");
    el.innerText = el.textContent = s;
    s = el.innerHTML;
    return s;
}