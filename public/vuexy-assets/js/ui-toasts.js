"use strict";
! function() {
    const e = document.querySelector(".toast-ex"),
        o = document.querySelector(".toast-placement-ex"),
        t = document.querySelector("#showToastAnimation"),
        s = document.querySelector("#showToastPlacement");
    let a, n, i, r, l;

    function c(t) {
        t && null !== t._element && (o && (o.classList.remove(a), o.querySelector(".ti").classList.remove(a), DOMTokenList.prototype.remove.apply(o.classList, i)), e && (e.classList.remove(a, n), e.querySelector(".ti").classList.remove(a)), t.dispose())
    }
    t && (t.onclick = function() {
        r && c(r), a = document.querySelector("#selectType").value, n = document.querySelector("#selectAnimation").value, e.classList.add(n), e.querySelector(".ti").classList.add(a), (r = new bootstrap.Toast(e)).show()
    }), s && (s.onclick = function() {
        l && c(l), a = document.querySelector("#selectTypeOpt").value, i = document.querySelector("#selectPlacement").value.split(" "), o.querySelector(".ti").classList.add(a), DOMTokenList.prototype.add.apply(o.classList, i), (l = new bootstrap.Toast(o)).show()
    })
}(), $(function() {
    var k, y = -1,
        f = 0;
    $("#closeButton").on("click", function() {
        $(this).is(":checked") ? $("#addBehaviorOnToastCloseClick").prop("disabled", !1) : ($("#addBehaviorOnToastCloseClick").prop("disabled", !0), $("#addBehaviorOnToastCloseClick").prop("checked", !1))
    }), $("#showtoast").on("click", function() {
        var t = $("#toastTypeGroup input:radio:checked").val(),
            e = "rtl" === $("html").at__("dir"),
            o = $("#message").val(),
            s = $("#title").val() || "",
            a = $("#showDuration"),
            n = $("#hideDuration"),
            i = $("#timeOut"),
            r = $("#extendedTimeOut"),
            l = $("#showEasing"),
            c = $("#hideEasing"),
            p = $("#showMethod"),
            u = $("#hideMethod"),
            d = f++,
            h = $("#addClear").prop("checked"),
            m = void 0 === toastr.options.positionClass ? "toast-top-right" : toastr.options.positionClass,
            o = (toastr.options = {
                maxOpened: 1,
                autoDismiss: !0,
                closeButton: $("#closeButton").prop("checked"),
                debug: $("#debugInfo").prop("checked"),
                newestOnTop: $("#newestOnTop").prop("checked"),
                progressBar: $("#progressBar").prop("checked"),
                positionClass: $("#positionGroup input:radio:checked").val() || "toast-top-right",
                preventDuplicates: $("#preventDuplicates").prop("checked"),
                onclick: null,
                rtl: e
            }, m != toastr.options.positionClass && (toastr.options.hideDuration = 0, toastr.clear()), $("#addBehaviorOnToastClick").prop("checked") && (toastr.options.onclick = function() {
                alert("You can perform some custom action after a toast goes away")
            }), $("#addBehaviorOnToastCloseClick").prop("checked") && (toastr.options.onCloseClick = function() {
                alert("You can perform some custom action when the close button is clicked")
            }), a.val().length && (toastr.options.showDuration = parseInt(a.val())), n.val().length && (toastr.options.hideDuration = parseInt(n.val())), i.val().length && (toastr.options.timeOut = h ? 0 : parseInt(i.val())), r.val().length && (toastr.options.extendedTimeOut = h ? 0 : parseInt(r.val())), l.val().length && (toastr.options.showEasing = l.val()), c.val().length && (toastr.options.hideEasing = c.val()), p.val().length && (toastr.options.showMethod = p.val()), u.val().length && (toastr.options.hideMethod = u.val()), h && (e = (e = o) || "Clear itself?", o = e += '<br /><br /><button type="button" class="btn btn-secondary clear">Yes</button>', toastr.options.tapToDismiss = !1), o || (m = ["Don't be pushed around by the fears in your mind. Be led by the dreams in your heart.", '<div class="mb-3"><input class="input-small form-control" value="Textbox"/>&nbsp;<a href="http://johnpapa.net" target="_blank">This is a hyperlink</a></div><div class="d-flex"><button type="button" id="okBtn" class="btn btn-primary btn-sm me-2">Close me</button><button type="button" id="surpriseBtn" class="btn btn-sm btn-secondary">Surprise me</button></div>', "Live the Life of Your Dreams", "Believe in Your Self!", "Be mindful. Be grateful. Be positive.", "Accept yourself, love yourself!"])[y = ++y === m.length ? 0 : y]),
            v = toastr[t](o, s);
        void 0 !== (k = v) && (v.find("#okBtn").length && v.delegate("#okBtn", "click", function() {
            alert("you clicked me. i was toast #" + d + ". goodbye!"), v.remove()
        }), v.find("#surpriseBtn").length && v.delegate("#surpriseBtn", "click", function() {
            alert("Surprise! you clicked me. i was toast #" + d + ". You could perform an action here.")
        }), v.find(".clear").length) && v.delegate(".clear", "click", function() {
            toastr.clear(v, {
                force: !0
            })
        })
    }), $("#clearlasttoast").on("click", function() {
        toastr.clear(k)
    }), $("#cleartoasts").on("click", function() {
        toastr.clear()
    })
});