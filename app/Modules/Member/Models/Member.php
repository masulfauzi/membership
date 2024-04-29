<?php

namespace App\Modules\Member\Models;

use App\Helpers\UsesUuid;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Modules\User\Models\User;
use App\Modules\StatusMembership\Models\StatusMembership;


class Member extends Model
{
	use SoftDeletes;
	use UsesUuid;

	protected $casts      = ['deleted_at' => 'datetime', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
	protected $table      = 'member';
	protected $fillable   = ['*'];	

	public function user(){
		return $this->belongsTo(User::class,"id_user","id");
	}
public function statusmembership(){
		return $this->belongsTo(StatusMembership::class,"id_statusmembership","id");
	}

}
