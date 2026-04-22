<?php

namespace App\Models;

use CodeIgniter\Model;

class DataUdaraModel extends Model
{
    protected $table = 'data_udara';
    protected $primaryKey = 'id';
    protected $allowedFields = ['pm25', 'pm10', 'suhu', 'kelembaban', 'created_at'];
}