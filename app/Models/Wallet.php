<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $fillable = ['user_id', 'balance', 'rolename'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }



    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }




    public function ScopeFilterDashbord($query, array $filters)
    {



        $query->when($filters['search_mobile'] ?? false, function ($query, $search) {
            return $query->whereHas('user', function ($q) use ($search) {
                $q->where('mobile', 'LIKE', "%$search%");
            });
        });

        $query->when($filters['search_email'] ?? false, function ($query, $search) {
            return $query->whereHas('user', function ($q) use ($search) {
                $q->where('email', 'LIKE', "%$search%");
            });
        });

        $query->when($filters['search_client_name'] ?? false, function ($query, $search) {
            return $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'LIKE', "%$search%");
            });
        });
    }
}
