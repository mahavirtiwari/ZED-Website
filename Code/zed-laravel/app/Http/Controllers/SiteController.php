<?php

namespace App\Http\Controllers;

use App\Mail\ContactQuery;
use App\Models\Contact;
use App\Support\Captcha;
use App\Support\SiteData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * The public website (ported from the CodeIgniter Home controller). Pages are
 * rendered with the templates carried over from the old site: the layout
 * (resources/views/public/layout.blade.php) includes "public.{$page}".
 */
class SiteController extends Controller
{
    /** Data every page needs: menus, footer, settings and counters. */
    private function common(array $data): array
    {
        $globals = SiteData::globals();

        return $data + [
            'menu' => SiteData::menu(),
            'social_urls' => $globals,
            'counters' => $globals,
            'footer_news' => SiteData::footerNews(),
            'footer_gallery' => SiteData::footerGallery(),
        ];
    }

    private function render(string $page, array $data = []): View
    {
        return view('public.layout', $this->common(['page' => $page] + $data));
    }

    public function home(): View
    {
        return $this->render('index', [
            'home_seo' => DB::table('zed_global_config')->pluck('value', 'label')->all(),
            'news' => SiteData::homeNews(),
            'breaking_news' => SiteData::breakingNews(),
            'stakeholders' => SiteData::stakeholders(),
            'videos' => SiteData::homeVideos(),
            'page_sidemenu' => SiteData::homePages(),
            'slider' => SiteData::slider(),
            'ratingImage' => SiteData::ratingImage(),
            'ratingImageProcess' => SiteData::ratingProcessImage(),
            'allprograms' => SiteData::programs(),
            'incentives' => SiteData::incentives(),
            'allnotifications' => SiteData::notifications(),
            'testimonials' => SiteData::testimonials(),
            'gallery' => SiteData::gallery(),
        ]);
    }

    /**
     * /{slug}: a news item or a page. When both use a slug, the news item wins
     * (as on the old site, where post routes were registered after page routes).
     */
    public function slug(Request $request, string $slug): View
    {
        $slug = Str::lower($slug);
        $post = DB::table('zed_posts')->whereRaw('LOWER(post_slug) = ?', [$slug])->where('status', 1)->first();
        if ($post) {
            return $this->post($post);
        }
        $page = DB::table('zed_pages')->whereRaw('LOWER(page_slug) = ?', [$slug])->where('status', 1)->first();
        abort_if($page === null, 404);

        return $this->page($request, $page, $slug);
    }

    private function post(object $post): View
    {
        return $this->render('post', [
            'details' => $post,
            'popular_news' => DB::table('zed_posts')->where('id', '!=', $post->id)->where('status', 1)->orderByDesc('id')->limit(3)->get()->all(),
        ]);
    }

    private function page(Request $request, object $page, string $slug): View
    {
        $sectors = [0 => 'Select Sector'];
        foreach (SiteData::sectors() as $sector) {
            $sectors[$sector['sectorid']] = $sector['code'].'-'.$sector['name'];
        }
        $data = [
            'details' => $page,
            'page_type' => $page->page_type,
            'page_sidemenu' => SiteData::pageMenus((int) $page->page_menu),
            'parameter' => SiteData::allParameters(),
            'zed_rating' => SiteData::ratings(),
            'parent' => $sectors,
            'slug' => $slug,
        ];

        if ($slug === 'faq' || $slug === 'faqs') {
            return $this->render('faq', $data + ['faqs' => SiteData::faqs()]);
        }
        if ($slug === 'benefitsincentives') {
            return $this->render('incentives', $data);
        }
        if ($slug === 'verify-zed-certificate') {
            $data += $this->verifyCertificate($request);
        }

        return $this->render('page', $data);
    }

    /** ZED certificate check against the ZED portal (form on the verify-zed-certificate page). */
    private function verifyCertificate(Request $request): array
    {
        $udyam = trim((string) $request->input('udyamNumber', ''));
        $certificate = trim((string) $request->input('certificationNumber', ''));
        $result = ['udyamNumber' => $udyam, 'certificationNumber' => $certificate];
        if ($request->isMethod('post') && $udyam !== '' && $certificate !== '') {
            try {
                $result['verifyCertification'] = Http::connectTimeout(3)->timeout(10)->acceptJson()->get(config('zed.verify_url'), [
                    'token' => config('zed.verify_token'),
                    'udyamNumber' => Str::limit($udyam, 40, ''),
                    'certificationNumber' => Str::limit($certificate, 60, ''),
                ])->json();
            } catch (\Throwable $e) {
                Log::warning('Certificate verification failed: '.$e->getMessage());
                $result['verifyCertification'] = null;
            }
        }

        return $result;
    }

    public function contact(Request $request, Captcha $captcha): View|RedirectResponse
    {
        $type = zed_setting('contact_form_type', 'zoho') === 'site' ? 'site' : 'zoho';
        $useCaptcha = captcha_enabled('captcha_contact_us');

        if ($request->isMethod('post')) {
            abort_unless($type === 'site', 404);
            try {
                return $this->submitContact($request, $captcha, $useCaptcha);
            } catch (ValidationException $e) {
                // explicit target: "back" would be the captcha image, the last GET of the session
                throw $e->redirectTo(url('contact-us').'#zed-contact-form');
            }
        }

        return $this->render('contactus', ['contact_form_type' => $type, 'captcha' => $useCaptcha]);
    }

    private function submitContact(Request $request, Captcha $captcha, bool $useCaptcha): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:100'],
            'enterprise_name' => ['nullable', 'string', 'max:200'],
            'query_category' => ['required', 'in:'.implode(',', config('zed.contact_categories'))],
            'message' => ['required', 'string', 'max:3000'],
            'captcha' => $useCaptcha ? ['required', 'string'] : ['nullable'],
        ], [], ['captcha' => 'security code']);
        if ($useCaptcha && ! $captcha->check('contact', $request->input('captcha'))) {
            throw ValidationException::withMessages(['captcha' => 'The security code was not correct. Please type the new code shown.']);
        }

        $contact = Contact::query()->create([
            'contact_name' => $data['name'],
            'contact_email' => $data['email'],
            'contact_phone' => (string) ($data['phone'] ?? ''),
            'enterprise_name' => (string) ($data['enterprise_name'] ?? ''),
            'query_category' => $data['query_category'],
            'contact_query' => $data['message'],
        ]);
        try {
            $training = in_array($data['query_category'], ['Training Program', 'Awareness Program'], true);
            $mail = Mail::to($training ? config('zed.contact_training_to') : config('zed.contact_to'));
            if (! $training && config('zed.contact_cc')) {
                $mail->cc(config('zed.contact_cc'));
            }
            $mail->send(new ContactQuery($contact));
        } catch (\Throwable $e) {
            // the query is saved in CMS > Contact queries even if mail fails
            Log::error('Contact query mail failed: '.$e->getMessage());
        }

        return redirect('contact-us')->with('message', 'Thank you. Your query has been submitted successfully.');
    }

    public function video(): View
    {
        return $this->render('video', ['page_sidemenu' => []]);
    }

    public function gallery(): View
    {
        return $this->render('gallery', [
            'gallery_seo' => DB::table('zed_global_config')->pluck('value', 'label')->all(),
            'details' => SiteData::gallery(),
        ]);
    }

    public function galleryMonth(int $year, string $month): View
    {
        $month = Str::ucfirst(Str::lower($month));

        return $this->render('individual-gallery', [
            'details' => SiteData::galleryMonth($year, $month),
            'gallery_seo' => DB::table('zed_global_config')->pluck('value', 'label')->all(),
            'year' => $year,
            'month' => $month,
        ]);
    }

    public function search(Request $request): View
    {
        $data = [];
        $term = trim((string) $request->input('search_query', ''));
        if ($term !== '') {
            $term = Str::limit(strip_tags($term), 100, '');
            $data['search'] = $term;
            $results = SiteData::search($term);
            $results === false ? $data['error'] = 'No Results found' : $data['details'] = $results;
        }

        return $this->render('search', $data);
    }

    public function news(): View
    {
        return $this->render('news', ['details' => SiteData::news()]);
    }

    // --- AJAX (sector drop-downs on the parameters / maturity / guidance pages)

    public function disciplines(Request $request): JsonResponse
    {
        if (! $request->filled('id')) {
            return response()->json(['success' => false, 'token' => csrf_token()]);
        }
        $discipline = [];
        foreach (SiteData::disciplineResults((int) $request->input('id')) as $row) {
            $discipline[$row['code']]['para_discipline'] ??= $row['para_discipline'];
            $discipline[$row['code']]['data'][] = $row;
        }

        return response()->json(['success' => true, 'discipline' => $discipline, 'token' => csrf_token()]);
    }

    public function maturity(Request $request): JsonResponse
    {
        if (! $request->filled('id')) {
            return response()->json(['success' => false, 'token' => csrf_token()]);
        }

        return response()->json(['success' => true, 'discipline' => SiteData::maturity((int) $request->input('id')), 'token' => csrf_token()]);
    }

    public function guidance(Request $request): JsonResponse
    {
        if (! $request->filled('id')) {
            return response()->json(['success' => false, 'token' => csrf_token()]);
        }
        $rows = SiteData::guidance((int) $request->query('id'));

        return response()->json(['success' => $rows !== [], 'discipline' => $rows, 'token' => csrf_token()]);
    }

    public function sitemap(): Response
    {
        $base = rtrim(url('/'), '/').'/';
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n".'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
        foreach (DB::table('zed_pages')->where('status', 1)->pluck('page_slug') as $slug) {
            $xml .= '<url><loc>'.htmlspecialchars($base.$slug, ENT_XML1, 'UTF-8').'</loc><changefreq>always</changefreq><priority>1</priority></url>'."\n";
        }
        foreach (DB::table('zed_posts')->where('status', 1)->pluck('post_slug') as $slug) {
            $xml .= '<url><loc>'.htmlspecialchars($base.$slug, ENT_XML1, 'UTF-8').'</loc><changefreq>always</changefreq><priority>0.9</priority></url>'."\n";
        }

        return response($xml."</urlset>\n", 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }
}
