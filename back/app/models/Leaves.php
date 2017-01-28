<?php
	
	class Leaves extends Eloquent{

		protected $table="cheques";
		public $timestamps = false;

		protected $guarded=['id'];
	}
