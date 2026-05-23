<?php

namespace App\Console\Commands;

use App\Models\OfferAffiliate;
use App\Utils\UrlHelper;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateSlugs extends Command
{
    protected $signature   = 'affiliates:generate-slugs';
    protected $description = 'Generate short-link slugs for existing offer_affiliate records that have none.';

    public function handle(): void
    {
        $records = OfferAffiliate::whereNull('slug')->get();

        if ($records->isEmpty()) {
            $this->info('All records already have slugs.');
            return;
        }

        $this->info("Generating slugs for {$records->count()} record(s)...");
        $bar = $this->output->createProgressBar($records->count());
        $bar->start();

        foreach ($records as $oa) {
            do {
                $slug = Str::random(8);
            } while (OfferAffiliate::where('slug', $slug)->exists());

            $oa->slug         = $slug;
            $oa->tracking_url = UrlHelper::buildShortLinkUrl(null, $slug);
            $oa->save();

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Done.');
    }
}