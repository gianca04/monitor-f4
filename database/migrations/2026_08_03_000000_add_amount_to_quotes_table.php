<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->decimal('amount', 10, 2)->default(0.00)->after('status');
        });

        // Poblar el monto inicial para todas las cotizaciones existentes
        $quotes = \App\Models\Quote::with('details')->get();
        foreach ($quotes as $quote) {
            $total = (float) round($quote->details->sum(function ($detail) {
                return $detail->subtotal ?? ($detail->quantity * $detail->unit_price);
            }), 2);

            DB::table('quotes')->where('id', $quote->id)->update(['amount' => $total]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quotes', function (Blueprint $table) {
            $table->dropColumn('amount');
        });
    }
};
