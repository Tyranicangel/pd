<?php
	
	class Loc extends Eloquent{

		protected $table="locrequest";
		public $timestamps = false;

		protected $guarded=['id'];

		public function requser()
		{	
			return $this->belongsTo('Users','requestuser','username');
		}

		public function schemes()
		{	
			return $this->belongsTo('Schemes','hoa','hoa');
		}

		public function accounts()
		{	
			return $this->belongsTo('Pdaccount','hoa','hoa');
		}
	}