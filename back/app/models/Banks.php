<?php
	
	class Banks extends Eloquent{

		protected $table="banklist";
		public $timestamps = false;

		protected $guarded=['id'];
	}	
