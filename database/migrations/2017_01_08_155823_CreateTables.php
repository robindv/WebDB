<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        if(! \Schema::hasTable("projects"))
        {
            Schema::create('projects', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->boolean('advanced');
                $table->timestamps();
            });
        }

        if(! \Schema::hasTable("users"))
        {
            Schema::create('users', function (Blueprint $table) {
                $table->increments('id');
                $table->string("uvanetid");
                $table->unsignedInteger("gitlab_user_id")->nullable();
                $table->string("firstname");
                $table->string("infix");
                $table->string("lastname");
                $table->string("email");
                $table->unsignedInteger("role");
                $table->string("linux_name");
                $table->timestamps();
            });
        }

        if(! \Schema::hasTable("groups"))
        {
            Schema::create('groups', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->unsignedInteger('gitlab_group_id')->nullable();
                $table->unsignedInteger('assistant_id')->nullable();
                $table->unsignedInteger('project_id')->nullable();
                $table->text('remark');
                $table->timestamps();

                $table->foreign("assistant_id")->references("id")->on("users")->onDelete("set null");
                $table->foreign("project_id")->references("id")->on("projects")->onDelete("set null");
            });
        }

        if(! \Schema::hasTable("students"))
        {
            Schema::create('students', function (Blueprint $table) {
                $table->increments('id');
                $table->string("programme");
                $table->unsignedInteger("group_id")->nullable();
                $table->boolean("active");
                $table->unsignedInteger("user_id")->nullable();
                $table->unsignedInteger("tutor_id")->nullable();
                $table->text("remark");
                $table->timestamps();

                $table->foreign("group_id")->references("id")->on("groups")->onDelete("set null");
                $table->foreign("user_id")->references("id")->on("users")->onDelete("cascade");
                $table->foreign("tutor_id")->references("id")->on("users")->onDelete("set null");
            });
        }

        if(! \Schema::hasTable("servers"))
        {
            Schema::create('servers', function (Blueprint $table) {
                $table->increments('id');
                $table->string("name");
                $table->string("cloudstack_id")->nullable();
                $table->string("ip_address")->nullable();
                $table->boolean("configured");
                $table->unsignedInteger("group_id")->nullable();
                $table->string("state")->nullable();
                $table->bigInteger("memory")->nullable();
                $table->string("ssl_issuer")->nullable();
                $table->timestamp("ssl_valid_from")->nullable();
                $table->timestamp("ssl_valid_to")->nullable();
                $table->timestamps();

                $table->foreign("group_id")->references("id")->on("groups")->onDelete("set null");
            });
        }

        if(! \Schema::hasTable("server_users"))
        {
            Schema::create('server_users', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger("server_id");
                $table->unsignedInteger("user_id")->nullable();
                $table->tinyInteger("created")->unsigned;
                $table->string("username");
                $table->string("password");
                $table->timestamps();

                $table->foreign("server_id")->references("id")->on("servers")->onDelete("cascade");
                $table->foreign("user_id")->references("id")->on("users")->onDelete("set null");
            });
        }

        if(! \Schema::hasTable("server_tasks"))
        {
            Schema::create('server_tasks', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger("server_id");
                $table->string("action");
                $table->unsignedInteger("status");
                $table->timestamps();

                $table->foreign("server_id")->references("id")->on("servers")->onDelete("cascade");
            });


        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists("server_tasks");
        Schema::dropIfExists("server_users");
        Schema::dropIfExists("servers");
        Schema::dropIfExists("students");
        Schema::dropIfExists('groups');
        Schema::dropIfExists('users');
        Schema::dropIfExists('projects');

    }
}
