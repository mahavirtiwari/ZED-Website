{{-- Settings read by assets/js/ajax_actions.js (posts the CSRF token with each AJAX call) --}}
<script>
var ci_csrf_enabled = 1;
var ci_token_name = '_token';
var ci_token_val = @json(csrf_token());
var error_redirect = '';
var debug_enable = false;
</script>
