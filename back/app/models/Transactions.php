<?php
	
	class Transactions extends Eloquent{

		protected $table="transactions";
		public $timestamps = false;

		protected $guarded=['id'];
		
		public function requser()
		{	
			return $this->belongsTo('Users','issueuser','username');
		}

		public function laptrans()
		{	
			return $this->hasMany('Transactions','laprecid','id');
		}

		public function accountdet()
		{	
			return $this->belongsTo('Pdaccount','issueuser','ddocode');
		}

	}