<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Special page content chosen in the CMS (Pages > Special content) instead of
 * being tied to page addresses in the templates: FAQ list, certificate
 * verification form, sector widgets, embedded pages.
 *
 * Additive and nullable, so the old CodeIgniter CMS can still insert pages.
 * Existing special pages get the widget they had before, so nothing changes.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('zed_pages', function (Blueprint $table) {
            if (! Schema::hasColumn('zed_pages', 'page_widget')) {
                $table->string('page_widget', 30)->nullable()->after('page_type');
            }
            if (! Schema::hasColumn('zed_pages', 'page_embed_url')) {
                $table->string('page_embed_url', 500)->nullable()->after('page_widget');
            }
        });

        $set = fn (array $where, array $values) => DB::table('zed_pages')->where($where)->whereNull('page_widget')->update($values);
        foreach (['faq', 'faqs'] as $slug) {
            $set(['page_slug' => $slug], ['page_widget' => 'faq']);
        }
        foreach (['zed-maturity-matrix', 'zed-maturity-assessment-model'] as $slug) {
            $set(['page_slug' => $slug], ['page_widget' => 'maturity']);
        }
        $set(['page_slug' => 'guidance-document'], ['page_widget' => 'guidance']);
        $set(['page_slug' => 'verify-zed-certificate'], ['page_widget' => 'verify']);
        $set(['page_slug' => 'zed-certified-msmes'], ['page_widget' => 'embed_only', 'page_embed_url' => 'https://loginzed.msme.gov.in/Dashboard/certifiedmsme']);
        $set(['page_slug' => 'kawach-certified-msmes'], ['page_widget' => 'embed', 'page_embed_url' => 'https://msmekawach.org.in/Dashboard/CertifiedMsme']);
        $set(['page_slug' => 'benefitsincentives'], ['page_widget' => 'embed_page', 'page_embed_url' => 'https://login.zed.org.in/Incentive/DetailsMobile?ct=Bronze&id=']);
        DB::table('zed_pages')->whereRaw('LOWER(page_name) = ?', ['parameters'])->whereNull('page_widget')->update(['page_widget' => 'parameters']);
    }

    public function down(): void
    {
        Schema::table('zed_pages', function (Blueprint $table) {
            $table->dropColumn(['page_widget', 'page_embed_url']);
        });
    }
};
