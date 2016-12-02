<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('configurations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('value');
            $table->timestamps();
        });

        DB::insert("INSERT INTO configurations (name, value, created_at, updated_at) VALUES ('cloudstack_zoneid','', NOW(), NOW())");
        DB::insert("INSERT INTO configurations (name, value, created_at, updated_at) VALUES ('cloudstack_networkid','', NOW(), NOW())");
        DB::insert("INSERT INTO configurations (name, value, created_at, updated_at) VALUES ('cloudstack_templateid','', NOW(), NOW())");
        DB::insert("INSERT INTO configurations (name, value, created_at, updated_at) VALUES ('cloudstack_serviceofferingid','', NOW(), NOW())");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('configurations');
    }
}
