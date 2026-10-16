{{-- Captcha field. @param string $form  login | forgot_password --}}
<div class="mb-3">
    <label for="captcha" class="form-label">Security code</label>
    <div class="d-flex align-items-center gap-2 mb-2">
        <img id="captcha-img" class="captcha-img" src="{{ route('captcha', $form) }}?r={{ time() }}" width="190" height="60" alt="Security code image">
        <button type="button" class="btn btn-outline-secondary" data-captcha-refresh="captcha-img" data-captcha-input="captcha" title="Show a new code" aria-label="Show a new security code"><i class="bi bi-arrow-clockwise"></i></button>
    </div>
    <input type="text" name="captcha" id="captcha" class="form-control captcha-input @error('captcha') is-invalid @enderror" maxlength="6" required autocomplete="off" autocapitalize="characters" spellcheck="false" placeholder="Enter the code">
    @error('captcha')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
