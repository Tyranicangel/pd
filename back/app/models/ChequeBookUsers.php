<?php

class ChequeBookUsers extends Eloquent {

	protected $table = "chequebookusers";
	public $timestamps = false;

	protected $guarded = ["id"];
}