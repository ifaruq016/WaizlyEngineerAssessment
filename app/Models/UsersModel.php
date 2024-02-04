<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UsersModel extends Model
{
    use HasFactory;
    public $table = 'users';
    public $timestamps = true;
    protected $fillable = [
        'full_name',
        'email',
        'user_type',
        'status',
        'password'
    ];

    const USER_STATUS = [
        'ACTIVE' => 'ACTIVE',
        'INACTIVE' => 'INACTIVE'
    ];

    public function scopeGetUser($query, $email, $status=[]){
        $result = $query->where('email', $email);

        if(count($status) > 0) {
            $result = $result->whereIn('status', $status);
        };

        return $result->first();
    }

    public function scopeGetUserById($query, $id){
        $result = $query->where('id', $id)->first();

        return $result;
    }

    public function scopeGetLastUser($query){
        $result = $query->where('status', 'ACTIVE')
            ->where('email', '<>', 'me@admin.com')
            ->orderByDesc('id')->first();

        return $result;
    }

    public function scopeGetAllUser($query, $page, $sort, $search){
        $page_number = 1;
        if ($page) {
            $page_number = $page;
        }

        $result = $query->select('id', 'full_name', 'email', 'user_type', 'status');
        if ($search) {
            $result = $result->where('email', 'like', '%' . $search . '%')
                ->orWhere('full_name', 'like', '%' . $search . '%');
        }

        if ($sort) {
            if ($sort == 'ASC') {
                $result = $result->orderBy('id');
            } else {
                $result = $result->orderByDesc('id');
            }
        } else {
            $result = $result->orderByDesc('id');
        }

        $result = $result->paginate(10, ['*'], 'page', $page_number);

        return $result;
    }
}
