<?php

namespace App\Console\Commands;

use App\Models\Manager;
use Illuminate\Console\Command;

class UpdateManagerHierarchy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'managers:update-hierarchy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update hierarchy paths and depth for all managers';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating manager hierarchy paths...');

        // Start with top-level managers (no parent)
        $topLevelManagers = Manager::whereNull('parent_id')->get();
        
        $updated = 0;
        
        foreach ($topLevelManagers as $manager) {
            $this->updateManagerAndChildren($manager, 0, '/' . $manager->id . '/');
            $updated++;
        }
        
        $this->info("Successfully updated hierarchy for {$updated} top-level managers and their subordinates.");
        
        return Command::SUCCESS;
    }
    
    /**
     * Recursively update manager and all children hierarchy paths
     */
    private function updateManagerAndChildren(Manager $manager, int $depth, string $path)
    {
        // Update this manager
        $manager->update([
            'depth' => $depth,
            'hierarchy_path' => $path
        ]);
        
        $this->line("Updated: {$manager->name} (ID: {$manager->id}) - Depth: {$depth}, Path: {$path}");
        
        // Update all children
        foreach ($manager->children as $child) {
            $childPath = rtrim($path, '/') . '/' . $child->id . '/';
            $this->updateManagerAndChildren($child, $depth + 1, $childPath);
        }
    }
}
