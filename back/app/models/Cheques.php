<?php
	
	class Cheques extends Eloquent{

		protected $table="chequelist";
		public $timestamps = false;

		protected $guarded=['id'];
	}
