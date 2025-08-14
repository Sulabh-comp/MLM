<?php

namespace App\Console\Commands;

use App\Models\Manager;
use App\Models\ManagerLevel;
use Illuminate\Console\Command;

class HierarchyManagement extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hierarchy:manage 
                            {action? : Action to perform: stats, tree, validate, rebuild, permissions}
                            {--manager= : Manager ID for specific operations}
                            {--level= : Manager level filter}
                            {--format=table : Output format: table, json, tree}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage and display manager hierarchy information';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action') ?? 'stats';

        match($action) {
            'stats' => $this->showStats(),
            'tree' => $this->showTree(),
            'validate' => $this->validateHierarchy(),
            'rebuild' => $this->rebuildHierarchy(),
            'permissions' => $this->showPermissions(),
            'test' => $this->runTests(),
            default => $this->error('Invalid action. Use: stats, tree, validate, rebuild, permissions, test')
        };
    }

    private function showStats()
    {
        $this->info('📊 Manager Hierarchy Statistics');
        $this->newLine();

        $stats = Manager::getHierarchyStats();

        // Basic stats
        $this->table(['Metric', 'Value'], [
            ['Total Managers', $stats['total_managers']],
            ['Top Level Managers', $stats['top_level_managers']],
            ['Maximum Depth', $stats['max_depth']],
            ['Levels in Use', $stats['levels_in_use']],
        ]);

        // Managers by level
        if (!empty($stats['managers_by_level'])) {
            $this->newLine();
            $this->info('👥 Managers by Level:');
            $levelData = [];
            foreach ($stats['managers_by_level'] as $level => $count) {
                $levelData[] = [$level, $count];
            }
            $this->table(['Level', 'Count'], $levelData);
        }

        // Managers by depth
        if (!empty($stats['managers_by_depth'])) {
            $this->newLine();
            $this->info('📏 Managers by Hierarchy Depth:');
            $depthData = [];
            foreach ($stats['managers_by_depth'] as $depth => $count) {
                $depthData[] = ["Level $depth", $count];
            }
            $this->table(['Depth', 'Count'], $depthData);
        }
    }

    private function showTree()
    {
        $this->info('🌳 Manager Hierarchy Tree');
        $this->newLine();

        if ($managerId = $this->option('manager')) {
            $manager = Manager::find($managerId);
            if (!$manager) {
                $this->error("Manager with ID $managerId not found");
                return;
            }
            $this->displayManagerTree($manager);
        } else {
            $topManagers = Manager::getTopLevelManagers();
            foreach ($topManagers as $manager) {
                $this->displayManagerTree($manager);
                $this->newLine();
            }
        }
    }

    private function displayManagerTree(Manager $manager, int $level = 0)
    {
        $indent = str_repeat('  ', $level);
        $icon = $level === 0 ? '👑' : ($manager->isLeaf() ? '👤' : '👥');
        
        $this->line(sprintf(
            '%s%s %s (%s) - %s - Team: %d',
            $indent,
            $icon,
            $manager->name,
            $manager->level_name ?? 'No Level',
            $manager->code ?? 'No Code',
            $manager->getTeamSize()
        ));

        foreach ($manager->children as $child) {
            $this->displayManagerTree($child, $level + 1);
        }
    }

    private function validateHierarchy()
    {
        $this->info('🔍 Validating Manager Hierarchy');
        $this->newLine();

        $issues = Manager::validateHierarchy();

        if (empty($issues)) {
            $this->info('✅ Hierarchy validation passed! No issues found.');
        } else {
            $this->error('❌ Hierarchy validation failed. Issues found:');
            foreach ($issues as $issue) {
                $this->line("  • $issue");
            }
        }

        // Check for orphaned managers
        $orphaned = Manager::getOrphanedManagers();
        if ($orphaned->isNotEmpty()) {
            $this->newLine();
            $this->warn('🚨 Orphaned Managers Found:');
            $orphanedData = $orphaned->map(function ($manager) {
                return [
                    $manager->id,
                    $manager->name,
                    $manager->parent_id,
                    $manager->level_name ?? 'No Level'
                ];
            })->toArray();
            $this->table(['ID', 'Name', 'Parent ID', 'Level'], $orphanedData);
        }
    }

    private function rebuildHierarchy()
    {
        $this->info('🔧 Rebuilding Manager Hierarchy Paths');
        $this->newLine();

        if (!$this->confirm('This will recalculate all hierarchy paths. Continue?')) {
            $this->info('Operation cancelled.');
            return;
        }

        $updated = Manager::rebuildHierarchyPaths();
        $this->info("✅ Successfully rebuilt hierarchy paths for $updated managers.");
    }

    private function showPermissions()
    {
        $this->info('🔐 Manager Permissions Analysis');
        $this->newLine();

        if ($managerId = $this->option('manager')) {
            $manager = Manager::find($managerId);
            if (!$manager) {
                $this->error("Manager with ID $managerId not found");
                return;
            }
            $this->displayManagerPermissions($manager);
        } else {
            $managers = Manager::with('managerLevel')->get();
            $permissionsData = [];

            foreach ($managers as $manager) {
                $permissionsData[] = [
                    $manager->id,
                    $manager->name,
                    $manager->level_name ?? 'No Level',
                    $manager->canCreateSubordinates() ? '✅' : '❌',
                    $manager->getAssignableLevels()->count(),
                    $manager->getTeamSize(),
                ];
            }

            $this->table([
                'ID', 'Name', 'Level', 'Can Create', 'Assignable Levels', 'Team Size'
            ], $permissionsData);
        }
    }

    private function displayManagerPermissions(Manager $manager)
    {
        $this->info("Permissions for: {$manager->name} ({$manager->level_name})");
        $this->newLine();

        $permissions = $manager->getAllPermissions();
        
        if (empty($permissions)) {
            $this->warn('No permissions found for this manager.');
        } else {
            $this->info('📋 All Permissions:');
            foreach ($permissions as $permission) {
                $this->line("  • $permission");
            }
        }

        $this->newLine();
        $this->info('👥 Management Capabilities:');
        $this->table(['Capability', 'Status'], [
            ['Can Create Subordinates', $manager->canCreateSubordinates() ? '✅ Yes' : '❌ No'],
            ['Assignable Levels Count', $manager->getAssignableLevels()->count()],
            ['Current Team Size', $manager->getTeamSize()],
            ['Direct Reports', $manager->getDirectReportsCount()],
            ['Span of Control', $manager->getSpanOfControl()],
        ]);

        if ($manager->canCreateSubordinates()) {
            $this->newLine();
            $this->info('📝 Assignable Levels:');
            $levelData = $manager->getAssignableLevels()->map(function ($level) {
                return [$level->name, $level->hierarchy_level, $level->code];
            })->toArray();
            $this->table(['Level Name', 'Hierarchy Level', 'Code'], $levelData);
        }
    }

    private function runTests()
    {
        $this->info('🧪 Running Hierarchy Method Tests');
        $this->newLine();

        // Test with a sample manager
        $manager = Manager::first();
        if (!$manager) {
            $this->error('No managers found in database');
            return;
        }

        $this->info("Testing with manager: {$manager->name}");
        $this->newLine();

        $tests = [
            'Hierarchy Path' => fn() => implode(' → ', $manager->getHierarchyPath()),
            'Breadcrumb' => fn() => $manager->getHierarchyBreadcrumb(),
            'Team Size' => fn() => $manager->getTeamSize(),
            'Direct Reports' => fn() => $manager->getDirectReportsCount(),
            'Span of Control' => fn() => $manager->getSpanOfControl(),
            'Is Top Level' => fn() => $manager->isTopLevel() ? 'Yes' : 'No',
            'Is Leaf' => fn() => $manager->isLeaf() ? 'Yes' : 'No',
            'Can Create Subordinates' => fn() => $manager->canCreateSubordinates() ? 'Yes' : 'No',
        ];

        $testResults = [];
        foreach ($tests as $testName => $testFunction) {
            try {
                $result = $testFunction();
                $testResults[] = [$testName, '✅', $result];
            } catch (\Exception $e) {
                $testResults[] = [$testName, '❌', $e->getMessage()];
            }
        }

        $this->table(['Test', 'Status', 'Result'], $testResults);
    }
}
