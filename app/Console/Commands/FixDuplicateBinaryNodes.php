<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BinaryNode;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class FixDuplicateBinaryNodes extends Command
{
    protected $signature = 'binary:fix-duplicates {--dry-run : Show what would be fixed without making changes}';
    protected $description = 'Fix duplicate BinaryNodes where multiple children exist in the same position';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->info('DRY RUN MODE - No changes will be made');
        }

        $this->info('Scanning for duplicate BinaryNodes...');
        
        // Find all parents that have multiple children in the same position
        $duplicates = BinaryNode::select('parent_id', 'position', DB::raw('COUNT(*) as count'))
            ->groupBy('parent_id', 'position')
            ->having('count', '>', 1)
            ->get();

        if ($duplicates->isEmpty()) {
            $this->info('No duplicate BinaryNodes found!');
            return 0;
        }

        $this->warn("Found {$duplicates->count()} positions with duplicate children");
        
        $fixed = 0;
        $kept = 0;
        $removed = 0;

        foreach ($duplicates as $duplicate) {
            $parentId = $duplicate->parent_id;
            $position = $duplicate->position;
            
            $parent = User::find($parentId);
            $parentName = $parent ? $parent->name . ' (' . $parent->referral_code . ')' : "User ID {$parentId}";
            
            $this->line("\n--- Processing: {$parentName} - Position: {$position} ---");
            
            // Get all nodes for this parent and position, ordered by creation (oldest first)
            $nodes = BinaryNode::where('parent_id', $parentId)
                ->where('position', $position)
                ->orderBy('id', 'asc')
                ->get();
            
            $this->info("Found {$nodes->count()} nodes in this position:");
            
            foreach ($nodes as $index => $node) {
                $user = User::find($node->user_id);
                $userName = $user ? $user->name . ' (' . $user->referral_code . ')' : "User ID {$node->user_id}";
                
                if ($index === 0) {
                    $this->info("  ✓ KEEPING (oldest): Node ID {$node->id} - {$userName}");
                    $kept++;
                } else {
                    $this->warn("  ✗ REMOVING (duplicate): Node ID {$node->id} - {$userName}");
                    
                    if (!$dryRun) {
                        // Check if this user has children - if so, we need to handle them
                        $hasChildren = BinaryNode::where('parent_id', $node->user_id)->exists();
                        
                        if ($hasChildren) {
                            $this->error("    WARNING: This user has children! Moving children to the kept node...");
                            
                            // Move children to the first (kept) node
                            $keptNode = $nodes->first();
                            BinaryNode::where('parent_id', $node->user_id)
                                ->update(['parent_id' => $keptNode->user_id]);
                            
                            $this->info("    Children moved to node ID {$keptNode->id}");
                        }
                        
                        // Delete the duplicate node
                        $node->delete();
                        $removed++;
                    } else {
                        $removed++;
                    }
                }
            }
            
            $fixed++;
        }

        $this->line("\n" . str_repeat('=', 50));
        $this->info("Summary:");
        $this->info("  Positions fixed: {$fixed}");
        $this->info("  Nodes kept: {$kept}");
        $this->info("  Nodes removed: {$removed}");
        
        if ($dryRun) {
            $this->warn("\nThis was a DRY RUN. Run without --dry-run to apply changes.");
        } else {
            $this->info("\n✓ Duplicate BinaryNodes have been fixed!");
        }

        return 0;
    }
}




