window.addEventListener("app:mounted", (function() {
    var e = document.querySelector("#editor");
    e._editor = new Quill(e, {
        modules: {
            toolbar: [
                ["bold", "italic", "underline", "strike"],
                ["blockquote", "code-block"],
                [{
                    header: 1
                }, {
                    header: 2
                }],
                [{
                    list: "ordered"
                }, {
                    list: "bullet"
                }],
                [{
                    script: "sub"
                }, {
                    script: "super"
                }],
                [{
                    indent: "-1"
                }, {
                    indent: "+1"
                }],
                [{
                    direction: "rtl"
                }],
                [{
                    size: ["small", !1, "large", "huge"]
                }],
                [{
                    header: [1, 2, 3, 4, 5, 6, !1]
                }],
                [{
                    color: []
                }, {
                    background: []
                }],
                [{
                    font: []
                }],
                [{
                    align: []
                }],
                ["clean"]
            ]
        },
        placeholder: "Enter your content...",
        theme: "snow"
    });
}), {
    once: !0
});