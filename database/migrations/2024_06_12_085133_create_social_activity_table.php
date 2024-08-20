<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('social_activities', function (Blueprint $table) {
            $table->id();
            $table->string('name')->default('')->nullable()->comment('活动名称');
            $table->integer('man_limit')->unsigned()->default(0)->nullable()->comment('活动人数，0表示不限制人数');
            $table->integer('days_active')->unsigned()->default(0)->nullable()->comment('活动天数');
            $table->dateTime('activity_date')->nullable()->comment('活动开始日期');
            $table->tinyInteger('status')->unsigned()->default(1)->nullable()->comment('活动状态');
            $table->json('sign_up')->nullable()->comment('活动报名');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_activities');
    }
};
