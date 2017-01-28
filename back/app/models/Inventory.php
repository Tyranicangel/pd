<?php
	
	class Inventory extends Eloquent{

		protected $table="inventory";
		public $timestamps = false;

		protected $guarded=['id'];
	}