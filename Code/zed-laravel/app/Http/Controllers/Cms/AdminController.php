<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\GlobalConfig;
use App\Models\NewsBlock;
use App\Models\Page;
use App\Models\Post;
use App\Models\Stakeholder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

/** Dashboard, settings screens, news blocks, contacts and editor image upload. */
class AdminController extends Controller
{
    public function dashboard(): View
    {
        return view('cms.dashboard', [
            'counts' => [
                'Pages' => [Page::query()->active()->count(), route('cms.resource.index', 'pages')],
                'News' => [Post::query()->active()->count(), route('cms.resource.index', 'news')],
                'Partners' => [Stakeholder::query()->active()->count(), route('cms.resource.index', 'partners')],
                'Contact queries' => [Contact::query()->count(), route('cms.contacts.index')],
            ],
        ]);
    }

    private function settingsGroup(string $group): array
    {
        $config = config('cms.settings.'.$group);
        abort_if($config === null, 404);
        abort_unless(auth()->user()->inGroup($config[1]), 403);

        return $config;
    }

    /**
     * Settings screen from config/cms.php "settings". Each field is
     * [label, type, default, help]; types: text, textarea, number, url, image,
     * toggle (show/hide), select:a=A,b=B and heading (a sub-title, not saved).
     */
    public function settings(string $group): View
    {
        [$title, , $fields] = $this->settingsGroup($group);
        $values = GlobalConfig::query()->whereIn('label', array_keys($fields))->pluck('value', 'label')->all();

        return view('cms.settings', compact('group', 'title', 'fields', 'values'));
    }

    public function saveSettings(Request $request, string $group): RedirectResponse
    {
        [$title, , $fields] = $this->settingsGroup($group);
        $rules = [];
        foreach ($fields as $name => $field) {
            $type = $field[1];
            if ($type === 'heading') {
                continue;
            }
            $rules[$name] = match (true) {
                $type === 'number' => ['nullable', 'integer'],
                $type === 'toggle' => ['sometimes', 'in:0,1'],
                $type === 'image' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,gif,webp,svg', 'max:2048'],
                $type === 'url' => ['nullable', 'string', 'max:500', 'regex:#^(https?://|/)#i'],
                str_starts_with($type, 'select:') => ['sometimes', 'required', 'in:'.implode(',', array_keys(self::selectOptions($type)))],
                default => ['nullable', 'string', 'max:5000'],
            };
        }
        $data = $request->validate($rules, ['regex' => 'The :attribute must start with https:// or /.'],
            array_map(fn ($f) => $f[0], $fields));

        $values = [];
        foreach ($fields as $name => $field) {
            $type = $field[1];
            if ($type === 'heading') {
                continue;
            }
            if ($type === 'image') {
                if ($request->hasFile($name)) {
                    $file = $request->file($name);
                    $fileName = Str::random(32).'.'.strtolower($file->getClientOriginalExtension() ?: $file->guessExtension());
                    $file->move(rtrim(config('zed.storage_root'), '/\\').DIRECTORY_SEPARATOR.'uploads'.DIRECTORY_SEPARATOR.'site', $fileName);
                    $values[$name] = '/uploads/site/'.$fileName;
                } elseif ($request->boolean('reset_'.$name)) {
                    $values[$name] = ''; // back to the built-in image
                }

                continue;
            }
            if (! $request->exists($name)) {
                continue; // not on the submitted form: keep the saved value
            }
            $values[$name] = (string) ($data[$name] ?? '');
        }
        GlobalConfig::put($values);

        return back()->with('status', $title.' saved.');
    }

    /** "select:1=Enabled,0=Disabled" => ['1' => 'Enabled', '0' => 'Disabled'] */
    public static function selectOptions(string $type): array
    {
        $options = [];
        foreach (explode(',', Str::after($type, 'select:')) as $pair) {
            [$value, $label] = explode('=', $pair, 2);
            $options[$value] = $label;
        }

        return $options;
    }

    public function newsBlocks(): View
    {
        return view('cms.news-blocks', ['blocks' => NewsBlock::query()->orderBy('news_block_id')->get()]);
    }

    public function saveNewsBlocks(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'blocks' => ['required', 'array'],
            'blocks.*.heading' => ['required', 'string', 'max:200'],
            'blocks.*.status' => ['required', 'in:active,inactive'],
        ]);
        foreach ($data['blocks'] as $id => $block) {
            NewsBlock::query()->whereKey((int) $id)->update(['news_block_heading' => $block['heading'], 'news_block_status' => $block['status']]);
        }

        return back()->with('status', 'News blocks saved.');
    }

    public function contacts(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $contacts = Contact::query()
            ->when($search !== '', fn ($q) => $q->where(fn ($w) => $w->where('contact_name', 'like', "%{$search}%")->orWhere('contact_email', 'like', "%{$search}%")))
            ->orderByDesc('id')->paginate(25)->withQueryString();

        return view('cms.contacts.index', compact('contacts', 'search'));
    }

    public function contact(string $id): View
    {
        return view('cms.contacts.show', ['contact' => Contact::query()->findOrFail($id)]);
    }

    public function deleteContacts(Request $request): RedirectResponse
    {
        $ids = array_filter(array_map('intval', (array) $request->input('ids', [])));
        if ($ids === []) {
            return back()->with('error', 'Select at least one query to delete.');
        }
        Contact::query()->whereKey($ids)->get()->each->markDeleted();

        return back()->with('status', count($ids).' quer'.(count($ids) === 1 ? 'y' : 'ies').' deleted.');
    }

    /** Image upload from the Trumbowyg editor (same-site, CSRF-checked, signed-in). */
    public function editorUpload(Request $request): JsonResponse
    {
        $validator = validator($request->all(), ['image' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:5120']]);
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => $validator->errors()->first('image')], 422);
        }
        $file = $request->file('image');
        $name = Str::random(32).'.'.strtolower($file->getClientOriginalExtension() ?: $file->guessExtension());
        $file->move(rtrim(config('zed.storage_root'), '/\\').DIRECTORY_SEPARATOR.'uploads', $name);

        return response()->json(['success' => true, 'url' => '/uploads/'.$name]);
    }
}
