<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Health\ResultStores\EloquentHealthResultStore;

return new class extends Migration
{
    public function up()
    {
        $tableName = EloquentHealthResultStore::getHistoryItemInstance()->getTable();

        Schema::table($tableName, function (Blueprint $table) {
            $table->string("server_key", 100)->nullable()->index();
        });
    }
    public function down()
    {
        $tableName = EloquentHealthResultStore::getHistoryItemInstance()->getTable();

        Schema::table($tableName, function (Blueprint $table) {
            $table->dropColumn("server_key");
        });
    }
};
