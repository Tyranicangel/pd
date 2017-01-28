<?php

class Pdaccount extends Eloquent {

	protected $table = "pdaccountinfo";
	public $timestamps = false;

	protected $guarded = ["id"];

	public function usernames()
	{	
		return $this->belongsTo('Users','ddocode','username');
	}

	public function scheme()
	{
		return $this->belongsTo('Schemes','hoa','hoa');
	}

	public function arealist()
	{
		return $this->belongsTo('Areas','areacode','areacode');
	}
}