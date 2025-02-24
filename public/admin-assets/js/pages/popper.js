if ($('#popper-0').length) {
    window.addEventListener("app:mounted", (function () {
        var popperConfig = {
            placement: "bottom-start",
            modifiers: [{
                name: "offset",
                options: {
                    offset: [0, 4]
                }
            }]
        };
        for (let key = 0; key < 12; key++) {
            new Popper("#popper-" + key, ".popper-ref", ".popper-root", popperConfig);
        }
    }), {
        once: !0
    });
}