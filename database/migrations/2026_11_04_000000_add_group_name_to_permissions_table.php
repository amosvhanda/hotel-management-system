<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Existing databases already have this column (added by hand); fresh installs need it.
    public function up(): void
    {
        if (! Schema::hasColumn('permissions', 'group_name')) {
            Schema::table('permissions', function (Blueprint $table) {
                $table->string('group_name')->nullable()->after('name');
            });
        }
    }

    public function down(): void
    {
        // Left in place on purpose: older databases created it outside migrations.
    }
};
