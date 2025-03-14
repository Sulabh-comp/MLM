window.addEventListener("app:mounted", (function() {
    var e = document.querySelector("#postConent");
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
    var n = document.querySelector("#postImages");
    n._filepond = FilePond.create(n);
    var t = document.querySelector("#postAuthor");
    t._tom = new Tom(t, {
        valueField: "id",
        searchField: "title",
        options: [{
            id: 1,
            name: "John Doe",
            job: "Web designer",
            src: "images/200x200.png"
        }, {
            id: 2,
            name: "Emilie Watson",
            job: "Developer",
            src: "images/200x200.png"
        }, {
            id: 3,
            name: "Nancy Clarke",
            job: "Software Engineer",
            src: "images/200x200.png"
        }],
        placeholder: "Select the author",
        render: {
            option: function(e, n) {
                return '<div class="flex space-x-3">\n                      <div class="avatar w-8 h-8">\n                        <img class="rounded-full" src="'.concat(n(e.src), '" alt="avatar"/>\n                      </div>\n                      <div class="flex flex-col">\n                        <span> ').concat(n(e.name), '</span>\n                        <span class="text-xs opacity-80"> ').concat(n(e.job), "</span>\n                      </div>\n                    </div>")
            },
            item: function(e, n) {
                return '<span class="badge rounded-full bg-primary dark:bg-accent text-white p-px mr-2">\n                      <span class="avatar w-6 h-6">\n                        <img class="rounded-full" src="'.concat(n(e.src), '" alt="avatar">\n                      </span>\n                      <span class="mx-2">').concat(n(e.name), "</span>\n                    </span>")
            }
        }
    }), configPostCategory = {
        create: !1,
        sortField: {
            field: "text",
            direction: "asc"
        }
    };
    var a = document.querySelector("#postCategory");
    a._tom = new Tom(a, configPostCategory);
    var o = document.querySelector("#postTags");
    o._tom = new Tom(o, {
        create: !0
    });
    var r = document.querySelector("#postPublishDate");
    r._datepicker = flatpickr(r)
}), {
    once: !0
});