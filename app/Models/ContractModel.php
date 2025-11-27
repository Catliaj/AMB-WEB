<?php

namespace App\Models;

use CodeIgniter\Model;

class ContractModel extends Model
{
    protected $table = 'contract';
    protected $primaryKey = 'contractID';
    protected $returnType = 'array';
    protected $allowedFields = ['bookingID','mode','monthly','proposedBy','confirmedBy','status','confirmed_at','created_at','updated_at'];
    protected $useTimestamps = false;
}
