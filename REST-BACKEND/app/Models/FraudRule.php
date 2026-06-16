<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Enums\FraudRuleScope;
use App\Enums\FraudRuleType;
use App\Enums\FraudRuleAction;

class FraudRule extends Model
{
    use HasFactory;

    protected $table = 'fraud_rules';

    protected $fillable = [
        'name', 'description', 'scope', 'scope_id',
        'rule_type', 'rule_action', 'conditions', 'is_active', 'created_by',
    ];

    protected $casts = [
        'conditions' => 'array',
        'is_active'  => 'boolean',
        'scope'       => FraudRuleScope::class,
        'rule_type'   => FraudRuleType::class,
        'rule_action' => FraudRuleAction::class,
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}