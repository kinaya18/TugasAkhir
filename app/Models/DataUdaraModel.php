<?php

namespace App\Models;

use CodeIgniter\Model;

class DataUdaraModel extends Model
{
    protected $table = 'data_udara';

    protected $primaryKey = 'id';

    protected $returnType = 'array';

    protected $allowedFields = [
        'temperature',
        'humidity',
        'pm1_0',
        'pm2_5',
        'pm10',
        'pollutant',
        'ozone',
        'no2',
        'timestamp'
    ];
}