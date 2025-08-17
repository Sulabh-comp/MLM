#!/usr/bin/env php
<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Models\Employee;
use App\Models\Manager;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Bootstrap Laravel
$app = require_once __DIR__ . '/../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== MLM Region Cleanup: Remove Region Dependencies from Employee System ===\n\n";

try {
    // Step 1: Verify all employees have manager assignments
    $employeesWithoutManager = Employee::whereNull('manager_id')->count();
    
    if ($employeesWithoutManager > 0) {
        echo "✗ Error: {$employeesWithoutManager} employees still don't have manager assignments.\n";
        echo "Please run the migration script first to assign all employees to managers.\n";
        exit(1);
    }
    
    echo "✓ All employees have manager assignments\n";
    
    // Step 2: Verify hierarchy integrity
    $invalidAssignments = Employee::whereHas('manager', function($query) {
        $query->whereNull('id');
    })->count();
    
    if ($invalidAssignments > 0) {
        echo "✗ Error: {$invalidAssignments} employees have invalid manager assignments.\n";
        echo "Please fix these assignments before proceeding.\n";
        exit(1);
    }
    
    echo "✓ All manager assignments are valid\n";
    
    // Step 3: Show current employee-manager distribution
    echo "\n=== Current Employee-Manager Distribution ===\n";
    $managerStats = Manager::withCount([
        'employees',
        'allSubordinateEmployees'
    ])->get();
    
    foreach ($managerStats as $manager) {
        echo "Manager: {$manager->name} - Direct: {$manager->direct_employees_count}, Total: {$manager->all_subordinate_employees_count}\n";
    }
    
    // Step 4: Check if region_id column can be safely removed from employees table
    echo "\n=== Region Dependency Analysis ===\n";
    
    // Check if any code still references employee region_id
    $employeesWithRegion = Employee::whereNotNull('region_id')->count();
    echo "Employees with region_id set: {$employeesWithRegion}\n";
    
    // Step 5: Create backup of current state
    echo "\n=== Creating Backup ===\n";
    $backupData = [
        'timestamp' => now()->toDateTimeString(),
        'employees' => Employee::with(['manager', 'region'])->get()->map(function($emp) {
            return [
                'id' => $emp->id,
                'name' => $emp->name,
                'email' => $emp->email,
                'manager_id' => $emp->manager_id,
                'manager_name' => $emp->manager ? $emp->manager->name : null,
                'region_id' => $emp->region_id,
                'region_name' => $emp->region ? $emp->region->name : null,
            ];
        })->toArray()
    ];
    
    $backupFile = storage_path('app/employee_hierarchy_backup_' . date('Y_m_d_H_i_s') . '.json');
    file_put_contents($backupFile, json_encode($backupData, JSON_PRETTY_PRINT));
    echo "✓ Backup created: {$backupFile}\n";
    
    // Step 6: Show cleanup options
    echo "\n=== Available Cleanup Actions ===\n";
    echo "1. Remove region_id column from employees table (DESTRUCTIVE)\n";
    echo "2. Set all employee region_id to NULL (REVERSIBLE)\n";
    echo "3. Generate migration to remove region_id column\n";
    echo "4. Exit without changes\n";
    
    echo "\nSelect action (1-4): ";
    $handle = fopen("php://stdin", "r");
    $choice = trim(fgets($handle));
    fclose($handle);
    
    switch ($choice) {
        case '1':
            echo "\n⚠️  WARNING: This will permanently remove the region_id column!\n";
            echo "Type 'REMOVE REGION COLUMN' to confirm: ";
            $handle = fopen("php://stdin", "r");
            $confirmation = trim(fgets($handle));
            fclose($handle);
            
            if ($confirmation === 'REMOVE REGION COLUMN') {
                DB::beginTransaction();
                try {
                    Schema::table('employees', function ($table) {
                        $table->dropForeign(['region_id']);
                        $table->dropColumn('region_id');
                    });
                    DB::commit();
                    echo "✓ region_id column removed from employees table\n";
                } catch (Exception $e) {
                    DB::rollBack();
                    echo "✗ Error removing column: " . $e->getMessage() . "\n";
                }
            } else {
                echo "✗ Action cancelled\n";
            }
            break;
            
        case '2':
            DB::beginTransaction();
            try {
                Employee::query()->update(['region_id' => null]);
                DB::commit();
                echo "✓ All employee region_id values set to NULL\n";
            } catch (Exception $e) {
                DB::rollBack();
                echo "✗ Error updating records: " . $e->getMessage() . "\n";
            }
            break;
            
        case '3':
            $migrationContent = "<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('employees', function (Blueprint \$table) {
            \$table->dropForeign(['region_id']);
            \$table->dropColumn('region_id');
        });
    }

    public function down()
    {
        Schema::table('employees', function (Blueprint \$table) {
            \$table->unsignedBigInteger('region_id')->nullable()->after('manager_id');
            \$table->foreign('region_id')->references('id')->on('regions')->onDelete('set null');
        });
    }
};";
            
            $migrationFile = database_path('migrations/' . date('Y_m_d_His') . '_remove_region_id_from_employees.php');
            file_put_contents($migrationFile, $migrationContent);
            echo "✓ Migration created: {$migrationFile}\n";
            echo "Run: php artisan migrate\n";
            break;
            
        case '4':
            echo "✓ Exiting without changes\n";
            break;
            
        default:
            echo "✗ Invalid choice\n";
            break;
    }
    
    // Step 7: Final verification
    if (in_array($choice, ['1', '2'])) {
        echo "\n=== Post-Cleanup Verification ===\n";
        
        $employeesCount = Employee::count();
        $employeesWithManager = Employee::whereNotNull('manager_id')->count();
        $orphanedEmployees = Employee::whereNull('manager_id')->count();
        
        echo "Total employees: {$employeesCount}\n";
        echo "Employees with managers: {$employeesWithManager}\n";
        echo "Orphaned employees: {$orphanedEmployees}\n";
        
        if ($orphanedEmployees == 0) {
            echo "✓ All employees properly assigned to hierarchy\n";
        } else {
            echo "✗ Warning: {$orphanedEmployees} employees are not assigned to any manager\n";
        }
    }
    
} catch (Exception $e) {
    echo "✗ Cleanup failed: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n=== Cleanup Complete ===\n";
