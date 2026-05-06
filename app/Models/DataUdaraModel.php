<?php

namespace App\Models;

use CodeIgniter\Model;

class DataUdaraModel extends Model
{
    protected $table = 'data_udara';
    protected $primaryKey = 'id';
    protected $allowedFields = ['suhu', 'kelembaban', 'pm1_0', 'pm2_5', 'pm10', 'polutan','latitude', 'longitude', 'altitude','satellites'];
}