<?php

namespace Tests\Feature;

use App\Models\Quote;
use App\Models\QuoteCategory;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class QuoteCategoryTest extends TestCase
{
    // Use RefreshDatabase if you want to work on a clean DB, but let's assume we use the current one or a test one.
    // For now, let's just use the existing DB if possible or use a factory.

    public function test_can_update_quote_category()
    {
        $quote = Quote::first();
        if (!$quote) {
            $this->markTestSkipped('No quotes found.');
        }

        $newCategory = QuoteCategory::where('id', '!=', $quote->quote_category_id)->first();
        if (!$newCategory) {
            $newCategory = QuoteCategory::create(['name' => 'New Category']);
        }

        $newType = ($quote->quote_type === \App\Enums\QuoteType::PREVENTIVO)
            ? \App\Enums\QuoteType::CORRECTIVO
            : \App\Enums\QuoteType::PREVENTIVO;

        $response = $this->putJson("/quotes/{$quote->id}", [
            'quote_category_id' => $newCategory->id,
            'quote_type' => $newType->value,
            'project_name' => $quote->project->name ?? 'Project Name',
            'status' => $quote->status,
            'quote_date' => $quote->quote_date ? $quote->quote_date->toDateString() : now()->toDateString(),
            'execution_date' => $quote->execution_date ? $quote->execution_date->toDateString() : now()->toDateString(),
            'items' => []
        ]);

        $response->assertStatus(200);
        $updatedQuote = $quote->fresh();
        $this->assertEquals($newCategory->id, $updatedQuote->quote_category_id);
        $this->assertEquals($newType, $updatedQuote->quote_type);
    }
}
