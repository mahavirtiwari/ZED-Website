<?php

namespace App\Cms;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

/**
 * One form field of a CMS resource: how it is shown, validated and saved.
 *
 *   Field::text('faq_question', 'Question')->required()->max(300)
 *   Field::image('slider_slug', 'Image')->dir('slider')->urlStyle('root')->nameColumn('slider_name')
 *   Field::select('post_category', 'Type', [1 => 'Article', 2 => 'Link'])
 *   Field::editor('page_description', 'Content')->showIf('page_type', ['1', '2'])
 */
class Field
{
    public string $type;

    public string $name;

    public string $label;

    public bool $required = false;

    public ?int $max = null;

    public ?string $help = null;

    public int $width = 12;

    /** @var array<int|string, string>|Closure */
    public array|Closure $options = [];

    /** @var array<int, string> */
    public array $extraRules = [];

    // uploads
    public string $dir = 'uploads';

    /** 'relative' stores "uploads/x.png", 'root' stores "/uploads/x.png" */
    public string $urlStyle = 'relative';

    public ?string $nameColumn = null;

    /** @var array<int, string> */
    public array $fileTypes = [];

    public int $maxKb = 5120;

    // multiselect
    public bool $ordered = false;

    // conditional display: [field, [values]]
    public ?array $showIf = null;

    public mixed $default = null;

    public ?string $placeholder = null;

    final public function __construct(string $type, string $name, string $label)
    {
        $this->type = $type;
        $this->name = $name;
        $this->label = $label;
    }

    public static function text(string $name, string $label): static
    {
        return (new static('text', $name, $label))->max(250);
    }

    public static function url(string $name, string $label): static
    {
        return (new static('url', $name, $label))->max(1000);
    }

    public static function email(string $name, string $label): static
    {
        return (new static('email', $name, $label))->max(150);
    }

    public static function number(string $name, string $label): static
    {
        return new static('number', $name, $label);
    }

    public static function textarea(string $name, string $label): static
    {
        return new static('textarea', $name, $label);
    }

    /** Rich text (Trumbowyg). Stored as HTML. */
    public static function editor(string $name, string $label): static
    {
        return new static('editor', $name, $label);
    }

    public static function date(string $name, string $label): static
    {
        return new static('date', $name, $label);
    }

    /** @param array<int|string, string>|Closure $options */
    public static function select(string $name, string $label, array|Closure $options): static
    {
        $field = new static('select', $name, $label);
        $field->options = $options;

        return $field;
    }

    /** Several values stored as a comma list (e.g. "3,7,12"). @param array<int|string, string>|Closure $options */
    public static function multiselect(string $name, string $label, array|Closure $options): static
    {
        $field = new static('multiselect', $name, $label);
        $field->options = $options;

        return $field;
    }

    /** 1/0 switch, e.g. status Active / Inactive. */
    public static function toggle(string $name, string $label): static
    {
        return (new static('toggle', $name, $label))->default(1);
    }

    public static function image(string $name, string $label): static
    {
        $field = new static('image', $name, $label);
        $field->fileTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

        return $field;
    }

    public static function file(string $name, string $label): static
    {
        $field = new static('file', $name, $label);
        $field->fileTypes = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'csv', 'txt', 'zip', 'jpg', 'jpeg', 'png', 'gif'];
        $field->maxKb = 20480;

        return $field;
    }

    public function required(bool $required = true): static
    {
        $this->required = $required;

        return $this;
    }

    public function max(?int $max): static
    {
        $this->max = $max;

        return $this;
    }

    public function help(string $help): static
    {
        $this->help = $help;

        return $this;
    }

    public function width(int $columns): static
    {
        $this->width = $columns;

        return $this;
    }

    /** @param array<int, string> $rules */
    public function rules(array $rules): static
    {
        $this->extraRules = $rules;

        return $this;
    }

    public function dir(string $dir): static
    {
        $this->dir = trim($dir, '/');

        return $this;
    }

    public function urlStyle(string $style): static
    {
        $this->urlStyle = $style;

        return $this;
    }

    /** Column that also gets the stored file name (e.g. slider_name). */
    public function nameColumn(string $column): static
    {
        $this->nameColumn = $column;

        return $this;
    }

    /** @param array<int, string> $types */
    public function types(array $types): static
    {
        $this->fileTypes = $types;

        return $this;
    }

    public function maxKb(int $kb): static
    {
        $this->maxKb = $kb;

        return $this;
    }

    public function ordered(bool $ordered = true): static
    {
        $this->ordered = $ordered;

        return $this;
    }

    /** Only show (and validate/save) this field when $field has one of $values. */
    public function showIf(string $field, array $values): static
    {
        $this->showIf = [$field, array_map('strval', $values)];

        return $this;
    }

    public function default(mixed $value): static
    {
        $this->default = $value;

        return $this;
    }

    public function placeholder(string $text): static
    {
        $this->placeholder = $text;

        return $this;
    }

    /** @return array<int|string, string> */
    public function options(): array
    {
        return $this->options instanceof Closure ? ($this->options)() : $this->options;
    }

    public function isUpload(): bool
    {
        return $this->type === 'image' || $this->type === 'file';
    }

    /** Is the field shown for this request (showIf)? */
    public function isActive(Request $request, ?Model $record): bool
    {
        if ($this->showIf === null) {
            return true;
        }
        [$other, $values] = $this->showIf;
        $value = $request->has($other) ? $request->input($other) : $record?->getAttribute($other);

        return in_array((string) $value, $values, true);
    }

    /** Laravel validation rules for this field. */
    public function validationRules(bool $creating): array
    {
        $rules = [];
        switch ($this->type) {
            case 'image':
            case 'file':
                $rules[] = $this->required && $creating ? 'required' : 'nullable';
                $rules[] = 'file';
                $rules[] = 'mimes:'.implode(',', $this->fileTypes);
                $rules[] = 'max:'.$this->maxKb;
                if ($this->type === 'image') {
                    $rules[] = 'image';
                }
                break;
            case 'multiselect':
                $rules[] = $this->required ? 'required' : 'nullable';
                $rules[] = 'array';
                break;
            case 'toggle':
                $rules[] = 'nullable';
                $rules[] = 'in:0,1';
                break;
            case 'number':
                $rules[] = $this->required ? 'required' : 'nullable';
                $rules[] = 'integer';
                break;
            case 'date':
                $rules[] = $this->required ? 'required' : 'nullable';
                $rules[] = 'date';
                break;
            case 'email':
                $rules[] = $this->required ? 'required' : 'nullable';
                $rules[] = 'email';
                break;
            case 'url':
                $rules[] = $this->required ? 'required' : 'nullable';
                $rules[] = 'string';
                break;
            case 'select':
                $rules[] = $this->required ? 'required' : 'nullable';
                $rules[] = 'in:'.implode(',', array_map('strval', array_keys($this->options())));
                break;
            default:
                $rules[] = $this->required ? 'required' : 'nullable';
                $rules[] = 'string';
        }
        if ($this->max !== null && in_array($this->type, ['text', 'url', 'email', 'textarea'], true)) {
            $rules[] = 'max:'.$this->max;
        }

        return array_merge($rules, $this->extraRules);
    }

    /**
     * Value to store, or null to leave the column unchanged (no new upload).
     */
    public function valueFrom(Request $request): mixed
    {
        switch ($this->type) {
            case 'toggle':
                return $request->boolean($this->name) ? 1 : 0;
            case 'multiselect':
                return implode(',', array_filter(array_map('intval', (array) $request->input($this->name, []))));
            case 'image':
            case 'file':
                $file = $request->file($this->name);

                return $file instanceof UploadedFile ? $this->store($file) : null;
            case 'number':
                $value = $request->input($this->name);

                return $value === null || $value === '' ? 0 : (int) $value;
            case 'date':
                $value = $request->input($this->name);

                return $value === null || $value === '' ? null : $value;
            default:
                return (string) $request->input($this->name, '');
        }
    }

    /**
     * Save an upload under the shared storage root with a random name.
     * @return array{path: string, name: string}
     */
    public function store(UploadedFile $file): array
    {
        $extension = strtolower($file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'bin');
        $name = Str::random(32).'.'.$extension;
        $file->move(rtrim(config('zed.storage_root'), '/\\').DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $this->dir), $name);
        $path = $this->dir.'/'.$name;

        return ['path' => $this->urlStyle === 'root' ? '/'.$path : $path, 'name' => $name];
    }

    /** Current value for the form. */
    public function formValue(?Model $record): mixed
    {
        $old = old($this->name);
        if ($old !== null) {
            return $old;
        }
        if ($record !== null && $record->exists) {
            return $record->getAttribute($this->name);
        }

        return $this->default;
    }
}
