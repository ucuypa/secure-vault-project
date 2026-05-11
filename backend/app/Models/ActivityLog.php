<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'vault_file_id',
        'action',
        'ip_address',
        'user_agent'
    ];

    public static function log($action, $vaultFileId = null)
    {
        $user = request()->user();
        
        if ($user) {
            self::create([
                'user_id'       => $user->id,
                'vault_file_id' => $vaultFileId,
                'action'        => $action,
                'ip_address'    => request()->ip(),
                'user_agent'    => request()->userAgent()
            ]);
        }
    }

    public function vaultFile()
    {
        return $this->belongsTo(VaultFile::class, 'vault_file_id');
    }
}