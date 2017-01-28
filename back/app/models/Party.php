<?php
	
	class Party extends Eloquent{

		protected $table="partydetails";
		public $timestamps = false;

		protected $guarded=['id'];

		
	}