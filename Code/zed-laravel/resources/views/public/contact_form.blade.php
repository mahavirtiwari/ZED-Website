{{-- The site's own Contact Us form (CMS > Settings > Security > Contact Us form = Website form) --}}
<div class="zed-contact-form">
    <h3>Send us your query</h3>
    @if ($errors->any())
        <div class="alert alert-danger">Please correct the highlighted fields and try again.</div>
    @endif
    <form action="{{ url('contact-us') }}" method="post" id="zed-contact-form" novalidate>
        @csrf
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group @error('name') has-error @enderror">
                    <label for="cf-name">Name <span class="text-danger">*</span></label>
                    <input type="text" id="cf-name" name="name" class="form-control" maxlength="100" required value="{{ old('name') }}">
                    @error('name')<span class="help-block">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group @error('email') has-error @enderror">
                    <label for="cf-email">Email <span class="text-danger">*</span></label>
                    <input type="email" id="cf-email" name="email" class="form-control" maxlength="150" required value="{{ old('email') }}">
                    @error('email')<span class="help-block">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group @error('phone') has-error @enderror">
                    <label for="cf-phone">Phone</label>
                    <input type="tel" id="cf-phone" name="phone" class="form-control" maxlength="100" value="{{ old('phone') }}">
                    @error('phone')<span class="help-block">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group @error('enterprise_name') has-error @enderror">
                    <label for="cf-enterprise">Enterprise name</label>
                    <input type="text" id="cf-enterprise" name="enterprise_name" class="form-control" maxlength="200" value="{{ old('enterprise_name') }}">
                    @error('enterprise_name')<span class="help-block">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group @error('query_category') has-error @enderror">
                    <label for="cf-category">Query category <span class="text-danger">*</span></label>
                    <select id="cf-category" name="query_category" class="form-control" required>
                        <option value="">Select a category</option>
                        @foreach (config('zed.contact_categories') as $category)
                            <option value="{{ $category }}" @selected(old('query_category') === $category)>{{ $category }}</option>
                        @endforeach
                    </select>
                    @error('query_category')<span class="help-block">{{ $message }}</span>@enderror
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group @error('message') has-error @enderror">
                    <label for="cf-message">Message <span class="text-danger">*</span></label>
                    <textarea id="cf-message" name="message" class="form-control" rows="6" maxlength="3000" required>{{ old('message') }}</textarea>
                    @error('message')<span class="help-block">{{ $message }}</span>@enderror
                </div>
            </div>
            @if ($captcha)
                <div class="col-sm-12">
                    <div class="form-group zed-captcha @error('captcha') has-error @enderror">
                        <label for="cf-captcha">Security code <span class="text-danger">*</span></label>
                        <div class="zed-captcha-row">
                            <img id="cf-captcha-img" src="{{ route('captcha', 'contact') }}?r={{ time() }}" width="190" height="60" alt="Security code image">
                            <button type="button" class="btn btn-default" data-captcha-refresh="cf-captcha-img" data-captcha-input="cf-captcha" title="Show a new code" aria-label="Show a new security code">&#x21bb;</button>
                        </div>
                        <input type="text" id="cf-captcha" name="captcha" class="form-control zed-captcha-input" maxlength="6" required autocomplete="off" autocapitalize="characters" spellcheck="false" placeholder="Enter the code">
                        @error('captcha')<span class="help-block">{{ $message }}</span>@enderror
                    </div>
                </div>
            @endif
            <div class="col-sm-12">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </form>
</div>
<script src="{{ asset('js/zed-captcha.js') }}"></script>
