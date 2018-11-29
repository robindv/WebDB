<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('prefix');
            $table->string('examples_site');
            $table->string( 'examples_site_ip');
            $table->string( 'admin_email');
            $table->datetime('project_deadline');
            $table->timestamps();
        });

        Schema::table('projects', function(Blueprint $table) {
            $table->unsignedInteger('course_id')->after('advanced');
            $table->foreign('course_id')->references('id')->on('courses');
        });

        Schema::table('servers', function(Blueprint $table) {
            $table->unsignedInteger('course_id')->after('name');
            $table->foreign('course_id')->references('id')->on('courses');
        });

        Schema::table('groups', function(Blueprint $table) {
            $table->unsignedInteger('course_id')->after('name');
            $table->foreign('course_id')->references('id')->on('courses');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign('projects_course_id_foreign');
            $table->dropColumn('course_id');
        });

        Schema::table('servers', function (Blueprint $table) {
            $table->dropForeign('servers_course_id_foreign');
            $table->dropColumn('course_id');
        });

        Schema::table('groups', function (Blueprint $table) {
            $table->dropForeign('groups_course_id_foreign');
            $table->dropColumn('course_id');
        });

        Schema::dropIfExists('courses');

    }
}
