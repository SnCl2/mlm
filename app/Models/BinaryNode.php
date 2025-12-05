<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BinaryNode extends Model
{
    protected $fillable = [
        'user_id',
        'parent_id',
        'position',
        'left_points',
        'right_points',
        'cb_left',
        'cb_right',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    /**
     * Get all child nodes
     */
    public function children()
    {
        return $this->hasMany(BinaryNode::class, 'parent_id', 'user_id');
    }

    /**
     * Get left child node
     */
    public function leftChild()
    {
        return $this->hasOne(BinaryNode::class, 'parent_id', 'user_id')
                    ->where('position', 'left');
    }

    /**
     * Get right child node
     */
    public function rightChild()
    {
        return $this->hasOne(BinaryNode::class, 'parent_id', 'user_id')
                    ->where('position', 'right');
    }
    
    public static function getPositionInChain($baseUserId, $targetUserId)
    {
        $currentNode = self::where('user_id', $targetUserId)->first();

        while ($currentNode) {
            if ($currentNode->parent_id == $baseUserId) {
                return $currentNode->position; // 'left' or 'right'
            }

            $currentNode = self::where('user_id', $currentNode->parent_id)->first();
        }

        return null; // Not in the base user's binary subtree
    }
    public static function getUplineChainToLevel($userId, $maxLevel)
    {
        $chain = [];
        $currentNode = self::where('user_id', $userId)->first();
        $level = 1;
    
        while ($currentNode && $currentNode->parent_id && $level <= $maxLevel) {
            $parentNode = self::where('user_id', $currentNode->parent_id)->first();
            if (!$parentNode) {
                break;
            }
            $chain[] = [
                'level' => $level,
                'user_id' => $parentNode->user_id,
                'child_position' => $currentNode->position, // 'left' or 'right'
            ];
            $currentNode = $parentNode;
            $level++;
        }
        return $chain; // From immediate parent up to maxLevel
    }

}
