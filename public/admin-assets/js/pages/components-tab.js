if($('#tab-wrapper-1').length) {
    window.addEventListener("app:mounted", (function() {
        new Tab(document.querySelector("#tab-wrapper-1"))
    }), {
        once: !0
    });
}