@php
    /** @var \App\Cms\Field $field */
    $id = 'f-'.$field->name;
    $value = $field->formValue($record);
    $invalid = $errors->has($field->name) ? ' is-invalid' : '';
    $wrap = 'col-md-'.$field->width;
    $showIf = $field->showIf ? ' data-show-if="'.e($field->showIf[0]).'" data-show-values="'.e(implode(',', $field->showIf[1])).'"' : '';
    $describedBy = $field->help ? ' aria-describedby="'.$id.'-help"' : '';
@endphp
<div class="{{ $wrap }}"{!! $showIf !!}>
    @if ($field->type === 'toggle')
        <div class="form-check form-switch mt-2">
            <input type="hidden" name="{{ $field->name }}" value="0">
            <input class="form-check-input{{ $invalid }}" type="checkbox" role="switch" id="{{ $id }}" name="{{ $field->name }}" value="1" @checked((int) $value === 1){!! $describedBy !!}>
            <label class="form-check-label" for="{{ $id }}">{{ $field->label }}</label>
        </div>
    @else
        <label class="form-label" for="{{ $id }}">{{ $field->label }}@if ($field->required) <span class="text-danger" aria-hidden="true">*</span>@endif</label>

        @switch($field->type)
            @case('textarea')
                <textarea class="form-control{{ $invalid }}" id="{{ $id }}" name="{{ $field->name }}" rows="4" @if ($field->max) maxlength="{{ $field->max }}" @endif{!! $describedBy !!}>{{ $value }}</textarea>
                @break

            @case('editor')
                <textarea class="form-control{{ $invalid }}" id="{{ $id }}" name="{{ $field->name }}" rows="12" data-editor{!! $describedBy !!}>{{ $value }}</textarea>
                @break

            @case('select')
                <select class="form-select{{ $invalid }}" id="{{ $id }}" name="{{ $field->name }}" @if ($field->required) required @endif{!! $describedBy !!}>
                    @unless ($field->required || array_key_exists('', $field->options()))<option value="">—</option>@endunless
                    @foreach ($field->options() as $optionValue => $optionLabel)
                        <option value="{{ $optionValue }}" @selected((string) $value === (string) $optionValue)>{{ $optionLabel }}</option>
                    @endforeach
                </select>
                @break

            @case('multiselect')
                @php
                    $selected = is_array($value) ? array_map('strval', $value) : array_filter(explode(',', (string) $value), 'strlen');
                    $options = $field->options();
                @endphp
                @if ($field->ordered)
                    {{-- ordered list of choices (menu pages) --}}
                    <div class="ordered-select" data-ordered-select="{{ $field->name }}">
                        <ol class="list-group list-group-numbered mb-2" data-ordered-list>
                            @foreach ($selected as $optionValue)
                                @if (isset($options[$optionValue]))
                                    <li class="list-group-item d-flex align-items-center gap-2" data-value="{{ $optionValue }}">
                                        <span class="me-auto">{{ $options[$optionValue] }}</span>
                                        <input type="hidden" name="{{ $field->name }}[]" value="{{ $optionValue }}">
                                        <button type="button" class="btn btn-sm btn-outline-secondary" data-move="up" aria-label="Move up"><i class="bi bi-arrow-up"></i></button>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" data-move="down" aria-label="Move down"><i class="bi bi-arrow-down"></i></button>
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-remove aria-label="Remove"><i class="bi bi-x-lg"></i></button>
                                    </li>
                                @endif
                            @endforeach
                        </ol>
                        <div class="input-group">
                            <select class="form-select" id="{{ $id }}" data-ordered-add{!! $describedBy !!}>
                                <option value="">Add a page…</option>
                                @foreach ($options as $optionValue => $optionLabel)
                                    <option value="{{ $optionValue }}">{{ $optionLabel }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @else
                    <select class="form-select{{ $invalid }}" id="{{ $id }}" name="{{ $field->name }}[]" multiple size="8"{!! $describedBy !!}>
                        @foreach ($options as $optionValue => $optionLabel)
                            <option value="{{ $optionValue }}" @selected(in_array((string) $optionValue, $selected, true))>{{ $optionLabel }}</option>
                        @endforeach
                    </select>
                    <div class="form-text">Hold Ctrl (Cmd on a Mac) to select several.</div>
                @endif
                @break

            @case('image')
            @case('file')
                @if ($value)
                    <div class="mb-2 current-file">
                        @if ($field->type === 'image')
                            <img src="{{ media_url($value) }}" alt="Current image" class="img-thumbnail current-image">
                        @else
                            <a href="{{ media_url($value) }}" target="_blank" rel="noopener"><i class="bi bi-file-earmark"></i> Current file</a>
                        @endif
                    </div>
                @endif
                <input class="form-control{{ $invalid }}" type="file" id="{{ $id }}" name="{{ $field->name }}"
                       accept="{{ $field->type === 'image' ? 'image/*' : '.'.implode(',.', $field->fileTypes) }}" data-preview{!! $describedBy !!}>
                <div class="form-text">{{ strtoupper(implode(', ', $field->fileTypes)) }}, up to {{ round($field->maxKb / 1024) }} MB.@if ($value) Choose a file only to replace the current one.@endif</div>
                @break

            @default
                <input class="form-control{{ $invalid }}" id="{{ $id }}" name="{{ $field->name }}" value="{{ $value }}"
                       type="{{ ['number' => 'number', 'date' => 'date', 'email' => 'email', 'url' => 'url'][$field->type] ?? 'text' }}"
                       @if ($field->max) maxlength="{{ $field->max }}" @endif
                       @if ($field->placeholder) placeholder="{{ $field->placeholder }}" @endif
                       @if ($field->required) required @endif{!! $describedBy !!}>
        @endswitch
    @endif
    @if ($field->help)<div id="{{ $id }}-help" class="form-text">{{ $field->help }}</div>@endif
    @error($field->name)<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
</div>
