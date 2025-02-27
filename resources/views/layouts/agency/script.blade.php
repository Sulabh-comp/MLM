<script src="{{asset('vuexy-assets/vendor/libs/jquery/jquery.js')}}"></script>
<script src="{{asset('vuexy-assets/vendor/libs/popper/popper.js')}}"></script>
<script src="{{asset('vuexy-assets/vendor/js/bootstrap.js')}}"></script>
<script src="{{asset('vuexy-assets/vendor/libs/node-waves/node-waves.js')}}"></script>
<script src="{{asset('vuexy-assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js')}}"></script>
<script src="{{asset('vuexy-assets/vendor/libs/hammer/hammer.js')}}"></script>
<script src="{{asset('vuexy-assets/vendor/libs/i18n/i18n.js')}}"></script>
<script src="{{asset('vuexy-assets/vendor/libs/typeahead-js/typeahead.js')}}"></script>
<script src="{{asset('vuexy-assets/vendor/js/menu.js')}}"></script>
<script src="{{asset('vuexy-assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js')}}"></script>
<script src="{{asset('vuexy-assets/vendor/libs/moment/moment.js')}}"></script>
<script src="{{asset('vuexy-assets/vendor/libs/flatpickr/flatpickr.js')}}"></script>
<script src="{{asset('vuexy-assets/vendor/libs/bootstrap-select/bootstrap-select.js')}}"></script>
<script src="{{asset('vuexy-assets/js/main.js')}}"></script>
<script src="{{asset('vuexy-assets/vendor/libs/toastr/toastr.js')}}"></script>
<script src="{{asset('vuexy-assets/vendor/libs/bs-stepper/bs-stepper.js')}}"></script>
<script src="{{asset('vuexy-assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('vuexy-assets/js/form-wizard-icons.js')}}"></script>
<script src="{{asset('vuexy-assets/js/form-layouts.js')}}"></script>
<script src="{{asset('vuexy-assets/vendor/libs/cleavejs/cleave.js')}}"></script>
<script src="{{asset('vuexy-assets/vendor/libs/cleavejs/cleave-phone.js')}}"></script>
<script src="{{asset('vuexy-assets/vendor/libs/quill/katex.js')}}"></script>
<script src="{{asset('vuexy-assets/vendor/libs/quill/quill.js')}}"></script>
<script src="{{asset('vuexy-assets/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js')}}"></script>

<script type="text/javascript">

function notifier(message, type) {

    toastr.options = {

      "closeButton": true,

      "debug": false,

      "newestOnTop": true,

      "progressBar": true,

      "positionClass": "toast-top-right",

      "preventDuplicates": true,

      "onclick": null,

      "showDuration": "300",

      "hideDuration": "1000",

      "timeOut": "5000",

      "extendedTimeOut": "1000",

      "showEasing": "swing",

      "hideEasing": "linear",

      "showMethod": "fadeIn",

      "hideMethod": "fadeOut"

    }

    switch(type) {

        case "success":
            alert(message);

            toastr.success(message, "{{ env('SITE_NAME') }}", {"iconClass": 'toast-success-background'});

        break;

        case "error":

            alert(message);

            toastr.error(message, "{{ env('SITE_NAME') }}", {"iconClass": 'toast-error-background'});

        break;

    }

}

if ($('#quill-editor').length) {
    var quill_editor = new Quill("#quill-editor", {
        bounds: "#quill-editor",
        placeholder: "Add Content here...",
        modules: {
            formula: !0,
            toolbar: [
                [{
                    font: []
                }, {
                    size: []
                }],
                ["bold", "italic", "underline", "strike"],
                [{
                    color: []
                }, {
                    background: []
                }],
                [{
                    script: "super"
                }, {
                    script: "sub"
                }],
                [{
                    header: "1"
                }, {
                    header: "2"
                }, "blockquote", "code-block"],
                [{
                    list: "ordered"
                }, {
                    list: "bullet"
                }, {
                    indent: "-1"
                }, {
                    indent: "+1"
                }],
                [{
                    direction: "rtl"
                }],
                ["link", "formula"],
                ["clean"]
            ]
        },
        theme: "snow"
    });
}

if($("#scroll-bar").length) {
    document.addEventListener('DOMContentLoaded', function() {
        new PerfectScrollbar('#scroll-bar');
    });
}

if($('#checkAll').length && $('.checkSingle').length) {
    $('#checkAll').click(function () {
       $('.checkSingle').prop('checked', $(this).prop('checked'));
    });

    $('.checkSingle').click(function () {
       var checked = $('.checkSingle:checked').length == $('.checkSingle').length;
       $('#checkAll').prop('checked', checked);
    });

    $('.bulkAction').click(function() {

        var model_ids = $('.bulkModelId input:checkbox:checked').map(function () {
            return $(this).data('model-id');
        }).get();

        if(!model_ids.length) {
          notifier("Please select any rows to perform the bulk action.", "error");
          return false;
        }

        var actionType = $(this).data('action-type');
        var actionText = $(this).data('action-text');

        $('#modelIds').val(model_ids);
        $('#actionType').val(actionType);
        var confirmation =  confirm(actionText);
        if(confirmation) {
            $("#bulkActionForm").submit();
        }
    });
}

@if(session('error'))
    notifier("{{ session('error') }}", "error")
@endif

@if(session('success'))
    notifier("{{ session('success') }}", "success")
@endif

@isset($errors)
    @if($errors->any() && (!session('card_transaction_store_error') && !session('bank_transaction_store_error') && !session('upi_transaction_store_error')))
        @foreach($errors->all() as $key => $error)
            notifier("{{ $error }}", "error")
        @endforeach
    @endif
@endisset

@if(isset($page))
    $('#' + "{{ $page }}").addClass("active open");
@endif

@if(isset($sub_page))
    $('#' + "{{ $sub_page }}").addClass("active");
@endif

const dropdowns = document.querySelectorAll('.dropdown-toggle')
const dropdown = [...dropdowns].map((dropdownToggleEl) => new bootstrap.Dropdown(dropdownToggleEl, {
    popperConfig(defaultBsPopperConfig) {
        return { ...defaultBsPopperConfig, strategy: 'fixed' };
    }
}));

if($(".copy-button").length) {
    setCopyButton();
}

function setCopyButton() {

    const copyButtons = document.querySelectorAll('.copy-button');
    copyButtons.forEach(button => {
      button.addEventListener('click', function() {
          const textToCopy = this.getAttribute('data-copy');
          copyTextToClipboard(textToCopy);
      });
    });

    function copyTextToClipboard(text) {
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        notifier("{{__('copied_success')}}", "success");
        textArea.remove();
    }
}

$('input:password').keypress(function(e) {
    if(e.keyCode == 32) {
        var errorMessage = e.delegateTarget.name == "cvv" ? "{{ __('cvv_regex_error') }}" : "{{ __('password_regex_error') }}";
        notifier(errorMessage, "error");
        return false;
    }
});

function handleFormOnSubmit(id, text) {
    $('#' + id).at__("disabled", true);
    $('#' + id).text(text);
    $('#' + id).append(`&nbsp;<div class="spinner-border" role="status"></div>`);
}
</script>
