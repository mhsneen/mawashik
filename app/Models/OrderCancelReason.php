<?php

namespace App\Models;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderCancelReason  extends Model
{
    use HasFactory;
    use Translatable, SoftDeletes;
    protected $with = ['translations'];
    protected $translatedAttributes = ['name'];
    protected $hidden=['translations'];
    protected $fillable = ['is_active'];

  public function getActive()
    {
        return $this->is_active == 0 ? __('dashboard.Inactive') : __('dashboard.effective');
    }


    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }

       public function orders()
    {
        return $this->hasMany(Order::class, 'cancel_reason_id');
    }

}
