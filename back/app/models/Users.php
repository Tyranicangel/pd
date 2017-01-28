<?php
	
	class Users extends Eloquent{

		protected $table="users";
		public $timestamps = false;

		protected $guarded=['id'];

		public function mappedto()
		{	
			return $this->belongsTo('Maptable','username','currentuser');
		}

		public function chequemap()
		{	
			return $this->belongsTo('ChequeBookUsers','username','sauser');
		}
		
	}