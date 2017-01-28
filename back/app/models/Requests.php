<?php
	
	class Requests extends Eloquent{

		protected $table="chequerequest";
		public $timestamps = false;

		protected $guarded=['id'];

		public function requser()
		{	
			return $this->belongsTo('Users','requestuser','username');
		}

		public function leafs()
		{
			return $this->hasMany('Leaves','user','requestuser');
		}

		public function bookdata()
		{
			return $this->hasOne('Cheques','requestid','id');
		}
	}