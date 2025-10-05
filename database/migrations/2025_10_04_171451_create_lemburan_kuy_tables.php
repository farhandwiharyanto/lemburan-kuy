<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Update users table jika sudah ada
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'role')) {
                    $table->enum('role', ['admin', 'pimpinan', 'bawahan'])->default('bawahan');
                }
                if (!Schema::hasColumn('users', 'department')) {
                    $table->string('department')->nullable();
                }
            });
        }

        // Create overtimes table
        if (!Schema::hasTable('overtimes')) {
            Schema::create('overtimes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('employee_name');
                $table->string('department');
                $table->date('date');
                $table->time('start_time');
                $table->time('end_time');
                $table->text('task_description');
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
                $table->text('rejection_reason')->nullable();
                $table->timestamps();
            });
        }

        // Create overtime_approvals table
        if (!Schema::hasTable('overtime_approvals')) {
            Schema::create('overtime_approvals', function (Blueprint $table) {
                $table->id();
                $table->foreignId('overtime_id')->constrained()->onDelete('cascade');
                $table->foreignId('approver_id')->constrained('users')->onDelete('cascade');
                $table->enum('status', ['approved', 'rejected']);
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('overtime_approvals');
        Schema::dropIfExists('overtimes');
        
        // Optional: remove columns from users table
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'department']);
        });
    }
};