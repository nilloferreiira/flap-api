<?php

namespace App\Models\Client;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'clients';

    protected $fillable = [
        'id',
        'companyName',
        'cnpj',
        'address',
        'primaryContact',
        'phone',
        'email',
        'avatarUrl',
        'agentUrl',
    ];
}
