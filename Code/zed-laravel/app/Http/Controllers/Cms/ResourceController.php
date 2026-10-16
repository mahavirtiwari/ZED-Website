<?php

namespace App\Http\Controllers\Cms;

use App\Cms\Field;
use App\Cms\Resource;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * List / add / edit / delete screens for every module in config/cms.php.
 * Deleting sets status 2 (the ZED convention); rows are never removed.
 */
class ResourceController extends Controller
{
    /** @return class-string<Resource> */
    private function resource(string $slug): string
    {
        foreach (config('cms.resources') as $class) {
            if ($class::$slug === $slug) {
                abort_unless($class::allows(auth()->user()), 403);

                return $class;
            }
        }
        abort(404);
    }

    public function index(Request $request, string $slug): View
    {
        $resource = $this->resource($slug);
        $query = $resource::query();
        $search = trim((string) $request->query('q', ''));
        if ($search !== '' && $resource::searchable() !== []) {
            $query->where(function ($q) use ($resource, $search) {
                foreach ($resource::searchable() as $column) {
                    $q->orWhere($column, 'like', '%'.$search.'%');
                }
            });
        }

        return view('cms.resource.index', [
            'resource' => $resource,
            'records' => $query->paginate($resource::$perPage)->withQueryString(),
            'search' => $search,
        ]);
    }

    public function create(string $slug): View
    {
        $resource = $this->resource($slug);
        abort_unless($resource::$canCreate, 404);

        return view('cms.resource.form', ['resource' => $resource, 'record' => new $resource::$model]);
    }

    public function store(Request $request, string $slug): RedirectResponse
    {
        $resource = $this->resource($slug);
        abort_unless($resource::$canCreate, 404);

        return $this->save($request, $resource, new $resource::$model);
    }

    public function edit(string $slug, string $id): View
    {
        $resource = $this->resource($slug);

        return view('cms.resource.form', ['resource' => $resource, 'record' => $resource::$model::query()->findOrFail($id)]);
    }

    public function update(Request $request, string $slug, string $id): RedirectResponse
    {
        $resource = $this->resource($slug);

        return $this->save($request, $resource, $resource::$model::query()->findOrFail($id));
    }

    public function destroyMany(Request $request, string $slug): RedirectResponse
    {
        $resource = $this->resource($slug);
        abort_unless($resource::$canDelete, 404);
        $ids = array_filter(array_map('intval', (array) $request->input('ids', [])));
        if ($ids === []) {
            return back()->with('error', 'Select at least one '.strtolower($resource::$singular).' to delete.');
        }
        $deleted = 0;
        foreach ($resource::$model::query()->whereKey($ids)->get() as $record) {
            $deleted += (int) $record->markDeleted();
        }

        return back()->with('status', $deleted === 1 ? '1 '.strtolower($resource::$singular).' deleted.' : $deleted.' '.strtolower($resource::$title).' deleted.');
    }

    /** "Upload several images" on the gallery list. */
    public function uploadMany(Request $request, string $slug): RedirectResponse
    {
        $resource = $this->resource($slug);
        abort_unless($resource::$multiUpload, 404);
        $field = collect($resource::fields())->first(fn (Field $f) => $f->type === 'image');
        $request->validate([
            'files' => ['required', 'array', 'max:50'],
            'files.*' => ['file', 'image', 'mimes:'.implode(',', $field->fileTypes), 'max:'.$field->maxKb],
        ]);
        foreach ($request->file('files') as $file) {
            $stored = $field->store($file);
            $record = new $resource::$model;
            $record->setAttribute($field->name, $stored['path']);
            if ($field->nameColumn) {
                $record->setAttribute($field->nameColumn, $stored['name']);
            }
            $resource::beforeSave($record, new Request);
            $record->save();
        }

        return back()->with('status', count($request->file('files')).' images uploaded.');
    }

    /** @param class-string<Resource> $resource */
    private function save(Request $request, string $resource, Model $record): RedirectResponse
    {
        $creating = ! $record->exists;
        $fields = array_filter($resource::fields(), fn (Field $f) => $f->isActive($request, $record));

        $rules = [];
        $labels = [];
        foreach ($fields as $field) {
            $rules[$field->name] = $field->validationRules($creating);
            $labels[$field->name] = $field->label;
        }
        Validator::make($request->all(), $rules, [], $labels)->validate();

        foreach ($fields as $field) {
            $value = $field->valueFrom($request);
            if ($field->isUpload()) {
                if ($value === null) {
                    continue; // no new file: keep the current one
                }
                $record->setAttribute($field->name, $value['path']);
                if ($field->nameColumn) {
                    $record->setAttribute($field->nameColumn, $value['name']);
                }

                continue;
            }
            $record->setAttribute($field->name, $value);
        }

        $error = $resource::beforeSave($record, $request);
        if ($error !== null) {
            throw ValidationException::withMessages(['_form' => $error]);
        }
        $record->save();

        $message = $resource::$singular.($creating ? ' added.' : ' updated.');

        return $request->boolean('stay')
            ? redirect()->route('cms.resource.edit', [$resource::$slug, $record->getKey()])->with('status', $message)
            : redirect()->route('cms.resource.index', $resource::$slug)->with('status', $message);
    }
}
